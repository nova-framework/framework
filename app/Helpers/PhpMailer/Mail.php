<?php
namespace Helpers\PhpMailer;

/*
 * Mail Helper
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 1.0
 * @date May 18 2015
 */
class Mail extends PhpMailer
{
    // Set default variables for all new objects
    public $From     = 'noreply@domain.com';
    public $FromName = SITETITLE;
    //public $Host     = 'smtp.gmail.com';
    //public $Mailer   = 'smtp';
    //public $SMTPAuth = true;
    //public $Username = 'email';
    //public $Password = 'password';
    //public $SMTPSecure = 'tls';
    public $WordWrap = 75;
    public $log = array();

  	public function __construct($param = false)
  	{
  		parent::__construct();

  		if(!isset($param['type']) OR isset($param['type']) && $param['type'] == 'smtp'){

  			//$isSMTP 		= (!isset($param['isSMTP'])) 		? MAIL_IS_SMTP 		: $param['isSMTP'];
  			$smtpAuth 		= (!isset($param['smtpAuth'])) 		? MAIL_SMTP_AUTH 	: $param['smtpAuth'];
  			$isHTML			= (!isset($param['isHTML']))		? MAIL_IS_HTML		: $param['isHTML'];
  			$charset		= (!isset($param['charset']))		? MAIL_CHARSET		: $param['charset'];
  			$smtpSecure 	= (!isset($param['smtpSecure'])) 	? MAIL_SMTP_SECURE 	: $param['smtpSecure'];
  			$host 			= (!isset($param['host'])) 			? MAIL_HOST 		: $param['host'];
  			$port 			= (!isset($param['port'])) 			? MAIL_PORT 		: $param['port'];
  			$user 			= (!isset($param['user'])) 			? MAIL_USER 		: $param['user'];
  			$pass 			= (!isset($param['pass'])) 			? MAIL_PASS 		: $param['pass'];

  			//Making an SMTP connection with authentication.

  			$this->Mailer = 'smtp';                    	  		  // Set mailer to use SMTP
  			$this->Host = $host;  								  // Specify main and backup SMTP servers
  			$this->SMTPAuth = $smtpAuth;                          // Enable SMTP authentication
  			$this->Username = $user;                 			  // SMTP username
  			$this->Password = $pass;                              // SMTP password
  			$this->SMTPSecure = $smtpSecure;                      // Enable TLS encryption, `ssl` also accepted
  			$this->Port = $port;								  // TCP port to connect to
  			$this->isHTML($isHTML);								  // Set email format to HTML
  			$this->CharSet = $charset;

  		}

  		else if(isset($param['type']) && $param['type'] == 'sendmail'){
  			//Sending a message using a local sendmail binary.
  			$this->Mailer = 'sendmail';
  		}

  		else if(isset($param['type']) && $param['type'] == 'mail' OR $param['type'] == 'php'){
  			//Send messages using PHP's mail() function.
  			$this->Mailer = 'mail';
  		}

  		//Enable SMTP debugging
  		// 0 = off (for production use)
  		// 1 = client messages
  		// 2 = client and server messages
  		//$this->SMTPDebug = 2;
  		//$this->Debugoutput = 'html';


  	}

      public function quick($subject, $message, $from, $destination, $replyto = null)
      {

          $this->subject($subject);
          $this->body($message);

          $replyto = ($replyto) ? $replyto : $from;

          $this->from($from, false);
          $this->replyTo($replyto);


          $this->destination($destination);

          return $this->go();
      }

      /**
       * The Subject of the message.
       * @type string
       */

  	public function subject($subject)
  	{
  		$this->Subject = $subject;
  	}

  	/**
  	 *
  	 * @param string ou array $destination
  	 * @param bool $send
  	 *
  	 * 	Ex. 1: $destination = array(
  	 *		'aangelomarinho@gmail.com, Adriano Marinho',
  	 *		'fabio@fabioassuncao.com.br, Fábio Silva',
  	 *		'fabio23gt@gmail.com, Fábio Assunção'
  	 *	);
  	 *
  	 *	Ex. 2: $destination = array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção');
  	 *
  	 *	Ex. 3: $destination = array(
  	 *		array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção'),
  	 *		array('mail' => 'fabio.as@live.com', 'name' => 'Adriano Marinho'),
  	 *	);
  	 *
  	 *	Ex. 4: $destination = 'fabio@fabioassuncao.com.br, Fábio Silva';
  	 *
  	 */

