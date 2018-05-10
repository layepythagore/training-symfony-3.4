<?php
namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\AdvertType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
*
*/
class AdvertController extends Controller
{
    public function menuAction($limit)
    {
            $em = $this->getDoctrine()->getManager();
            $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
              array(),                 // Pas de critère
              array('date' => 'desc'), // On trie par date décroissante
              $limit,                  // On sélectionne $limit annonces
              0                        // À partir du premier
            );

            return $this->render('@OCPlatform/Advert/menu.html.twig', ['listAdverts' => $listAdverts]);
    }

    public function indexAction($page)
    {
        if ($page < 1) {
          throw new createNotFoundException('Page "'.$page.'" inexistante.');
        }

            // Ici je fixe le nombre d'annonces par page à 3
            // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
            $nbPerPage = 3;

            // On récupère notre objet Paginator
            $listAdverts = $this->getDoctrine()->getManager()
                                ->getRepository('OCPlatformBundle:Advert')
                                ->getAdverts($page, $nbPerPage);

            // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne
            // le nombre total d'annonces
            $nbPages = ceil(count($listAdverts) / $nbPerPage);

            // Si la page n'existe pas, on retourne une 404
            if ($page > $nbPages) {
              throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            }

        // Et modifiez le 2nd argument pour injecter notre liste
        return $this->render('@OCPlatform/Advert/index.html.twig', array(
          'listAdverts' => $listAdverts,
              'nbPages'     => $nbPages,
              'page'        => $page
        ));
    }

    public function viewAction($id, Request $req)
    {
        $em = $this->getDoctrine()->getManager();
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
            // On crée un objet Advert
            $advert = new Advert();
            $form = $this->createForm(AdvertType::class, $advert);

        // Reste de la méthode qu'on avait déjà écrit
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){

                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                // Puis on redirige vers la page de visualisation de cettte annonce
                return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }
        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('@OCPlatform/Advert/add.html.twig', ['form' => $form->createView()]);
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
            /* @var advert Advert() */
        if (null === $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
            // Et on construit le formBuilder avec cette instance de l'annonce, comme précédemment
            $form = $this->createForm(AdvertEditType::class, $advert);

            $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {
                $em->flush();
                $request->getSession()->getFlashBag()
                        ->add('notice', 'Annonce bien modifiée.');

                return $this->redirectToRoute('oc_platform_view', ['id' => $advert->getId()]);
        }

        return $this->render('@OCPlatform/Advert/edit.html.twig', array(
                'advert' => $advert,
                'form' => $form->createView()
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");
            return $this->redirectToRoute('oc_platform_home');
        }

        return $this->render('@OCPlatform/Advert/delete.html.twig', [
            'advert' => $advert, 'form' => $form->createView()
        ]);
    }

    public function testAction() {

            $advert = new Advert();
            $advert->setAuthor("laye");
            $advert->setContent("test slug");
            $advert->setTitle("Recherche développeur !");
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush(); // C'est à ce moment qu'est généré le slug

            return new Response('Slug généré : '.$advert->getSlug());
        }
}