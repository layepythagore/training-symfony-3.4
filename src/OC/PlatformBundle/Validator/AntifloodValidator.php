<?php
/**
 * Created by PhpStorm.
 * User: AbdoulayeDIOP
 * Date: 12/05/2018
 * Time: 11:26
 */

namespace OC\PlatformBundle\Validator;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntifloodValidator extends ConstraintValidator
{
    private $requestStack;
    private $em;

    // Les arguments déclarés dans la définition du service arrivent au constructeur
    // On doit les enregistrer dans l'objet pour pouvoir s'en resservir dans la méthode validate()
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em           = $em;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        // Pour récupérer l'objet Request tel qu'on le connait, il faut utiliser
        // getCurrentRequest du service request_stack
        $request = $this->requestStack->getCurrentRequest();

        // on récupére l 'ip de celui qui poste
        $ip = $request->getClientIp();
        // on verifie si cette ip déja posté une candidature il y'a moins de 15 secondes
        $isFlood = $this->em->getRepository('OCPlatformBundle:Application')
                            ->isFlood($ip, 15);
        if ($isFlood) {
            // C'est cette ligne qui déclenche l'erreur pour le formulaire, avec en argument le message
            $this->context->addViolation($constraint->message);
        }
    }
}