  	public function destination($destination, $send = false)
  	{
  		return $this->address('to', $destination, $send);
  	}

  	/**
  	 * @param string ou array $mailingList
  	 *
  	 * 	Ex. 1: $mailingList = array(
  	 *		'aangelomarinho@gmail.com, Adriano Marinho',
  	 *		'fabio@fabioassuncao.com.br, Fábio Silva',
  	 *		'fabio23gt@gmail.com, Fábio Assunção'
  	 *	);
  	 *
  	 *	Ex. 2: $mailingList = array(
  	 *		array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção'),
  	 *		array('mail' => 'fabio.as@live.com', 'name' => 'Adriano Marinho'),
  	 *	);
  	 *
  	 */

  	public function mailingList($mailingList)
  	{
  		return $this->address('to', $mailingList, true);
  	}

  	/**
  	 * @param string ou array $from
  	 * @param bool ou array $replyto
  	 *
  	 * if $replyto is true => $replyto == $from
  	 *
  	 *	Ex. 1: $from = array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção');
  	 *	Ex. 2: $from = 'fabio@fabioassuncao.com.br, Fábio Silva';
  	 *
  	 */

  	public function from($from, $replyto = true)
  	{

  		if($replyto){
  			$this->replyTo($from);
  		}

  		if(is_string($from) && strpos($from, ',')){
  			$f = explode(',', $from);
  			$this->SetFrom($f[0], $f[1]);

  			return;
  		}

  		else if(is_array($from) && isset($from['mail']) && isset($from['name'])){
  			$this->SetFrom($from['mail'], $from['name']);

  			return;
  		}

  	}

  	/**
  	 * Add a "Reply-to" address.
  	 *
  	 * @param string ou array $replyTo
  	 *
  	 * 	Ex. 1: $replyTo = array(
  	 *		'aangelomarinho@gmail.com, Adriano Marinho',
  	 *		'fabio@fabioassuncao.com.br, Fábio Silva',
  	 *		'fabio23gt@gmail.com, Fábio Assunção'
  	 *	);
  	 *
  	 *	Ex. 2: $replyTo = array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção');
  	 *
  	 *	Ex. 3: $replyTo = array(
  	 *		array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção'),
  	 *		array('mail' => 'fabio.as@live.com', 'name' => 'Adriano Marinho'),
  	 *	);
  	 *
  	 *	Ex. 4: $replyTo = 'fabio@fabioassuncao.com.br, Fábio Silva';
  	 *
  	 */

  	public function replyTo($replyTo)
  	{
  		$this->address('Reply-To', $replyTo);
  	}


  	/**
  	 * Add a "CC" address.
  	 *
  	 * @param string ou array $replyTo
  	 *
  	 * 	Ex. 1: $replyTo = array(
  	 *		'aangelomarinho@gmail.com, Adriano Marinho',
  	 *		'fabio@fabioassuncao.com.br, Fábio Silva',
  	 *		'fabio23gt@gmail.com, Fábio Assunção'
  	 *	);
  	 *
  	 *	Ex. 2: $replyTo = array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção');
  	 *
  	 *	Ex. 3: $replyTo = array(
  	 *		array('mail' => 'fabio23gt@gmail.com', 'name' => 'Fábio Assunção'),
  	 *		array('mail' => 'fabio.as@live.com', 'name' => 'Adriano Marinho'),
  	 *	);
  	 *
  	 *	Ex. 4: $replyTo = 'fabio@fabioassuncao.com.br, Fábio Silva';
  	 */

  	public function cc($cc)
  	{
  		$this->address('cc', $cc);
  	}

  	/**
  	 * An HTML or plain text message body.
  	 * If HTML then call isHTML(true).
  	 * @type string
  	 */

  	public function body($body){
  		$this->Body = $body;
  	}

  	public function template($template, $data, $custom = false)
  	{

  			if($custom){

  				$filename = "app/templates/" . $custom ."/". $template;

  			}else{

  				$filename = "app/templates/email/" . $template;

  			}

  			$fd = fopen ($filename, "r");
  			$mailcontent = fread ($fd, filesize ($filename));


  			foreach ($data as $key => $value){
  				$mailcontent = str_replace("%%$key%%", $value, $mailcontent );
  			}

  			$mailcontent = stripslashes($mailcontent);



  			fclose ($fd);
  			$this->Body = $mailcontent;
  			$this->AltBody = 'Se este e-mail não aparecer corretamente, habilite a visualização de mensagens em HTML.';

  	}

