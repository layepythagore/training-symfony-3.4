<?php

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class CoreController extends Controller{
    
    // accueil
    function accueilAction() {
        return $this->render('@OCCore/core/index.html.twig');
    }
    
    function contactAction(Requeest $req) {
        $session = $req->getSession();
        $session->getFlashBag->add('info', 'La page de contact n’est pas encore disponible,'
                . ' merci de revenir plus tard.');
        
        return $this->redirecToRoute('oc_core_home');
    }
}

