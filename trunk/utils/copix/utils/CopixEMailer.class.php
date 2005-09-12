<?php
/**
* @package   copix
* @subpackage generaltools
* @version   $Id: CopixEMailer.class.php,v 1.13 2005/04/05 15:06:09 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* encapsule un email
* @package   copix
* @subpackage generaltools
*/
class CopixEMail {
    /**
    * The message.
    */
    var $message;
    /**
    * subject of the message
    */
    var $subject;
    /**
    * recipient
    */
    var $to;
    /**
    * carbon copy
    */
    var $cc;
    /**
    * hidden carbon copy
    */
    var $cci;
    /**
    * Sender
    */
    var $from;

    function CopixEMail ($to, $cc, $cci, $subject, $message){
        $this->from     = CopixConfig::get ('|mailFrom');
        $this->fromName = CopixCOnfig::get ('|mailFromName');

        $this->to = $to;
        $this->cc = $cc;
        $this->cci = $cci;
        $this->message = $message;
        $this->subject = $subject;
    }

    /**
    * Fonction d'envois
    */
    function send ($from = null, $fromName = null){
        $mailer = new CopixEMailer ();
        return $mailer->send ($this, $from, $fromName);
    }

    /**
    * Checks if we can send an email with the given configuration.
    */
    function & check (){
        require_once (COPIX_CORE_PATH . 'CopixErrorObject.class.php');
        $error = & new CopixErrorObject ();
        if ($this->to === null){
            $error->addError ('to', 'Aucune valeur donnée à destinataire.');
        }
        if ($this->from === null){
            $error->addError ('from', 'Aucune valeur expéditeur');
        }
        return $error;
    }
}

/**
* encapsule un email Mail au format HTML
* @package   copix
* @subpackage generaltools
*/
class CopixHTMLEMail extends CopixEMail {
    var $textEquivalent = 'HTML message';
    function CopixHTMLEMail ($to, $cc, $cci, $subject, $message, $textEquivalent=null){
        parent::CopixEMail ($to, $cc, $cci, $subject, $message);
        $this->textEquivalent = $textEquivalent;
    }
}

/**
* Mail au format texte
* @package   copix
* @subpackage generaltools
*/
class CopixTextEMail extends CopixEMail{
    function CopixTextEMail ($to, $cc, $cci, $subject, $message){
        parent::CopixEMail ($to, $cc, $cci, $subject, $message);
    }
}

/**
* Le géstionnaire de mail
* @package   copix
* @subpackage generaltools
*/
class CopixEMailer {
    /**
    * MailerObject
    */
    var $_mailer = null;

    /**
    * send an email.
    */
    function send ($copixEMail, $fromAdress=null, $fromName=null){
        //check if we want to write the message on the hard drive.
        if (intval(CopixConfig::get ('|mailSaveToDisk')) === 1){
            $this->_writeOnDisk ($copixEMail);
        }
        //check if we've been asked not to send the emails.
        if (intval(CopixConfig::get ('|mailEnabled')) !== 1){
            return;
        }
        $mailer = & $this->_createMailer();

        //check the HTML content, if any.
        if (strtolower (get_class ($copixEMail)) == strtolower ('CopixHTMLEMail')){
            $mailer->setHtml($copixEMail->message, $copixEMail->textEquivalent);
        }else{
            $mailer->setText ($copixEMail->message);
        }
        $mailer->setSubject($copixEMail->subject);

        $fromAdress = $fromAdress == null ? CopixConfig::get ('|mailFrom') : $fromAdress;
        $fromName =   $fromName == null ? CopixConfig::get ('|mailFromName') : $fromName;
        $mailer->setFrom('"'.$fromName.'" <'.$fromAdress.'>');
        
        return $mailer->send((array) $copixEMail->to, CopixConfig::get ('|mailMethod'));
    }
    /**
    * Writes the email on the harddrive
    */
    function _writeOnDisk (&$copixEmail){
        $mailFilePath = CopixCOnfig::get ('|mailPath') . UniqId ('mail_');
        //Writes the mail into the file
        $f = fopen ($mailFilePath, "w");
        fwrite ($f, '________________________________________________________'."\n");
        fwrite ($f, 'To: ');
        foreach ((array) $copixEMail->to as $adr){
            fwrite ($f, $adr."\n");
        }
        fwrite ($f, '________________________________________________________'."\n");
        fwrite ($f, 'cc: ');
        foreach ((array) $copixEMail->cc as $adr){
            fwrite ($f, $adr."\n");
        }
        fwrite ($f, '________________________________________________________'."\n");
        fwrite ($f, 'cci: ');
        foreach ((array) $copixEMail->cc as $adr){
            fwrite ($f, $adr."\n");
        }
        fwrite ($f, '________________________________________________________'."\n");
        fwrite ($f, 'Subject: '.$copixEMail->subject."\n");
        fwrite ($f, '________________________________________________________'."\n");
        fwrite ($f, 'Text content'."\n");
        fwrite ($f, '________________________________________________________'."\n");
        fwrite ($f, $copixEMail->message."\n");
        fwrite ($f, '________________________________________________________'."\n");
        fwrite ($f, 'HTML content'."\n");
        fwrite ($f, '________________________________________________________'."\n");
        fwrite ($f, $copixEMail->messageHTML."\n");
        fclose ($f);
    }
    /**
    * creates a mailer object
    * we only wants to create the object once....
    * @return htmlMimeMail
    */
    function & _createMailer () {
        if ($this->_mailer === null){
            require_once (COPIX_PATH.'../htmlMimeMail-2.5.0/htmlMimeMail.php');
            $mail = & new htmlMimeMail ();
            $mail->setReturnPath(CopixConfig::get ('|mailFrom'));
            $mail->setFrom('"'.CopixConfig::get ('|mailFromName').'" <'.CopixConfig::get ('|mailFrom').'>');
            $mail->setHeader('X-Mailer', 'COPIX (http://copix.aston.fr) with HTML Mime mail class (http://www.phpguru.org)');

            if (CopixConfig::get ('|mailMethod') == 'smtp'){
                $mail->setSMTPParams(CopixConfig::get ('|mailSmtpHost'));
            }

            $this->_mailer = & $mail;
        }
        return $this->_mailer;
    }
}
?>