<?php

namespace app\Controllers;

class Utenti extends Generic {
	
	public function index() {
		$this->check_logged_in('Devi essere loggato per vedere la lista degli utenti.');
		//creo una stmt per fare query sulla tabella degli utenti
		$stmt = $this->build_stmt()->table('utenti')->order_by('cognome ASC, nome ASC');
		
		//vedo se e quanti corsi ci sono
		$tot_utenti = $stmt->count();
		
		//se ce ne sono li seleziono e costruisco un loop
		if ($tot_utenti) {
			$stmt->select();
			$utenti = $this->build_loop($stmt, 'Utente');
		} else {
			$utenti = array();
		}
		
		$this->render('utenti/lista', compact('utenti', 'tot_utenti'));
	}
	
	public function singolo($id) {
		$this->check_logged_in('Devi accedere per vedere i profili degli utenti.');
		
		$utente = $this->build_model( 'Utente' );

		if ($this->session()->user_id() === $id) {
			$this->redirect($this->url_for('account'));
		}
		
		if ( ! $utente->populate($id) )
			throw new \lib\Exceptions\NotFoundHttpException( 'Utente non trovato' );
		
		$utente = $this->template_item_from($utente);
		
		$corso = $this->build_model('Corso');		
		$corso->populate($utente->iscrizione_corso_id);
		$corso = $this->template_item_from($corso);

		
		// il primo parametro è il percorso alla view relativo a webroot/app/views/
		// il secondo parametro è un array associativo, i cui valori diventivano disponibili nella view in variabili nominate come la chiave corrispondente
		$this->render( 'utenti/singolo', compact('utente', 'corso'));
	}
}