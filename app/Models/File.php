<?php

namespace app\Models;

use lib\Data\Model;

class File extends Model {
	
	const FILES_FOLDER = '/files';
	
	# nome tipo di item
	protected $item_type = 'file';
	
	# nome tabella
	protected $table = 'files';
	
	# primary key
	protected $pkey = 'file_id';
	
	# tutte le colonne della tabella a parte la pkey, i tipi possibili sono
	#s=stringa, i=intero, d=decimale; i bool sono interi e gli enum stringhe
	protected $cols_and_types = array(
		'hash_file'     => 's',
		'data_caricamento'    => 's',
		'titolo_file' => 's',
		'descrizione_file' => 's',
		'nome_originale_file'     => 's',
		'caricato_da_utente_id'  => 'i',
		'caricato_in_corso_id'  => 'i',
	);
	
	public function populate_hash($hash)
	{
		if (empty($hash)) {
			return false;
		}
		
		$stmt = $this->stmt->where('hash_file','=',$hash,'s');
		$stmt->select();
		$stmt->clean_where();

		$stmt->bind_assoc($this->values);
		$stmt->store_result();

		return $stmt->fetch();
	}
	
	public function check_unique()
	{
		
	}
	
	public static function files_folder()
	{
		return BASEPATH . self::FILES_FOLDER;
	}
	
	public function file_path()
	{
		return self::files_folder() .'/'. $this->values['hash_file'];
	}
}