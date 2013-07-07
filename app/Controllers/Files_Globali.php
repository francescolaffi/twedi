<?php

namespace app\Controllers;

class Files_Globali extends Files {

	public function index()
	{
		$stmt_files = $this->build_stmt()->table('files_autori')->where('caricato_in_corso_id','=',0,'i')->order_by('file_id DESC');
		if ($tot_files = $stmt_files->count()) {
			$stmt_files->select();
			$files = $this->build_loop($stmt_files, 'File_Autore');
		} else {
			$files = array();
		}

		$this->render('files/lista', compact('tot_files', 'files'));
	}

	public function singolo($id)
	{
		$file = $this->populate_model('File', $id, 'File non trovato');

		$autore = $this->populate_model('Utente', $file->caricato_da_utente_id, 'File non valido');

		$this->render('files/singolo', compact('file', 'autore'));
	}
	
	public function download($id)
	{
		$this->check_logged_in('Devi eseguire l\'accesso per scaricare i files.');
		
		$file = $this->populate_model('File', $id, 'File non trovato');
		
		$this->output_file($file);
	}
	
	public function inserisci()
	{
		$this->check_logged_in('Devi eseguire l\'accesso per inserire i files.');
		$this->check_is_admin();

		$errori_form = array();
		if (filter_input(INPUT_POST, 'submit')) {
			extract($this->handle_save());
			
			if ($result) {
				$this->session()->add_notice('success', 'File inserito correttamente', true);
				$this->redirect($file->url_vedi());
			} elseif(count($errori_form)) {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}
		
		$opzioni_select_corsi = $this->opzioni_select_corsi();
		
		$this->render('files/inserisci', compact('errori_form','opzioni_select_corsi'));
	}
	
	public function modifica($id)
	{
		$this->check_logged_in('Devi eseguire l\'accesso per modificare i files.');
		$this->check_is_admin();

		$file = $this->populate_model('File', $id, 'File non trovato');

		$errori_form = array();
		if (filter_input(INPUT_POST, 'submit')) {
			extract($this->handle_save($file));

			if ($result) {
				$this->session()->add_notice('success', 'File modificato correttamente', true);
				$this->redirect($file->url_vedi());
			} else {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}

		$opzioni_select_corsi = $this->opzioni_select_corsi();

		$this->render('files/modifica', compact('file', 'errori_form','opzioni_select_corsi'));
	}
	
	public function elimina($id)
	{
		$this->check_logged_in('Devi eseguire l\'accesso per eliminare i files.');
		$this->check_is_admin();
		
		$file = $this->populate_model('File', $id, 'File non trovato');

		$this->check_nonce('elimina_file');

		$file_path = $file->file_path();

		if ($file->delete() || !$file->populate($id)) {
			$this->session()->add_notice('success', 'File eliminato correttamente', true);
			@unlink($file_path);
			$this->redirect($this->url_for('files_globali'));
		} else {
			$this->session()->add_notice('error', 'Si è verificato un errore nell\'eliminare il file', false);
			$this->redirect($this->url_for('file_globali', array('id' => $id)));
		}
	}
}