  	private function address($kind, $data, $send = false)
  	{
  		if($send){
  			// SMTP connection will not close after each email sent, reduces SMTP overhead
  			// Deixa em aberto a conexão com servidor
  			$this->SMTPKeepAlive = true;
  		}

  		if(is_string($data) && strpos($data, ',')){
  			$d = explode(',', $data);
  			$this->addAnAddress($kind, $d[0], $d[1]);

  			if($send){
  				$result = $this->go(true);
  				array_push($this->log, array('mail' => $d[0], 'result' => $result));
  			}

  			return $this->log;
  		}

  		if(is_array($data) && isset($data['mail']) && isset($data['name'])){
  			$this->addAnAddress($kind, $data['mail'], $data['name']);

  			if($send){
  				$result = $this->go(true);
  				array_push($this->log, array('mail' => $data['mail'], 'result' => $result));
  			}

  			return $this->log;
  		}

  		else if(is_array($data)){

  			foreach($data as $d){

  				if(is_array($d) && isset($d['mail']) && isset($d['name'])){
  					$this->addAnAddress($kind, $d['mail'], $d['name']);

  					if($send){
  						$result = $this->go(true);
  						array_push($this->log, array('mail' => $d['mail'], 'result' => $result));
  					}
  				}

  				else if(is_string($d) && strpos($d, ',')){

  		        	$d = explode(',', $d);
  		        	$this->addAnAddress($kind, $d[0], $d[1]);

  		        	if($send){
  						$result = $this->go(true);
  						array_push($this->log, array('mail' => $d[0], 'result' => $result));
  		        	}

  				}

  			}

  			return $this->log;

  		}
  	}



  	/**
  	 * @param string ou array $attachment
  	 *
  	 * 	Ex. 1: $attachment = array(
  	 *		'uploads/teste_anexo.png, teste_anexo.png'
  	 *		'uploads/teste_anexo2.png, teste_anexo2.png'
  	 *	);
  	 *
  	 *	Ex. 2: $attachment = array('path' => 'uploads/teste_anexo.png', 'name' => 'teste_anexo.png');
  	 *
  	 *	Ex. 3: $attachment = array(
  	 *		array('path' => 'uploads/teste_anexo.png', 'name' => 'teste_anexo.png'),
  	 *		array('path' => 'uploads/teste_anexo2.png', 'name' => 'teste_anexo2.png'),
  	 *	);
  	 *
  	 *	Ex. 4: $attachment = 'uploads/teste_anexo.png, teste_anexo.png';
  	 *
  	 */

  	public function attachment($attachment)
  	{
  		if(is_string($attachment) && strpos($attachment, ',')){
  			$a = explode(',', $attachment);
  			$this->AddAttachment($a[0], $a[1]);

  			return;
  		}

  		if(is_array($attachment) && isset($attachment['path']) && isset($attachment['name'])){
  			$this->AddAttachment($attachment['path'], $attachment['name']);

  			return;
  		}

  		else if(is_array($attachment)){

  			foreach($attachment as $a){

  				if(is_array($a) && isset($a['path']) && isset($a['name'])){
  					$this->AddAttachment($a['path'], $a['name']);
  				}

  				else if(is_string($a) && strpos($a, ',')){

  					$a = explode(',', $a);
  					$this->AddAttachment($a[0], $a[1]);

  				}

  			}

  		}

  	}

  	public function go($mailingList = false)
  	{

  		$send = $this->Send();

  		if(!$send){
  			return "Erro ao enviar email.  ".$this->ErrorInfo;
  		}
  		else {
  			// se enviado com sucesso, limpa recipientes e anexos;
  			// Clear all addresses and attachments for next loop

  			if(!$mailingList){
  				$this->clearAllRecipients();
  			}

  			else{
  				$this->clearAddresses();
  			}

  			$this->clearAttachments();

  			return $send;
  		}

  	}

  	public function log()
  	{
  		return $this->log;
  	}

    public function send()
    {
        $this->AltBody = strip_tags(stripslashes($this->Body))."\n\n";
        $this->AltBody = str_replace("&nbsp;", "\n\n", $this->AltBody);
        return parent::send();
    }
}
