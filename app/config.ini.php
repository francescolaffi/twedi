;<?php die(); //for security

; debug config
debug.display_errors = localhost ; never/localhost/always
debug.error_reporting = -1 ; binary mask
debug.log_file = log.txt ; relative to basepath, disabled if evalutate to empty

environment.timezone = 'Europe/Rome';

; DB config
DB.host = localhost
DB.user = root
DB.pass = 
DB.name = twedi

; Session config
session.auth_table =  'auth'
session.auth_salt =  ; auth cookie salt
session.auth_cookie = auth ; auth cookie name
session.auth_duration = 1296000 ; 15days
session.flash_cookie = flash ; flash cookie name

; Routing and urls
routing.home_url = 'http://localhost/twedi'
routing.public_folder = '/public'
;routing.default_action = 'index'

mailer.from_mail = 'test@foobar.com';
mailer.from_name = 'Test';

; default msgs
strings.404msg = 'Pagina non trovata.'
strings.500msg = 'Errore del server.'
strings.nonce_err = "Errore nel token di verifica dell'azione. Ritorna indietro, aggiorna la pagina e riprova."
strings.permissions_err = 'Permessi insufficienti. Sono necessari i permessi di amministratore per compiere questa azione.'