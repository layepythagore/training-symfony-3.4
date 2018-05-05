<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace OC\PlatformBundle\Email;

use OC\PlatformBundle\Entity\Application;

class ApplicationMailer {
    /**
     * 
     * @var \Swift_Mailer $mailer
     */
    private $mailer;
    
    public function __construct(\Swift_Mailer $mailer) {
        $this->mailer = $mailer;
    }
    
    public function sendNewNotification(Application $application) {
        /* @var $message \Swift_Message **/
        $message = new \Swift_Message('Nouvelle candidature', 'vous avez reçu une nouvelle candidature.');
        $message->addTo($application->getAdvert()->getAuthor())
                ->addFrom('admin@votresite.com');
        
        $this->mailer->send($message);
    }
    
}
