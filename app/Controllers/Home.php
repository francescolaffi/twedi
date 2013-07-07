<?php

namespace app\Controllers;

class Home extends Generic {

	public function index(){

		
		//creo una stmt per fare query sulla view degli annunci di lavoro
		$stmt = $this->build_stmt()->table('annunci')->where('categoria_annuncio', '=','lavoro', 's')->order_by('annuncio_id DESC')->limit(0,3);
		
		//vedo se e quanti annunci ci sono
		$tot_annunci_lavoro = $stmt->count();
		
		//se ce ne sono li seleziono e costruisco un loop
		if ($tot_annunci_lavoro) {
			$stmt->select();
			$annunci = $this->build_loop($stmt, 'Annuncio');
		} else {
			$annunci = array();
		}
		
		
				//creo una stmt per fare query sulla view degli annunci dei corsi e globali
		$stmt = $this->build_stmt()->table('annunci')->where('categoria_annuncio', '=','corso', 's')->order_by('annuncio_id DESC')->limit(0,3);
		
		//vedo se e quanti annunci ci sono
		$tot_annunci_corsi = $stmt->count();
		
		//se ce ne sono li seleziono e costruisco un loop
		if ($tot_annunci_corsi) {
			$stmt->select();
			$annunci_c = $this->build_loop($stmt, 'Annuncio');
		} else {
			$annunci_c = array();
		}
		
		$this->render('home', compact('annunci', 'tot_annunci_lavoro', 'annunci_c','tot_annunci_corsi'));
		
		
		
		
	}
	
}