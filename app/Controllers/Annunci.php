<?php

namespace app\Controllers;

abstract class Annunci extends Generic {
	
	protected function gestisci_salvataggio($tipologia,$corso_id,$annuncio=null){
		//a.	Controllo il nonce  vedo se è valido (uguale per categoria di azione)
		$azione_nonce = is_null($annuncio) ? 'inserisci_annuncio' : 'modifica_annuncio';
		$this->check_nonce($azione_nonce);
		
		//b.	Validazione dei dati e generazione di un eventuale array (associativo) di errori:
		//(keys: nomi campi, values: stringa errori)
		$errori_form=array();
		
		if (!$titolo = trim(filter_input(INPUT_POST, 'titolo'))) {
			$errori_form['titolo']= 'Devi inserire il titolo';
		}
		if (!$contenuto = trim(filter_input(INPUT_POST, 'contenuto'))) {
			$errori_form['contenuto']= 'Devi inserire il contenuto dell\'annuncio';
		}
		
		$nuovo_corso_id = filter_input(INPUT_POST, 'corso_id', FILTER_VALIDATE_INT);
		if ($nuovo_corso_id && $corso_id != $nuovo_corso_id && !$this->build_stmt()->table('corsi')->where('corso_id','=',$nuovo_corso_id,'i')->count()) {
			$errori_form['corso_id']= 'Selezionare un corso valido';
		}
		
		$result = false;
		
		//c.	Se non ci sono errori: fare lazione e rimandare lutente a una pagina appropriata
		if(empty($errori_form)) {
			if(is_null($annuncio)) {
				$annuncio = $this->build_model('Annuncio');
				$annuncio->data_pubblicazione = gmdate('Y-m-d H:i:s');
				$annuncio->pubblicato_da_utente_id = $this->session()->user_id();
				$annuncio->categoria_annuncio = $tipologia;
			}
			$annuncio->pubblicato_in_corso_id = $nuovo_corso_id;
			$annuncio->titolo_annuncio = $titolo;
			$annuncio->contenuto_annuncio = $contenuto;
			
			$result = $annuncio->save();
			if (!$result) {
				$this->session()->add_notice('error', 'Errore di salvataggio nel database', false);
			}
		}
		
		return compact('annuncio','result','errori_form');
	}
	protected function fetch_singolo($id)
	{
		$annuncio = $this->build_model('Annuncio');
		if (!$annuncio->populate($id)) {
			throw new \lib\Exceptions\NotFoundHttpException('Annuncio non trovato.');
		}
		
		$annuncio = $this->template_item_from($annuncio);
		
	return $annuncio;
	}
	protected function opzioni_select_corsi()
	{
		$opzioni_select_corsi = array('0' => 'Tutti i corsi');
		$stmt = $this->build_stmt()->table('corsi')->order_by('corso_id DESC');
		$stmt->select();
		$stmt->bind_assoc($corso_row);
		while ($stmt->fetch()) {
			$opzioni_select_corsi[$corso_row['corso_id']] = $corso_row['nome_corso'];
		}
		return $opzioni_select_corsi;
	}
}

