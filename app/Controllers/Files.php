<?php

namespace app\Controllers;

use app\Models\File;

abstract class Files extends Generic {
	
	protected function output_file(File $file) {
		if (!is_file($file_path = $file->file_path()) || !($filesize = filesize($file_path))) {
			throw new \lib\Exceptions\NotFoundHttpException('File non valido');
		}
		
		$finfo = new \finfo;
		
		if ($content_type = $finfo->file($file_path)) {
			header("Content-type: $content_type");
		}
		
		header('Content-Disposition: attachment; filename="'.$file->nome_originale_file.'"');        
		header("Content-Length: $filesize");
		
		readfile($file_path);
		die;
	}
	
	/**
	 * Summary
	 * 
	 * @return \app\Models\File uploaded file on success
	 */
	protected function handle_upload()
	{
		if (empty($_FILES['file']) || !@is_file($_FILES['file']['tmp_name'])) {
			$this->session()->add_notice('error', 'Nessun file caricato, riprovare.', false);
			return false;
		} else {
			$phpfile = $_FILES['file'];
		}
		
		switch ($phpfile['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$this->session()->add_notice('error', 'File troppo grande (la dimensione massima dipende dalle impostazione del server).', false);
				return false;
			case UPLOAD_ERR_PARTIAL:
				$this->session()->add_notice('error', 'Il file è stato caricato solo parzialmente, riprovare.', false);
				return false;
			case UPLOAD_ERR_NO_FILE:
				$this->session()->add_notice('error', 'Nessun file caricato, riprovare.', false);
				return false;
			default:
				$this->session()->add_notice('error', "Errore del server nel gestire il file (errore {$phpfile['error']})", false);
				return false;
		}

		$file = $this->build_model('File');
		$file->nome_originale_file = $phpfile['name'];
		$file->hash_file = md5_file($phpfile['tmp_name']);
		
		$other_file = $this->build_model('File');
		if ($other_file->populate_hash($file->hash_file)) {
			$this->session()->add_notice('warning', 'Il file era già stato caricato in precedenza ed è raggiungibile tramite questa pagina.', true);
			$other_file = $this->template_item_from($other_file);
			$this->redirect($other_file->url_vedi());
			die();
		}

		$file_path = $file->file_path();
		
		if (!$this->mkdir(dirname($file_path)) || !rename($phpfile['tmp_name'], $file_path)) {
			@unlink($file_path);
			@unlink($phpfile['tmp_name']);
			$this->session()->add_notice('error', 'Errore del server nel gestire il file (impossibile spostare il file).', false);
			return false;
		}
		
		$file->data_caricamento = gmdate('Y-m-d H:i:s');
		$file->caricato_da_utente_id = $this->session()->user_id();
		
		return $file;
	}
	
	protected function handle_save(File $file = null, $corso_id = 0)
	{
		if ($file) {
			$this->check_nonce('modifica_file');
			$nuovo_file = false;
		} else {
			$this->check_nonce('inserisci_file');
			if (! $file = $this->handle_upload()) {
				return array('result' => false, 'errori_form' => array());
			}
			$nuovo_file = true;
		}
		
		$titolo = trim(filter_input(INPUT_POST, 'titolo'));
		if (!$titolo) {
			$errori_form['titolo']= 'Devi inserire il titolo del file';
		}

		$descrizione = trim(filter_input(INPUT_POST, 'descrizione'));

		$nuovo_nome_file = trim(filter_input(INPUT_POST, 'nome_file'));
		
		$nuovo_corso_id = filter_input(INPUT_POST, 'corso_id', FILTER_VALIDATE_INT);
		if ($nuovo_corso_id && $corso_id != $nuovo_corso_id && !$this->build_stmt()->table('corsi')->where('corso_id','=',$nuovo_corso_id,'i')->count()) {
			$errori_form['corso_id']= 'Selezionare un corso valido';
		}
		
		if(empty($errori_form)) {
			$file->titolo_file = $titolo;
			$file->descrizione_file = $descrizione;
			$file->caricato_in_corso_id = $nuovo_corso_id;
			if (!$nuovo_file && $nuovo_nome_file) {
				$file->nome_originale_file = $nuovo_nome_file;
			}
			
			$result = $file->save();
			
			if (!$result) {
				$this->session()->add_notice('error', 'Errore di salvataggio nel database', false);
				if ($nuovo_file) {
					@unlink($file->file_path());
					@$file->delete();
				}
			}
		} else {
			$result = false;
		}
		
		$file = $this->template_item_from($file);
		
		return compact('file','result','errori_form');
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

	protected function mkdir($target)
	{
		if ( file_exists( $target ) )
			return is_dir( $target );

		if ( @mkdir( $target, 0007777, true ) ) {
			$stat = @stat( dirname( $target ) );
			$dir_perms = $stat['mode'] & 0007777;  // Get the permission bits.
			@chmod( $target, $dir_perms );
			return true;
		}

		return false;
	}
}

