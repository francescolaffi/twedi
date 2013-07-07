<?php

namespace app\Models;

use lib\Data\Model_Hash;

class Account_Token extends Model_Hash {
	
	const AZN_CONFERMA_REGISTRAZIONE = 'conferma-registrazione';
	const AZN_CAMBIO_EMAIL = 'cambio-email';
	const AZN_NUOVA_PASSWORD = 'nuova-password';
	
	# nome tipo di item
	protected $item_type = 'account_token';
	
	# nome tabella
	protected $table = 'account_tokens';
	
	# primary key
	protected $pkey = 'token';
	
	# tutte le colonne della tabella a parte la pkey, i tipi possibili sono
	#s=stringa, i=intero, d=decimale; i bool sono interi e gli enum stringhe
	protected $cols_and_types = array(
		'token'                  => 's',
		'azione_account'         => 's',
		'richiesto_da_utente_id' => 'i',
		'conferma_email'         => 's',
		'generato_in_data'       => 's',
	);
}