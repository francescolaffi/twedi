;<?php die(); //for security

;
; Application Routes
;
; Format
; [{route_name}]
; uri = {the_uri_pattern}
; defaults[{param_name}] = {default_param_value}
; conditions[{placeholder_name}] = {regex_to_use_for_placeholder}
;

[home]
    uri = /
    defaults[controller] = Home

[login]
    uri = /login
    defaults[controller] = Auth
    defaults[action] = login

[logout]
    uri = /logout
    defaults[controller] = Auth
    defaults[action] = logout

[sitemap]
    uri = /sitemap
    defaults[controller] = Sitemap
    defaults[action] = index

[utente]
    uri = /utenti/:id
    defaults[controller] = Utenti
    defaults[action] = singolo

[amministrazione_utente]
    uri = /utenti/:id/:action
    defaults[controller] = Account
    defaults[action] = singolo
	conditions[action] = '(modifica-profilo|impostazioni-account|elimina-account)'
	
[utenti]
    uri = /utenti/
    defaults[controller] = Utenti
	
[annuncio_corso]
    uri = /corsi/:id/annunci/:id2/:action?
    defaults[controller] = Annunci_Corso
    defaults[action] = singolo
	conditions[action] = '(modifica|elimina)'
	
[annunci_corso]
    uri = /corsi/:id/annunci/:action
    defaults[controller] = Annunci_Corso
	conditions[action] = '(inserisci)'
	
[annuncio_globale]
    uri = /corsi/annunci/:id/:action?
    defaults[controller] = Annunci_Globali
    defaults[action] = singolo
	conditions[action] = '(modifica|elimina)'
	
[annunci_globali]
    uri = /corsi/annunci/:action?
    defaults[controller] = Annunci_Globali
	conditions[action] = '(inserisci)'
	
[file_corso]
    uri = /corsi/:id/file/:id2/:action?
    defaults[controller] = Files_Corso
    defaults[action] = singolo
	conditions[action] = '(download|modifica|elimina)'
	
[files_corso]
    uri = /corsi/:id/file/:action
    defaults[controller] = Files_Corso
	conditions[action] = '(inserisci)'
	
[file_globale]
    uri = /corsi/file/:id/:action?
    defaults[controller] = Files_Globali
    defaults[action] = singolo
	conditions[action] = '(download|modifica|elimina)'
	
[files_globali]
    uri = /corsi/file/:action?
    defaults[controller] = Files_Globali
	conditions[action] = '(inserisci)'
	
[corso]
    uri = /corsi/:id/:action?
    defaults[controller] = Corsi
    defaults[action] = singolo
	conditions[action] = '(modifica|elimina)'
	
[corsi]
    uri = /corsi/:action?
    defaults[controller] = Corsi
	conditions[action] = '(inserisci)'

[annuncio_lavoro_slug]
    uri = /annunci-di-lavoro/:id-:slug/
    defaults[controller] = Annunci_Lavoro
    defaults[action] = singolo
	conditions[slug] = '([\w-]+)'

[annuncio_lavoro]
    uri = /annunci-di-lavoro/:id/:action
    defaults[controller] = Annunci_Lavoro
	conditions[action] = '(modifica|elimina)'
	
[annunci_lavoro]
    uri = /annunci-di-lavoro/:action?
    defaults[controller] = Annunci_Lavoro
	conditions[action] = '(inserisci)'
	
[account]
	uri = /account/:action?/:token?
	defaults[controller] = Account
	defaults[action] = vedi_profilo
	conditions[action] = '(registrazione|password-dimenticata|conferma-email|modifica-profilo|impostazioni-account|elimina-account)'
	
[test]
    uri = /t/:action?
    defaults[controller] = Test
    defaults[action] = index
