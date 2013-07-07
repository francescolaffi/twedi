<?php

namespace app\Controllers;

class Annunci_Lavoro extends Annunci {
	
	public function index()
	{
			//creo una stmt per fare query sulla view degli annunci di lavoro
		$stmt = $this->build_stmt()->table('annunci_lavoro')->order_by('annuncio_id DESC');
		
		//vedo se e quanti annunci ci sono
		$tot_annunci_lavoro = $stmt->count();
		
		//se ce ne sono li seleziono e costruisco un loop
		if ($tot_annunci_lavoro) {
			$stmt->select();
			$annunci = $this->build_loop($stmt, 'Annuncio_Autore');
		} else {
			$annunci = array();
		}
		
		$this->render('annunci_lavoro/lista', compact('annunci', 'tot_annunci_lavoro'));
	}
	
	public function singolo($id)
	{
		$this->check_logged_in('Devi essere loggato per leggere gli annnunci.');
		$annuncio=parent::fetch_singolo($id); //serve per chiamare il template item costruito in Annunci.php
		//controllo se l'annuncio sia effettivamente un annuncio di lavoro
		//categoria_annuncio=='lavoro'
		if('lavoro'!=$annuncio->categoria_annuncio){
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}
		$autore = $this->build_model('Utente');
		if (!$autore->populate($annuncio->pubblicato_da_utente_id)) {
			throw new \lib\Exceptions\NotFoundHttpException('L\'annuncio non è valido.');
		}
		$autore = $this->template_item_from($autore);
		
		$this->render('annunci_lavoro/singolo', compact('annuncio', 'autore'));		
	}
	
	public function modifica($id)
	{
		//modifica singolo
		//1.	Controllo lutente e i suoi privilegi
		$this->check_logged_in('Devi essere loggato per modificare gli annnunci.');
		
		//2.	Controllare lesistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo lannuncio a un corso devi controllare che il corso esista)
		$annuncio = $this->build_model('Annuncio');
		if (! $annuncio->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}
		$autore = $this->build_model('Utente');
		if (!$autore->populate($annuncio->pubblicato_da_utente_id)) {
			throw new \lib\Exceptions\NotFoundHttpException('L\'annuncio non è valido.');
		}
		if ($this->session()->user_id() !== $autore->utente_id && !$this->session()->is_admin()) {
			throw new \lib\Exceptions\InsufficientPermissionsException('Devi essere l\'autore dell\'annuncio per modificarlo');
		} 
		
		$errori_form=array();
		//3.	Se viene attivata lazione allora fai:
		if (filter_has_var(INPUT_POST, 'submit')) {
			extract($this->gestisci_salvataggio('lavoro',0,$annuncio));
			if ($result) {
				//siamo positivi: è andato tutto bene!
				$this->session()->add_notice('success',"Annuncio {$annuncio->titolo} modificato correttamente", true);
				$annuncio = $this->template_item_from($annuncio);
				$this->redirect($annuncio->url_vedi());
			}else {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}
		
		//4.	Visualizzi la pagina del form con eventuali errori
		$this->render('annunci_lavoro/modifica', compact('annuncio', 'errori_form'));
	}
	
	public function elimina($id)
	{
		$this->check_logged_in('Devi essere loggato per eliminare gli annnunci.');
		$this->check_nonce('elimina_annuncio');
			
		$annuncio = $this->build_model('Annuncio');
		if (! $annuncio->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException( 'Annuncio non trovato' );
		}
		
		if (!$this->session()->is_admin()){
			$autore = $this->build_model('Utente');
			if (!$autore->populate($annuncio->pubblicato_da_utente_id)) {
				throw new \lib\Exceptions\NotFoundHttpException('L\'annuncio non è valido.');
			}
			if ($this->session()->user_id() !== $autore->utente_id) {
				throw new \lib\Exceptions\InsufficientPermissionsException('Devi essere l\'autore dell\'annuncio per eliminarlo');
			} 
		}
		if ($annuncio->delete()||!$annuncio->populate($id)){
			$this->session()->add_notice('success', "Annuncio eliminato correttamente.", true);
			$this->redirect($this->url_for('annunci_lavoro', array()));
		}
		else{
			$this->session()->add_notice('error', "L\'Annuncio {$annuncio->titolo_annuncio} non è stato eliminato.", true);
			$this->redirect($this->url_for('annuncio_lavoro', array('id' => $annuncio->annuncio_id)));
		}
	}
	public function inserisci()
	{
		//inserisci nuovo annuncio
		//1.	Controllo lutente e i suoi privilegi
		$this->check_logged_in('Devi essere loggato per inserire gli annnunci.');
		
		//2.	Controllare lesistenza e la validità degli elementi coinvolti
		//(se stai aggiungendo lannuncio a un corso devi controllare che il corso esista)
		$errori_form=array();

		//3.	Se viene attivata lazione allora fai:
		if (filter_has_var(INPUT_POST, 'submit')) {
			extract($this->gestisci_salvataggio('lavoro',0));
			if ($result) {
				//siamo positivi: è andato tutto bene!
				$this->session()->add_notice('success', "Annuncio {$annuncio->titolo_annuncio} inserito correttamente.", true);
				$this->redirect($this->url_for('annuncio_lavoro', array('id' => $annuncio->annuncio_id)));
			} else {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}
		
		//4.	Visualizzi la pagina del form con eventuali errori
			
		$this->render('annunci_lavoro/inserisci', compact('annuncio', 'errori_form'));
	}

}