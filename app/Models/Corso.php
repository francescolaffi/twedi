<?php

namespace app\Models;

use lib\Data\Model;

class Corso extends Model {
	
	# nome tipo di item
	protected $item_type = 'corso';
	
	# nome tabella
	protected $table = 'corsi';
	
	# primary key
	protected $pkey = 'corso_id';
	
	# tutte le colonne della tabella a parte la pkey, i tipi possibili sono
	#s=stringa, i=intero, d=decimale; i bool sono interi e gli enum stringhe
	protected $cols_and_types = array(
		'nome_corso'    => 's',
	);
}