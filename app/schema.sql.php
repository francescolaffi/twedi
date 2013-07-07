-- <?php die(); //for security

-- -----------------------------------------------------
-- Table `mydb`.`corsi`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `corsi`
(
  `corso_id` INT UNSIGNED SERIAL DEFAULT VALUE,
  `nome_corso` VARCHAR(100) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`corso_id`)
);


-- -----------------------------------------------------
-- Table `mydb`.`utenti`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `utenti`
(
  `utente_id` INT UNSIGNED SERIAL DEFAULT VALUE,
  `email` VARCHAR(100) NOT NULL DEFAULT '' ,
  `password` VARCHAR(32) NOT NULL DEFAULT '' ,
  `pwd_salt` VARCHAR(8) NOT NULL DEFAULT '' ,
  `nome` VARCHAR(100) NOT NULL DEFAULT '' ,
  `cognome` VARCHAR(100) NOT NULL DEFAULT '' ,
  
  `cellulare` VARCHAR(15) NOT NULL DEFAULT '' ,
  `occupazione` ENUM('','studente non lavora','studente cerca lavoro','studente lavora','cerca lavoro','lavora','disoccupato')  NOT NULL DEFAULT '' ,
  `attinenza_lavoro` ENUM('','attinente corso laurea','attinente twedi','non attinente') NOT NULL DEFAULT '' ,
  `nome_azienda` VARCHAR(100) NOT NULL DEFAULT '' ,
  `posizione` VARCHAR(100) NOT NULL DEFAULT '' ,
  `carriera` TEXT NOT NULL DEFAULT '' ,
  `anno_nascita` INT(4) NOT NULL DEFAULT 0 ,
  `sito_internet` VARCHAR(100) NOT NULL DEFAULT '' ,
  
  `iscrizione_corso_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `is_admin` BOOL NOT NULL DEFAULT FALSE ,
  `is_active` BOOL NOT NULL DEFAULT FALSE ,
  `avviso_annunci_corso` BOOL NOT NULL DEFAULT FALSE ,
  `avviso_annunci_lavoro` BOOL NOT NULL DEFAULT FALSE ,
  
  PRIMARY KEY (`utente_id`)
);


-- -----------------------------------------------------
-- Table `mydb`.`annunci`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `annunci`
(
  `annuncio_id` INT UNSIGNED SERIAL DEFAULT VALUE,
  `data_pubblicazione` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `titolo_annuncio` VARCHAR(45) NOT NULL DEFAULT '' ,
  `contenuto_annuncio` TEXT NOT NULL DEFAULT '' ,
  `categoria_annuncio` ENUM('lavoro','corso') NOT NULL DEFAULT 'corso' ,
  `pubblicato_da_utente_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `pubblicato_in_corso_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`annuncio_id`)
);


-- -----------------------------------------------------
-- Table `mydb`.`files`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `files`
(
  `file_id` INT UNSIGNED SERIAL DEFAULT VALUE,
  `data_caricamento` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `titolo_file` VARCHAR(100) NOT NULL DEFAULT '',
  `descrizione_file` TEXT NOT NULL DEFAULT '',
  `nome_originale_file` VARCHAR(100) NOT NULL DEFAULT '',
  `hash_file` VARCHAR(32) NOT NULL DEFAULT '',
  `caricato_da_utente_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `caricato_in_corso_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`file_id`)
);


-- -----------------------------------------------------
-- Table `mydb`.`account_tokens`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `account_tokens`
(
  `token` VARCHAR(10) NOT NULL DEFAULT '',
  `azione_account` ENUM('conferma-registrazione','cambio-email','nuova-password') NOT NULL DEFAULT 'conferma-registrazione',
  `richiesto_da_utente_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `conferma_email` VARCHAR(100) NOT NULL DEFAULT '',
  `generato_in_data` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  PRIMARY KEY (`token`)
);

