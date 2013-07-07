<?php

namespace app\Controllers;

class Annunci_Corso extends Annunci {
	
	public function singolo($id, $id2)
	{
		//controllare se esiste il corso
		$corso = $this->build_model('Corso');
		if (! $corso->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Corso non trovato' );
		}
		$corso = $this->template_item_from($corso);
		$annuncio=parent::fetch_singolo($id2);
		
		//controllare se l'id del corso corso_id è uguale alla proprietà pubblicato_in_corso_id
		if(!$corso->corso_id == $annuncio->pubblicato_in_corso_id){
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non corrispondente al corso' );
		}		
		
		//serve per chiamare il template item costruito in Annunci.php
		//controllo se l'annuncio sia effettivamente un annuncio di un corso
		//categoria_annuncio=='corso'
		if('corso'!=$annuncio->categoria_annuncio){
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}
		$autore = $this->build_model('Utente');
		if (!$autore->populate($annuncio->pubblicato_da_utente_id)) {
			throw new \lib\Exceptions\NotFoundHttpException('L\'annuncio non è valido.');
		}
		$autore = $this->template_item_from($autore);
		
		//nome del file nella cartella views
		$this->render('annunci_corso/singolo', compact('annuncio', 'autore','corso'));
		
	}
	
	public function inserisci($id)
	{
		//inserisci nuovo annuncio
		//1.	Controllo lutente e i suoi privilegi
		$this->check_logged_in('Devi essere loggato per inserire gli annnunci.');
		$this->check_is_admin();
		
		//2.	Controllare lesistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo lannuncio a un corso devi controllare che il corso esista)
		$corso = $this->build_model('Corso');
		if (! $corso->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Corso non trovato' );
		}
		
		$errori_form=array();
		//3.	Se viene attivata lazione allora fai:
		if (filter_has_var(INPUT_POST, 'submit')) {
			// $annunco $result $errori_form
			extract($this->gestisci_salvataggio('corso',$corso->corso_id));
			if ($result) {
				$this->session()->add_notice('success', "Annuncio {$annuncio->titolo_annuncio} inserito correttamente.", true);
				$annuncio = $this->template_item_from($annuncio);
				$this->redirect($annuncio->url_vedi());
			}else {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}
			
		//4.	Visualizzi la pagina del form con eventuali errori
		$opzioni_select_corsi=$this->opzioni_select_corsi();
		$this->render('annunci_corso/inserisci', compact('corso','annuncio', 'errori_form', 'opzioni_select_corsi'));
	}
	
	public function modifica($id, $id2)
	{
		//modifica singolo
		//1.	Controllo lutente e i suoi privilegi
		$this->check_logged_in('Devi essere loggato per modificare gli annnunci.');
		$this->check_is_admin();

		
		//2.	Controllare lesistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo lannuncio a un corso devi controllare che il corso esista)
		$corso = $this->build_model('Corso');
		if (! $corso->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Corso non trovato' );
		}
		$annuncio = $this->build_model('Annuncio');
		if (! $annuncio->populate($id2)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}
		$errori_form=array();
		//3.	Se viene attivata lazione allora fai:
		if (filter_has_var(INPUT_POST, 'submit')) {
			extract($this->gestisci_salvataggio('corso',$corso->corso_id,$annuncio));
			if ($result) {
				//siamo positivi: è andato tutto bene!
				$this->session()->add_notice('success',"Annuncio {$annuncio->titolo} modificato correttamente", true);
				$annuncio = $this->template_item_from($annuncio);
				$this->redirect($annuncio->url_vedi());
			}else {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}
		$annuncio = $this->template_item_from($annuncio);
		
		$opzioni_select_corsi=$this->opzioni_select_corsi();
		
		//4.	Visualizzi la pagina del form con eventuali errori
		$this->render('annunci_corso/modifica', compact('corso', 'annuncio', 'errori_form', 'opzioni_select_corsi'));
	}
	
	public function elimina($id, $id2)
	{
		$this->check_logged_in('Devi essere loggato per modificare gli annnunci.');
		$this->check_is_admin();//elimina singolo (no view)
		$this->check_nonce('elimina_annuncio');
		
		//1.	Controllo lutente e i suoi privilegi
		
		//2.	Controllare lesistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo lannuncio a un corso devi controllare che il corso esista)
		$corso = $this->build_model('Corso');
		if (! $corso->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Corso non trovato' );
		}
		$annuncio = $this->build_model('Annuncio');
		if (! $annuncio->populate($id2)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}
		if ($annuncio->delete()||!$annuncio->populate($id2)) {
			$this->session()->add_notice('success',"Annuncio eliminato correttamente",true);
			$this->redirect($this->url_for('corso', array('id' => $corso->corso_id)));
		} else {
			$this->session()->add_notice('error',"Impossibile eliminare l'annuncio, riprova.",true) ;
			$this->redirect($this->url_for('annuncio_corso', array('id' => $corso->corso_id, 'id2' => $annuncio->annuncio_id)));
		}
	}

}