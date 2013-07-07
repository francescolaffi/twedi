<?php

namespace app\Models;

use lib\Data\Model;

class Utente extends Model {
	
	# nome tipo di item
	protected $item_type = 'utente';
	
	# nome tabella
	protected $table = 'utenti';
	
	# primary key
	protected $pkey = 'utente_id';
	
	# tutte le colonne della tabella a parte la pkey, i tipi possibili sono
	#s=stringa, i=intero, d=decimale; i bool sono interi e gli enum stringhe
	protected $cols_and_types = array(
		'email'    => 's',
		'password' => 's',
		'pwd_salt' => 's',
		'nome'     => 's',
		'cognome'  => 's',
		'cellulare'  => 's',
		'occupazione'  => 's',
		'attinenza_lavoro'  => 's',
		'nome_azienda'  => 's',
		'posizione'  => 's',
		'carriera'  => 's',
		'anno_nascita'  => 'i',
		'sito_internet'  => 's',
		'iscrizione_corso_id'  => 'i',
		'is_admin'  => 'i',
		'is_active'  => 'i',
		'avviso_annunci_corso'  => 'i',
		'avviso_annunci_lavoro'  => 'i',
	);
}