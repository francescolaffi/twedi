<?php

namespace app\Controllers;

class Sitemap extends Generic {

	public function index(){

		//creo una stmt per fare query sulla view degli annunci di lavoro
		$stmt = $this->build_stmt()->table('annunci')->where('categoria_annuncio', '=','lavoro', 's')->order_by('annuncio_id DESC')->limit(0,20);
		
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
		$stmt = $this->build_stmt()->table('annunci')->where('categoria_annuncio', '=','corso', 's')->order_by('annuncio_id DESC')->limit(0,20);
		
		//vedo se e quanti annunci ci sono
		$tot_annunci_corsi = $stmt->count();
		
		//se ce ne sono li seleziono e costruisco un loop
		if ($tot_annunci_corsi) {
			$stmt->select();
			$annunci_c = $this->build_loop($stmt, 'Annuncio');
		} else {
			$annunci_c = array();
		}
				//creo una stmt per fare query sulla tabella dei corsi
		$stmt = $this->build_stmt()->table('corsi')->order_by('corso_id DESC')->limit(0,20);
		
		//vedo se e quanti corsi ci sono
		$tot_corsi = $stmt->count();
		
		//se ce ne sono li seleziono e costruisco un loop
		if ($tot_corsi) {
			$stmt->select();
			$corsi = $this->build_loop($stmt, 'Corso');
		} else {
			$corsi = array();
		}
		//FILES
		$stmt_files = $this->build_stmt()->table('files')->order_by('file_id DESC')->limit(0,20);
		if ($tot_files = $stmt_files->count()) {
			$stmt_files->select();
			$files = $this->build_loop($stmt_files, 'File');
		} else {
			$files = array();
		}

		$this->render('sitemap', compact('annunci', 'tot_annunci_lavoro', 'annunci_c','tot_annunci_corsi','corsi', 'tot_corsi','tot_files','files'));
		
	}

	public function dashboard(){
		
	}
	
}