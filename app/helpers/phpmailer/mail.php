<?php namespace helpers\phpmailer;
class mail extends phpmailer {
    // Set default variables for all new objects
    public $From     = 'noreply@simplemvcframework.com';
    public $FromName = SITETITLE;
    //public $Host     = 'smtp.gmail.com';
    //public $Mailer   = 'smtp';
    //public $SMTPAuth = true;                         
    //public $Username = 'email';                         
    //public $Password = 'password';                         
    //public $SMTPSecure = 'tls';                         
    public $WordWrap = 75;
                         

    public function send() {
        $this->AltBody = strip_tags(stripslashes($this->Body))."\n\n";
        $this->AltBody = str_replace("&nbsp;","\n\n",$this->AltBody);
        return parent::send();
    }


}