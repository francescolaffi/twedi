<?php

namespace app\Controllers;

class Account extends Generic {
	
	const EMAIL_REGEX = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i';

	public function registrazione()
	{
		if ($this->session()->is_logged_in()) {
			$this->session()->add_notice('warning', 'Sei già loggato, non puoi rifare la registrazione.', true);
			$this->redirect($this->url_for('home'));
		}
		
		$errori = array();
		
		if (filter_input(INPUT_POST, 'submit')) {
			
			//controllo nome e cognome
			if (!$nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_NO_ENCODE_QUOTES))) {
				$errori['nome'] = 'Devi inserire il tuo nome.';
			}
			if (!$cognome = trim(filter_input(INPUT_POST, 'cognome', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_NO_ENCODE_QUOTES))) {
				$errori['cognome'] = 'Devi inserire il tuo cognome.';
			}
			
			//controllo email, unicità email e conferma email
			if (!$email = strtolower(trim(filter_input(INPUT_POST, 'email')))) {
				$errori['email'] = 'Devi inserire il tuo indirizzo email.';
			} elseif (!preg_match(self::EMAIL_REGEX, $email)) {
				$errori['email'] = 'L\'indirizzo email inserito non è valido.';
			} elseif (0 < $this->build_stmt()->table('utenti')->where('email','=',$email,'s')->count()) {
				$errori['email'] = 'Esiste già un utente con questa email';
			} elseif (strtolower(trim(filter_input(INPUT_POST, 'conferma_email'))) !== $email ) {
				$errori['conferma_email'] = 'La conferma dell\'indirizzo email non combacia.';
			}
			
			//controllo password e conferma password
			if ((!$pwd = trim(filter_input(INPUT_POST, 'password'))) || strlen($pwd)<8) {
				$errori['password'] = 'Inserisci una password lunga almeno 8 caratteri.';
			} elseif (filter_input(INPUT_POST, 'conferma_password') !== $pwd ) {
				$errori['conferma_password'] = 'La conferma della password non combacia.';
			}
			
			if (!filter_input(INPUT_POST, 'accetto_condizioni')) {
				$errori['accetto_condizioni'] = 'Devi accettare le condizioni.';
			}
			
			// 0 nessun errore, 1
			$error_step = (int)(bool) count($errori);
			
			if (!$error_step) {
				$utente = $this->build_model('Utente');
				$utente->nome = $this->convert_case_nomi($nome);
				$utente->cognome = $this->convert_case_nomi($cognome);
				$utente->email = $email;
				$utente->pwd_salt = substr(md5(mt_rand()), 12, 8);
				$utente->password = md5($pwd . $utente->pwd_salt);
				
				if(!$utente->save()) {
					$error_step = 2;
				}
			}
			
			if (!$error_step) {
				$token = $this->build_model('Account_Token');
				$token->richiesto_da_utente_id = $utente->utente_id;
				$token->azione_account = $token::AZN_CONFERMA_REGISTRAZIONE;
				$token->conferma_email = $utente->email;
				$token->generato_in_data = gmdate('Y-m-d H:i:s');
				if (!$token->save() || !$token->token) {
					$error_step = 3;
				}
			}
			
			if (!$error_step) {
				if (!$this->manda_email_conferma_registrazione($token->token)) {
					$error_step = 4;
				}
			}
			
