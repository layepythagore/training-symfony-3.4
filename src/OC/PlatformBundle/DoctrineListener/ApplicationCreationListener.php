<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace OC\PlatformBundle\DoctrineListener;

use OC\PlatformBundle\Email\ApplicationMailer;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use OC\PlatformBundle\Entity\Application;

class ApplicationCreationListener {

    /**
     * @var ApplicationMailer
     */
    private $applicationMailer;
    
    public function __construct(ApplicationMailer $applicationMailer) {
        $this->applicationMailer = $applicationMailer;
    }
    
    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getObject();
        
        // On ne veut envoyer un email que pour les entités Application
        if (!$entity instanceof Application) {
            return;
        }
        try {
            $this->applicationMailer->sendNewNotification($entity);
        } catch (Exception $ex) {
            
        }        
    }

}

