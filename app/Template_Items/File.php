<?php

namespace app\Template_Items;

use lib\Templating\Template_Item;

class File extends Template_Item {
	
	protected $item_type = 'file';

	public function descrizione_formattata()
	{
		return \lib\Utils\StringUtils::make_html_paragraphs(htmlspecialchars($this->values['descrizione_file']));
	}
	
	private function file_url($action = 'singolo') {
		if (0 == $this->values['caricato_in_corso_id']) {
			return $this->url_for('file_globale', array('id' => $this->values['file_id'], 'action' => $action));
		} else {
			return $this->url_for('file_corso', array('id' => $this->values['caricato_in_corso_id'], 'id2' => $this->values['file_id'], 'action' => $action));
		}
	}
	
	public function url_vedi()
	{
		return $this->file_url();
	}
	
	public function url_download()
	{
		return $this->file_url('download');
	}
	
	public function url_modifica()
	{
		return $this->file_url('modifica');
	}
	
	public function url_elimina()
	{
		if (0 == $this->values['caricato_in_corso_id']) {
			return $this->nonced_url('file_globale', array('id' => $this->values['file_id'], 'action' => 'elimina'), 'elimina_file');
		} else {
			return $this->nonced_url('file_corso', array('id' => $this->values['caricato_in_corso_id'], 'id2' => $this->values['file_id'], 'action' => 'elimina'), 'elimina_file');
		}
	}
	
	public function data_caricamento($format = null, $timezone = null){
		return $this->format_date('data_caricamento', $format, $timezone);
	}
}