			switch ($error_step) {
				case 1:
					break;
				case 3:
					$token->delete();
				case 2:
					$utente->delete();
					$this->session()->add_notice('error', 'Errore di salvataggio dei dati, riprovare più tardi o contattare un amministratore.', false);
					break;
				case 4:
					$utente->delete();
					$token->delete();
					$this->session()->add_notice('error', 'Errore nell\'invio della email di conferma, riprovare più tardi o contattare un amministratore.', false);
					break;
				default:
					$this->session()->add_notice('success', 'Utente creato correttamente, è stata inviata una email con le istruzioni per attivare l\'account.', true);
					$this->redirect($this->url_for('account', array('action' => 'conferma_email')));
			}
		}
		
		$slots = array('errori_form' => $errori);
		
		$this->render( 'account/registrazione', $slots);
	}
	
	public function conferma_email($token = null)
	{
		$errori_form = array();
		
		if ($token) {
			if ($indirizzo_confermato = $this->elabora_token_email($token)) {
				$this->render('account/email_confermata', compact('indirizzo_confermato') );
				return;
			}
		} elseif (filter_input(INPUT_POST, 'submit')) {
			//TODO implementare reinvio email
			$this->session()->add_notice('warning', 'Funzione non implementata.', false);
		}
		
		$this->render('account/conferma_email', compact('errori_form'));
	}

	public function password_dimenticata() {
		if ($this->session()->is_logged_in()) {
			$this->session()->add_notice('warning', 'Sei già loggato, non puoi recuperare la password.', true);
			$this->redirect($this->url_for('home'));
		}

		$this->render('account/password_dimenticata');
	}

	public function vedi_profilo() {
		$utente = $this->populate_model('Utente', $this->session()->user_id());
		$corso = $this->populate_model('Corso', $utente->iscrizione_corso_id, false);
		$this->render( 'utenti/singolo', compact('utente', 'corso'));
	}

	public function modifica_profilo($uid = null) {
		if ($uid && $this->session()->user_id() != $uid) {
			$this->check_logged_in('Devi accedere per modificare i profili degli utenti.');
			$this->check_is_admin();
			$utente = $this->populate_model('Utente', $uid, 'Utente non trovato');
		} else {
			$this->check_logged_in('Devi accedere per modificare il tuo profilo.');
			$utente = $this->populate_model('Utente', $this->session()->user_id());
		}

		$errori_form = array();

		if (filter_input(INPUT_POST, 'submit')) {

			//controllo nome e cognome
			if (!$nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_NO_ENCODE_QUOTES))) {
				$errori_form['nome'] = 'Devi inserire il tuo nome.';
			}
			if (!$cognome = trim(filter_input(INPUT_POST, 'cognome', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_NO_ENCODE_QUOTES))) {
				$errori_form['cognome'] = 'Devi inserire il tuo cognome.';
			}

			//anno di nascita
			$nascita = filter_input(INPUT_POST, 'anno_nascita');
			if ($nascita && (string)(int)$nascita === (string)$nascita && 2 <= strlen($nascita)) {
				$nascita = (int)$nascita;
				$anno4 = (int)(date('Y'));
				if ($nascita < 0 || ($nascita > 99 && $nascita < ($anno4 - 100))) {
					$errori['anno_nascita'] = 'Anno di nascita non valido.';
				} else {
					$anno2 = $anno4 % 100;
					if ($nascita < $anno2 ) {
						$nascita += intval($anno4/100)*100;
					} elseif ($nascita < 100) {
						$nascita += intval($anno4/100-1)*100;
					}
				}
			} elseif ($nascita) {
				$errori_form['anno_nascita'] = 'Anno di nascita non valido.';
			}

			//sito
			$sito = trim(filter_input(INPUT_POST, 'sito'));
			if ($sito) {
				if (!preg_match('@^((?P<schema>https?)\://)?([-a-z0-9]+\.)*[-a-z0-9]{2,}\.[a-z]{2,4}(/\S*)?$@i', $sito, $matches)) {
					$errori_form['sito'] = 'URL non valido, controllarne la correttezza.';
				} elseif(!@$matches['schema']) {
					$sito = "http://$sito";
				}
			}

			//cellulare
			$cellulare = filter_input(INPUT_POST, 'cellulare');
			$cellulare = trim(preg_replace(array('/^00/', '/[\s-]+/'),array('+', ' '), $cellulare));
			if ($cellulare && !preg_match('/^(\+[1-9]\s?)?([0-9]+\s?)+$/', $cellulare)) {
				$errori_form['cellulare'] = 'Numero non valido, controllarne la correttezza.';
			}

			//controllo occupazione e attinenza
			$occupazioni_enum = array('studente non lavora','studente cerca lavoro','studente lavora','cerca lavoro','lavora','disoccupato');
			$attinenza_enum = array('attinente corso laurea','attinente twedi','non attinente');
			$occupazione = trim(filter_input(INPUT_POST, 'occupazione'));
			if ($occupazione && !in_array($occupazione, $occupazioni_enum)) {
				$errori_form['occupazione'] = 'Opzione non valida, seleziona una opzione dall\'elenco a discesa.';
			}
			$attinenza = trim(filter_input(INPUT_POST, 'attinenza'));
			if ($attinenza && !in_array($attinenza, $attinenza_enum)) {
				$errori_form['attinenza'] = 'Opzione non valida, seleziona una opzione dall\'elenco a discesa.';
			}

			//lavoro
			$azienza = trim(filter_input(INPUT_POST, 'azienda', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_NO_ENCODE_QUOTES));
			$posizione = trim(filter_input(INPUT_POST, 'posizione', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_NO_ENCODE_QUOTES));
			$carriera = trim(filter_input(INPUT_POST, 'carriera', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));

			if (!count($errori_form)) {
				$utente->nome = $this->convert_case_nomi($nome);
				$utente->cognome = $this->convert_case_nomi($cognome);
				$utente->anno_nascita = $nascita;
				$utente->sito_internet = $sito;
				$utente->cellulare = $cellulare;
				$utente->occupazione = $occupazione;
				$utente->attinenza_lavoro = $attinenza;
				$utente->nome_azienda = $azienza;
				$utente->posizione = $posizione;
				$utente->carriera = $carriera;

				if ($utente->save()) {
					$this->session()->add_notice('success', 'Modifiche salvate correttamente', true);
					if ($uid) {
						$utente = $this->template_item_from($utente);
						$this->redirect($utente->url_vedi());
					} else {
						$this->redirect($this->url_for('account', array('action' => 'vedi_profilo')));
					}
				} else {
					$this->session()->add_notice('error', 'Errore di salvataggio dei dati, riprovare più tardi o contattare un amministratore.', false);
				}
			}
		}

		$utente = $this->template_item_from($utente);

		$this->render('account/modifica_profilo', compact('utente', 'errori_form'));
	}

	public function impostazioni_account($uid = null) {
		if ($uid && $this->session()->user_id() != $uid) {
			$this->check_logged_in('Devi accedere per modificare le impostazioni account degli utenti.');
			$this->check_is_admin();
			$utente = $this->populate_model('Utente', $uid, 'Utente non trovato');
		} else {
			$this->check_logged_in('Devi accedere per modificare le tue impostazioni account.');
			$utente = $this->populate_model('Utente', $this->session()->user_id());
		}

		$errori_form = array();

		if (filter_input(INPUT_POST, 'submit')) {

		}

		$opzioni_select_corsi = array('0' => 'Nessun corso');
		$stmt = $this->build_stmt()->table('corsi')->order_by('corso_id DESC');
		$stmt->select();
		$stmt->bind_assoc($corso_row);
		while ($stmt->fetch()) {
			$opzioni_select_corsi[$corso_row['corso_id']] = $corso_row['nome_corso'];
		}

		$this->render('account/impostazioni_account', compact('utente', 'errori_form', 'opzioni_select_corsi'));
	}
	
	/**
	 * formatta i nomi con solo le iniziali maiuscole
	 * 
	 * @param string $nome
	 * 
	 * @return string	
	 */
	private function convert_case_nomi($nome) {
		return preg_replace_callback('/\b.*?\b/', function ($m){
			return mb_convert_case($m[0], MB_CASE_TITLE);
		}, $nome);
	}
	
	private function elabora_token_email($tok_str)
	{
		$token = $this->build_model('Account_Token');
		if (!$token->populate($tok_str)) {
			$this->session()->add_notice('error', 'Questo link non è corretto, controllare che sia esatto e riprovare.', false);
			return false;
		}
		
		$utente = $this->build_model('Utente');
		if (!$utente->populate($token->richiesto_da_utente_id)) {
			$this->session()->add_notice('error', 'Questo link non è più valido.', false);
			@$token->delete();
			return false;
		}
		
		if ($token::AZN_CONFERMA_REGISTRAZIONE == $token->azione_account) {
			if ($utente->is_active || $utente->email != $token->conferma_email) {
				$this->session()->add_notice('error', 'Questo link non è più valido.', false);
				@$token->delete();
				return false;
			}
			
			if ($this->session()->is_logged_in()) {
				$this->session()->add_notice('warning', 'Sei già loggato, non puoi compiere questa azione.', false);
				return false;
			}
			
			$utente->is_active = true;
			
			if (!$utente->save()) {
				$this->session()->add_notice('error', 'Non è stato possibile attivare l\'account per un problema del server, riprovare più tardi o contattare un amministratore.', false);
				return false;
			}
			
			@$token->delete();
			return $utente->email;
		} elseif ($token::AZN_CAMBIO_EMAIL == $token->azione_account) {
			//controllare utente loggato non sia diverso
			//controllare email non sia già in uso
			
			//cambiare email
			//eliminare token
		} 
	}
	
	private function manda_email_conferma_registrazione($utente, $token)
	{
		$link_conferma = $this->url_for('account', array('action' => 'conferma_email', 'token' => $token));
		$mail = $this->factory_build('mailer');
		$mail->addAddress($utente->email, "{$utente->nome} {$utente->cognome}");
		$mail->Subject = 'Conferma registrazione';
		$mail->Body = <<<MSG
Grazie per esserti registrato.

Puoi confermare il tuo indirizzo email cliccando il link:
$link_conferma

se non puoi cliccare il link, copialo e incollalo nella barra degli indirizzi del tuo browser.
MSG;
		return $mail->Send();
	}
	
	private function manda_email_conferma_cambio_email($utente, $token, $nuovamail)
	{
		$link_conferma = $this->url_for('account', array('action' => 'conferma_email', 'token' => $token));
		$mail = $this->factory_build('mailer');
		$mail->addAddress($nuovamail, "{$utente->nome} {$utente->cognome}");
		$mail->Subject = 'Conferma cambio email';
		$mail->Body = <<<MSG
Puoi confermare il tuo nuovo indirizzo email cliccando il link:
$link_conferma

se non puoi cliccare il link, copialo e incollalo nella barra degli indirizzi del tuo browser.
MSG;
		return $mail->Send();
	}

}