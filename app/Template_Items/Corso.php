<?php

namespace app\Template_Items;

use lib\Templating\Template_Item;

class Corso extends Template_Item {
	
	# nome tipo di item
	protected $item_type = 'corso';
	
	public function url_vedi()
	{
		return $this->url_for('corso', array('id' => $this->values['corso_id']));
	}
	
	public function url_modifica()
	{
		return $this->url_for('corso', array('id' => $this->values['corso_id'], 'action'=> 'modifica'));
	}
	
	public function url_elimina()
	{
		return $this->url_for('corso', array('id' => $this->values['corso_id'], 'action'=> 'elimina'));
	}
	
	public function url_inserisci_annuncio()
	{
		return $this->url_for('annunci_corso', array('id' => $this->values['corso_id'], 'action'=> 'inserisci'));
	}
	
	public function url_inserisci_file()
	{
		return $this->url_for('files_corso', array('id' => $this->values['corso_id'], 'action'=> 'inserisci'));
	}
}