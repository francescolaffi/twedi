-- <?php die(); //for security

-- -----------------------------------------------------
-- Vista auth
-- -----------------------------------------------------
CREATE VIEW auth AS
    SELECT utente_id as id, email as name, password as pass, pwd_salt as salt, is_admin, is_active as is_valid
    FROM utenti
;

-- -----------------------------------------------------
-- Vista annunci lavoro
-- -----------------------------------------------------
CREATE VIEW annunci_lavoro AS
    SELECT annunci.*, autori.*
    FROM annunci JOIN utenti AS autori ON (annunci.pubblicato_da_utente_id = autori.utente_id)
    WHERE annunci.categoria_annuncio = 'lavoro'
;

-- -----------------------------------------------------
-- Vista annunci corso
-- -----------------------------------------------------
CREATE VIEW annunci_corso AS
    SELECT annunci.*, autori.*
    FROM annunci JOIN utenti AS autori ON (annunci.pubblicato_da_utente_id = autori.utente_id)
    WHERE annunci.categoria_annuncio = 'corso'
;

-- -----------------------------------------------------
-- Vista annunci tutti corsi
-- -----------------------------------------------------
CREATE VIEW annunci_tutti_corsi AS
    SELECT annunci.*, autori.*
    FROM annunci JOIN utenti AS autori ON (annunci.pubblicato_da_utente_id = autori.utente_id)
    WHERE annunci.categoria_annuncio = 'corso' AND annunci.pubblicato_in_corso_id = '0'
;

-- -----------------------------------------------------
-- Viste files
-- -----------------------------------------------------
CREATE VIEW files_autori AS
    SELECT files.*, autori.*
    FROM files JOIN utenti AS autori ON (files.caricato_da_utente_id = autori.utente_id)
;

CREATE VIEW files_corsi AS
    SELECT files.*, corsi.*
    FROM files JOIN corsi ON (files.caricato_in_corso_id = corsi.corso_id)
;
