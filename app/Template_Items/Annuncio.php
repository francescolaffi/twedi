<?php

namespace app\Template_Items;

use lib\Templating\Template_Item;

class Annuncio extends Template_Item {
	
	# nome tipo di item
	protected $item_type = 'annuncio'; //si deve chiamare come l'item_type del modello

	public function contenuto_formattato()
	{
		return \lib\Utils\StringUtils::make_html_paragraphs(htmlspecialchars($this->values['contenuto_annuncio']));
	}
	
	public function url_vedi()
	{			// nome della route, 						nome del campo del db
		if('lavoro'==$this->values['categoria_annuncio']){
			return $this->url_for('annuncio_lavoro_slug', array('id' => $this->values['annuncio_id'], 'slug' => \lib\Utils\StringUtils::slugify($this->values['titolo_annuncio'])));
		}
		elseif(0 == $this->values['pubblicato_in_corso_id']){
			return $this->url_for('annuncio_globale', array('id' => $this->values['annuncio_id']));
		}
		else{
			return $this->url_for('annuncio_corso', array('id'=>$this->values['pubblicato_in_corso_id'],'id2' => $this->values['annuncio_id']));
		}
	}
	
	public function url_modifica()
	{						// nome della route, 						nome del campo del db
		if('lavoro'==$this->values['categoria_annuncio']){
			return $this->url_for('annuncio_lavoro', array('id' => $this->values['annuncio_id'], 'action'=> 'modifica'));
		}
		elseif(0==$this->values['pubblicato_in_corso_id']){
			return $this->url_for('annuncio_globale', array('id' => $this->values['annuncio_id'], 'action'=> 'modifica'));
		}
		else{
			return $this->url_for('annuncio_corso', array('id'=>$this->values['pubblicato_in_corso_id'],'id2' => $this->values['annuncio_id'], 'action'=> 'modifica'));
		}
	}
	
	public function url_elimina()
	{
		if('lavoro'==$this->values['categoria_annuncio']){
			return $this->nonced_url('annuncio_lavoro', array('id' => $this->values['annuncio_id'], 'action'=> 'elimina'), 'elimina_annuncio');
		}
		elseif(0==$this->values['pubblicato_in_corso_id']){
			return $this->nonced_url('annuncio_globale', array('id' => $this->values['annuncio_id'], 'action'=> 'elimina'), 'elimina_annuncio');
		}
		else{
			return $this->nonced_url('annuncio_corso', array('id' => $this->values['pubblicato_in_corso_id'], 'id2' => $this->values['annuncio_id'], 'action'=> 'elimina'), 'elimina_annuncio');
		}
	}
	
	public function data_pubblicazione($format = null, $timezone = null){
		return $this->format_date('data_pubblicazione', $format, $timezone);
	}
}
