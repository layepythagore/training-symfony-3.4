<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
* 
*/
class AdvertController extends Controller
{
 	public function menuAction($limit)
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
	    return $this->render('@OCPlatform/Advert/index.html.twig', array(
	      'listAdverts' => $listAdverts
	    ));
	}

	public function viewAction($id, Request $req)
	{
            $em = $this->getDoctrine()->getManager();
	    // On récupère l'entité correspondante à l'id $id
	    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

	    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
	    // ou null si l'id $id  n'existe pas, d'où ce if :
	    if (null === $advert) {
	      	throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
	    }

	    // On récupère la liste des candidatures de cette annonce
            $listApplications = $em->getRepository('OCPlatformBundle:Application')
    					->findBy(array('advert' => $advert));
            
            // On récupère maintenant la liste des AdvertSkill
            $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')
                                   ->findBy(array('advert' => $advert));                

            return $this->render('@OCPlatform/Advert/view.html.twig', [
                'advert' => $advert, 
                'listApplications' => $listApplications,
                'listAdvertSkills' => $listAdvertSkills
	    ]);
	}

	public  function addAction(Request $request)
	{   
            $em = $this->getDoctrine()->getManager();
	    $advert = new Advert();
	    $advert->setTitle('Recherche développeur Symfony.');
	    $advert->setAuthor('Alexandre');
	    $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

	    // On récupère toutes les compétences possibles
            $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();
            // Pour chaque compétence
            foreach ($listSkills as $skill) {
              // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
              $advertSkill = new AdvertSkill();
              // On la lie à l'annonce, qui est ici toujours la même
              $advertSkill->setAdvert($advert);
              // On la lie à la compétence, qui change ici dans la boucle foreach
              $advertSkill->setSkill($skill);
              // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
              $advertSkill->setLevel('Expert');
              // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
              $em->persist($advertSkill);
            }
            
            $em->persist($advert);            
	    $em->flush();
	    // Reste de la méthode qu'on avait déjà écrit
	    if ($request->isMethod('POST')) {
	      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
	      // Puis on redirige vers la page de visualisation de cettte annonce
	      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
	    }
	    // Si on n'est pas en POST, alors on affiche le formulaire
	    return $this->render('@OCPlatform/Advert/add.html.twig', ['advert' => $advert]);
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

		
	    $em = $this->getDoctrine()->getManager();
	    // On récupère l'annonce $id
	    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

	    if (null === $advert) {
	      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
	    }
	    // La méthode findAll retourne toutes les catégories de la base de données
	    $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();
	    // On boucle sur les catégories pour les lier à l'annonce
	    foreach ($listCategories as $category) {
	      $advert->addCategory($category);
	    }
	    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
	    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine
	    // Étape 2 : On déclenche l'enregistrement
	    $em->flush();
		
		return $this->render('@OCPlatform/Advert/edit.html.twig', array(
		  'advert' => $advert
		));
	}

	public function deleteAction($id)
	{
		// Ici, on récupérera l'annonce correspondant à $id

		// Ici, on gérera la suppression de l'annonce en question
		return $this->render('@OCPlatform/Advert/delete.html.twig');
	}
}