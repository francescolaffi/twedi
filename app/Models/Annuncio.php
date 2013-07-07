<?php

namespace app\Models;

use lib\Data\Model;

class Annuncio extends Model {
	
	# nome tipo di item
	protected $item_type = 'annuncio';
	
	# nome tabella
	protected $table = 'annunci';
	
	# primary key
	protected $pkey = 'annuncio_id';
	
	# tutte le colonne della tabella a parte la pkey, i tipi possibili sono
	#s=stringa, i=intero, d=decimale; i bool sono interi e gli enum stringhe
	protected $cols_and_types = array(
		'data_pubblicazione'    => 's',
		'titolo_annuncio' => 's',
		'contenuto_annuncio' => 's',
		'categoria_annuncio'     => 's',
		'pubblicato_da_utente_id'  => 'i',
		'pubblicato_in_corso_id'  => 'i',
	);
}