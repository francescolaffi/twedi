<?php

namespace app\Controllers;

class Test extends Generic {

	public function index(){
		phpinfo();
	}
	
	public function reset_db(){
		
		$db_host = $this->injector->get_parameter('DB.host');
		$db_user = $this->injector->get_parameter('DB.user');
		$db_pass = $this->injector->get_parameter('DB.pass');
		$db_name = $this->injector->get_parameter('DB.name');
		
		/** var $DB MySQLi */
		$DB = new \lib\Data\DB($db_host, $db_user, $db_pass,'');
		
		$sql = <<<SQL
DROP DATABASE IF EXISTS $db_name;
CREATE DATABASE $db_name;
USE $db_name;
\n\n
SQL;
		
		$sql .= file_get_contents(BASEPATH.'/app/schema.sql.php')."\n\n";
		$sql .= file_get_contents(BASEPATH.'/app/viste.sql.php');
		
		$DB->multi_query($sql);
		
		echo '<pre>',$DB->error,PHP_EOL,htmlspecialchars($sql);
		
		$DB->close();
		
	}
	
	public function test_data(){
		
		$lipsum = new \lib\Third_Party\LoremIpsumGenerator(50);
		
		$stmt = $this->factory_build('DB_STMT');
		
		$nutenti = 6;
		$ncorsi = 3;
		$nannunci = 20;
		
		
		$salt = 'abcdefgh';
		$pwd = 'password';
		$pwd_hash = md5($pwd . $salt);
		$um = $this->build_model('Utente');
		$um->pwd_salt = $salt;
		$um->password = $pwd_hash;
		$um->is_active = 1;
		$um->cognome = 'test';
		$um->nome = 'admin';
		$um->email = "{$um->nome}@{$um->cognome}.it";
		$um->is_admin = 1;
		$um->save();
		$um->utente_id = null;
		$um->nome = 'user';
		$um->email = "{$um->nome}@{$um->cognome}.it";
		$um->is_admin = 0;
		$um->iscrizione_corso_id = mt_rand(1,$ncorsi);
		$um->save();
		for($i = 1; $i <= $nutenti; $i++){
			$um->utente_id = null;
			$words = explode(' ',$lipsum->getContent(3,'plain',false));
			$um->nome = $words[0];
			$um->cognome = $words[1];
			$um->email = "{$um->nome}@{$um->cognome}.it";
			$um->iscrizione_corso_id = mt_rand(1,$ncorsi);
			$um->save();
		}
		
		$cm = $this->build_model('Corso');
		
		for($i = 1; $i <= $ncorsi; $i++){
			$cm->corso_id = null;
			$cm->nome_corso = "corso$i ".strstr($lipsum->getContent(3,'plain',false),'.',true);
			$cm->save();
		}
		
		$am = $this->build_model('Annuncio');
		
		$timedelta = 20000;
		$time = time();
		$tipi_annunci = array('lavoro','corso');
		for($i = 1; $i <= $nannunci; $i++){
			$am->annuncio_id = null;
			$am->titolo_annuncio = "annuncio$i ".strstr($lipsum->getContent(3,'plain',false),'.',true);
			$am->contenuto_annuncio = $lipsum->getContent(mt_rand(100,200), 'txt');
			$am->data_pubblicazione = gmdate('Y-m-d H:i:s', $time - ($nannunci-$i)*$timedelta);
			$am->pubblicato_da_utente_id = mt_rand(1,$nutenti);
			$am->categoria_annuncio = $tipi_annunci[mt_rand(0,1)];
			$am->pubblicato_in_corso_id = ('corso' == $am->categoria_annuncio) ? mt_rand(0,$ncorsi) : 0;
			$am->save();
		}
	}
	
}