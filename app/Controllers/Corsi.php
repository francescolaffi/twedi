<?php

namespace app\Controllers;

class Corsi extends Generic {
	
	public function index()
	{
		//creo una stmt per fare query sulla tabella dei corsi
		$stmt = $this->build_stmt()->table('corsi')->order_by('corso_id DESC');
		
		//vedo se e quanti corsi ci sono
		$tot_corsi = $stmt->count();
		
		//se ce ne sono li seleziono e costruisco un loop
		if ($tot_corsi) {
			$stmt->select();
			$corsi = $this->build_loop($stmt, 'Corso');
		} else {
			$corsi = array();
		}
		
		$this->render('corsi/lista', compact('corsi', 'tot_corsi'));
	}
	
	public function singolo($id)
	{
		$corso = $this->build_model('Corso');
		if (!$corso->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException('Corso non trovato.');
		}
		
		$stmt_annunci = $this->build_stmt()->table('annunci_corso')->where('pubblicato_in_corso_id','IN',array(0,$corso->corso_id),'i')->order_by('annuncio_id DESC');
		if ($tot_annunci = $stmt_annunci->count()) {
			$stmt_annunci->select();
			$annunci = $this->build_loop($stmt_annunci, 'Annuncio_Autore');
		} else {
			$annunci = array();
		}
		
		$stmt_files = $this->build_stmt()->table('files_autori')->where('caricato_in_corso_id','=',$corso->corso_id,'i')->order_by('file_id DESC');
		if ($tot_files = $stmt_files->count()) {
			$stmt_files->select();
			$files = $this->build_loop($stmt_files, 'File_Autore');
		} else {
			$files = array();
		}
		
		if ($this->session()->is_logged_in()) {
			$stmt_studenti = $this->build_stmt()->table('utenti')->where('iscrizione_corso_id','=',$corso->corso_id,'i')->where('is_admin','=',0,'i')->where('is_active','=',1,'i');
			if ($tot_studenti = $stmt_studenti->count()) {
				$stmt_studenti->select();
				$studenti = $this->build_loop($stmt_studenti, 'Utente');
			} else {
				$studenti = array();
			}
		} else {
			$tot_studenti = 0;
			$studenti = array();
		}
		
		$corso = $this->template_item_from($corso);
		
		$this->render('corsi/singolo', compact('corso', 'tot_annunci', 'annunci', 'tot_files', 'files', 'tot_studenti', 'studenti'));
	}
	
	public function modifica($id)
	{
		//1.	Controllo lutente e i suoi privilegi
		$this->check_logged_in('Devi essere loggato per modificare il corso.');
		$this->check_is_admin();

		//2.	Controllare lesistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo l'annuncio a un corso devi controllare che il corso esista)
		$corso = $this->build_model('Corso');
		if (! $corso->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Corso non trovato' );
		}
		$errori_form=array();
		//3.	Se viene attivata l'azione allora fai:
		if (filter_input(INPUT_POST, 'submit')) {
			//a.	Controllo il nonce – vedo se è valido (uguale per categoria di azione)
			$this->check_nonce('modifica_corso');
			
			if (empty($_POST['nome_corso'])) {
				$errori_form['nome_corso']= 'Devi inserire il nome del corso';
			} elseif($this->injector->DB_STMT()->table('corsi')->where('nome_corso', '=', $_POST['nome_corso'], 's')->count()) {
				$errori_form['nome_corso']= 'Corso già esistente';
			}
			
			if(empty($errori)) {
				$corso->nome_corso = $_POST['nome_corso'];
				
				if($corso->save()) {
					//siamo positivi: è andato tutto bene!
					$this->session()->add_notice('success', "Corso modificato correttamente.", true);
					$this->redirect($this->url_for('corso', array('id' => $corso->corso_id )));
				} else {
					//forse non è andato tutto bene
					$this->session()->add_notice('error',"Errore di salvataggio nel database. Riprova!", false);
				}
			}
		}
			//4.	Visualizzi la pagina del form con eventuali errori
			$this->render('corsi/modifica', compact('corso', 'errori_form'));
	}
	
	public function elimina($id)
	{
		$corso = $this->populate_model('Corso', $id);

		$this->render('corsi/elimina', compact('corso'));
	}
	public function inserisci()
	{
		//inserisci nuovo corso
		//1.	Controllo l’utente e i suoi privilegi
		$this->check_logged_in('Devi essere loggato per inserire gli annnunci.');
		$this->check_is_admin();
		
		//2.	Controllare l’esistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo l’annuncio a un corso devi controllare che il corso esista…)
		//--> non ce ne sono
			
		//3.	Se viene attivata l’azione allora fai:
		$errori=array();
		if (!empty($_POST['submit'])) {
			//a.	Controllo il nonce – vedo se è valido (uguale per categoria di azione)
			$this->check_nonce('inserisci_corso');
			
			//b.	Validazione dei dati e generazione di un eventuale array (associativo) di errori:
			//(keys: nomi campi, values: stringa errori)
			
				
			if (empty($_POST['nome_corso'])) {
				$errori['nome_corso']= 'Devi inserire il nome del corso';
			} elseif($this->injector->DB_STMT()->table('corsi')->where('nome_corso', '=', $_POST['nome_corso'], 's')->count()) {
				$errori['nome_corso']= 'Corso già esistente';
			}
			
			
			//c.	Se non ci sono errori: fare l’azione e rimandare l’utente a una pagina appropriata
			if(empty($errori)) {
				$corso = $this->build_model( 'Corso' );
				$corso->nome_corso = $_POST['nome_corso'];
				
				if($corso->save()) {
					//siamo positivi: è andato tutto bene!
					$this->session()->add_notice('success', "Corso {$corso->nome_corso} inserito correttamente.", true);
					$this->redirect($this->url_for('corso', array('id' => $corso->corso_id )));
				} else {
					//forse non è andato tutto bene
					$this->session()->add_notice('error',"Errore di salvataggio nel database. Riprova!", false);
				}
			}
		}
		//4.	Visualizzi la pagina del form con eventuali errori
		$slots = array(
			'errori_form' => $errori,
		);
		$this->render( 'corsi/inserisci', $slots);

	}

}