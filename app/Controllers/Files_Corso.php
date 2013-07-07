<?php

namespace app\Controllers;

class Files_Corso extends Files {

	public function singolo($id, $id2)
	{
		$corso = $this->populate_model('Corso', $id, 'Corso non trovato');
		$file = $this->populate_model('File', $id2, 'File non trovato');

		if($corso->corso_id !== $file->caricato_in_corso_id){
			throw new \lib\Exceptions\NotFoundHttpException('File non trovato');
		}

		$autore = $this->populate_model('Utente', $file->caricato_da_utente_id, 'File non valido');

		$this->render('files/singolo', compact('file', 'corso', 'autore'));
	}
	
	public function download($id, $id2)
	{
		$this->check_logged_in('Devi eseguire l\'accesso per scaricare i files.');
		
		$corso = $this->populate_model('Corso', $id, 'Corso non trovato');
		$file = $this->populate_model('File', $id2, 'File non trovato');
		
		if ($corso->corso_id !== $file->caricato_in_corso_id) {
			throw new \lib\Exceptions\NotFoundHttpException('File non trovato');
		}
		
		$this->output_file($file);
	}
	
	public function inserisci($id)
	{
		$this->check_logged_in('Devi eseguire l\'accesso per inserire i files.');
		$this->check_is_admin();
		
		$corso = $this->populate_model('Corso', $id, 'Corso non trovato');

		$errori_form = array();
		if (filter_input(INPUT_POST, 'submit')) {
			extract($this->handle_save(null, $corso->corso_id));
			
			if ($result) {
				$this->session()->add_notice('success', 'File inserito correttamente', true);
				$this->redirect($file->url_vedi());
			} elseif(count($errori_form)) {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}
		
		$opzioni_select_corsi = $this->opzioni_select_corsi();
		
		$this->render('files/inserisci', compact('corso','errori_form','opzioni_select_corsi'));
	}
	
	public function modifica($id, $id2)
	{
		$this->check_logged_in('Devi eseguire l\'accesso per modificare i files.');
		$this->check_is_admin();
		
		$corso = $this->populate_model('Corso', $id, 'Corso non trovato');
		$file = $this->populate_model('File', $id2, 'File non trovato');

		$errori_form = array();
		if (filter_input(INPUT_POST, 'submit')) {
			extract($this->handle_save($file, $corso->corso_id));

			if ($result) {
				$this->session()->add_notice('success', 'File modificato correttamente', true);
				$this->redirect($file->url_vedi());
			} else {
				$this->session()->add_notice('warning', "Alcuni campi sono da correggere!", false);
			}
		}

		$opzioni_select_corsi = $this->opzioni_select_corsi();

		$this->render('files/modifica', compact('corso', 'file', 'errori_form','opzioni_select_corsi'));
	}
	
	public function elimina($id, $id2)
	{
		$this->check_logged_in('Devi eseguire l\'accesso per eliminare i files.');
		$this->check_is_admin();
		
		$corso = $this->populate_model('Corso', $id, 'Corso non trovato');
		$file = $this->populate_model('File', $id2, 'File non trovato');

		$this->check_nonce('elimina_file');

		$file_path = $file->file_path();

		if ($file->delete() || !$file->populate($id2)) {
			$this->session()->add_notice('success', 'File eliminato correttamente', true);
			@unlink($file_path);
			$this->redirect($this->url_for('corso', array('id' => $id)));
		} else {
			$this->session()->add_notice('error', 'Si è verificato un errore nell\'eliminare il file', false);
			$this->redirect($this->url_for('file_corso', array('id' => $id, 'id2' => $id2)));
		}
	}
}