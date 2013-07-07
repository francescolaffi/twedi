<?php

namespace app\Controllers;

class Annunci_Globali extends Annunci {
	
	public function index()
	{
		//creo una stmt per fare query sulla view degli annunci globali
		$stmt = $this->build_stmt()->table('annunci_tutti_corsi')->order_by('annuncio_id DESC');
		
		//vedo se e quanti annunci ci sono
		$tot_annunci_globali = $stmt->count();
				
		//se ce ne sono li seleziono e costruisco un loop
		if ($tot_annunci_globali) {
			$stmt->select();
			$annunci = $this->build_loop($stmt, 'Annuncio_Autore');
		} else {
			$annunci = array();
		}
		
		$this->render('annunci_globali/lista', compact('annunci', 'tot_annunci_globali'));
	}

	public function singolo($id)
	{
		//controllare se esiste l'annuncio
		$annuncio=parent::fetch_singolo($id);
		
		//se è di tipo corso		
		if('corso'!=$annuncio->categoria_annuncio){
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}

		//controllare se  pubblicato_in_corso_id  = 0
		if(!0 == $annuncio->pubblicato_in_corso_id){
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non globale' );
		}	
	
		//serve per chiamare il template item costruito in Annunci.php

		$autore = $this->build_model('Utente');
		if (!$autore->populate($annuncio->pubblicato_da_utente_id)) {
			throw new \lib\Exceptions\NotFoundHttpException('L\'annuncio non è valido.');
		}
		$autore = $this->template_item_from($autore);
		
		//nome del file nella cartella views
		$this->render('annunci_globali/singolo', compact('annuncio', 'autore'));

	}

	
	public function modifica($id)
	{
		//modifica singolo
		//1.	Controllo lutente e i suoi privilegi
		$this->check_logged_in('Devi essere loggato per modificare gli annnunci.');
		$this->check_is_admin();

		
		//2.	Controllare lesistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo lannuncio a un corso devi controllare che il corso esista)
		$annuncio = $this->build_model('Annuncio');
		if (! $annuncio->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}
		
		$errori_form=array();
		//3.	Se viene attivata lazione allora fai:
		if (filter_has_var(INPUT_POST, 'submit')) {
			extract($this->gestisci_salvataggio('corso',0,$annuncio));
			if ($result) {
				//siamo positivi: è andato tutto bene!
				$this->session()->add_notice('success',"Annuncio {$annuncio->titolo_annuncio} modificato correttamente", true);
				$annuncio = $this->template_item_from($annuncio);
				$this->redirect($annuncio->url_vedi());
			}else {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}
		$annuncio = $this->template_item_from($annuncio);
		
		$opzioni_select_corsi=$this->opzioni_select_corsi();
		
		//4.	Visualizzi la pagina del form con eventuali errori
		$this->render('annunci_globali/modifica', compact('annuncio', 'errori_form', 'opzioni_select_corsi'));
	}
	
	
	public function elimina($id)
		{
		$this->check_logged_in('Devi essere loggato per eliminare gli annnunci.');
		$this->check_is_admin();
		$this->check_nonce('elimina_annuncio');
			
		$annuncio = $this->build_model('Annuncio');
		if (! $annuncio->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}
		
		if ($annuncio->delete()||!$annuncio->populate($id)){
			$this->session()->add_notice('success', "Annuncio eliminato correttamente.", true);
			$this->redirect($this->url_for('annunci_globali', array()));
		}
		else{
			$this->session()->add_notice('error', "L\'Annuncio {$annuncio->titolo_annuncio} non è stato eliminato.", true);
			$this->redirect($this->url_for('annuncio_globale', array('id' => $annuncio->annuncio_id)));
		}
	}
	public function inserisci()
	{
		//inserisci nuovo annuncio
		//1.	Controllo lutente e i suoi privilegi
		$this->check_logged_in('Devi essere loggato per inserire gli annnunci.');
		$this->check_is_admin();
		
		//2.	Controllare lesistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo lannuncio a un corso devi controllare che il corso esista)
		
			
		$errori_form = array();
		//3.	Se viene attivata lazione allora fai:
		if (filter_has_var(INPUT_POST, 'submit')) {
			extract($this->gestisci_salvataggio('corso',0));
			if ($result) {
				//siamo positivi: è andato tutto bene!
				$this->session()->add_notice('success', 'Annuncio inserito correttamente.', true);
				$annuncio = $this->template_item_from($annuncio);
				$this->redirect($annuncio->url_vedi());
			} else {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}
		
		//4.	Visualizzi la pagina del form con eventuali errori
		$opzioni_select_corsi=$this->opzioni_select_corsi();
		$this->render('annunci_globali/inserisci', compact('errori_form','opzioni_select_corsi'));
	}

}