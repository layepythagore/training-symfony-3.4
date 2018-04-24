<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
* 
*/
class AdvertController extends Controller
{
 	public function menuAction()
 	{
 		// On fixe en dur une liste ici, bien entendu par la suite
                // on la récupérera depuis la BDD !
 		$listAdverts = [
 			['id' => 2, 'title' => 'Recherche développeur Symfony'],
 			['id' => 5, 'title' => 'Mission de webmaster'],
 			['id' => 9, 'title' => 'Offre de stage webdesigner']
 		];

 		return $this->render('@OCPlatform/Advert/menu.html.twig', ['listAdverts' => $listAdverts]);
 	}

	public function indexAction($page)
	{
		if ($page < 1) { 
	      // On déclenche une exception NotFoundHttpException, cela va afficher
	      // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
	      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
	    }

		//return $this->render('@OCPlatform/Advert/index.html.twig', array('nom' => 'laye'));
		// Notre liste d'annonce en dur
	    $listAdverts = array(
	      array(
	        'title'   => 'Recherche développpeur Symfony',
	        'id'      => 1,
	        'author'  => 'Alexandre',
	        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
	        'date'    => new \Datetime()),
	      array(
	        'title'   => 'Mission de webmaster',
	        'id'      => 2,
	        'author'  => 'Hugo',
	        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
	        'date'    => new \Datetime()),
	      array(
	        'title'   => 'Offre de stage webdesigner',
	        'id'      => 3,
	        'author'  => 'Mathieu',
	        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
	        'date'    => new \Datetime())
	    );

	    // Et modifiez le 2nd argument pour injecter notre liste
	    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
	      'listAdverts' => $listAdverts
	    ));
	}

	public function viewAction($id, Request $req)
	{
		$tag = $req->query->get('tag');

		/*return new Response("Affichage de l'annonce d'id : ".$id);*/
		return $this->render('@OCPlatform/Advert/view.html.twig', ['id' => $id,
			'tag' => $tag]
		);

	}

	public  function addAction(Request $request)
	{
		// Si la requête est en POST, c'est que le visiteur a soumis le formulaire
		if ($request->isMethod('POST')) {
			// Ici, on s'occupera de la création et de la gestion du formulaire
			 $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

			 // Puis on redirige vers la page de visualisation de cettte annonce
		    return $this->redirectToRoute('oc_platform_view', array('id' => 5));
		}		

	    // Puis on redirige vers la page de visualisation de cette annonce
	    return $this->render('@OCPlatform/Advert/add.html.twig');

	}

	public function editAction($id, Request $request)
	{
		// Ici, on récupérera l'annonce correspondante à $id

		// Même mécanisme que pour l'ajout
		if ($request->isMethod('POST')) {
			$request->getSession()->getFlashBag()
				->add('notice', 'Annonce bien modifiée.');

			return $this->redirectToRoute('oc_platform_view', ['id' => 5]);
		}

		return $this->render('@OCPlatform/Advert/edit.html.twig');
	}

	public function deleteAction($id)
	{
		// Ici, on récupérera l'annonce correspondant à $id

		// Ici, on gérera la suppression de l'annonce en question
		return $this->render('@OCPlatform/Advert/delete.html.twig');
	}
}