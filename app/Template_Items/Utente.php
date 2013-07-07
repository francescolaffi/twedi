<?php

namespace app\Template_Items;

use lib\Templating\Template_Item;

class Utente extends Template_Item {
	
	# nome tipo di item
	protected $item_type = 'utente';
	
	public function url_vedi()
	{
		//primo parametro è il nome della route o una stringa nel formato Controller/action
		//il secondo paramtro è un array associativo con i dati necessari per creare l'url
		return $this->url_for('utente', array('id' => $this->values['utente_id']));
	}

	public function url_modifica()
	{
		return $this->url_for('amministrazione_utente', array('id' => $this->values['utente_id'], 'action'=> 'modifica-profilo'));
	}

	public function url_impostazioni()
	{
		return $this->url_for('amministrazione_utente', array('id' => $this->values['utente_id'], 'action'=> 'impostazioni-account'));
	}

	public function url_elimina()
	{
		return $this->url_for('amministrazione_utente', array('id' => $this->values['utente_id'], 'action'=> 'elimina-account'));
	}
}