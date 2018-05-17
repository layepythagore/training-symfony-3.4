<?php
/**
 * Created by PhpStorm.
 * User: AbdoulayeDIOP
 * Date: 12/05/2018
 * Time: 11:21
 */

namespace OC\PlatformBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class Antiflood
 * @package OC\PlatformBundle\Validator
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";

    public function validatedBy()
    {
        return 'oc_platform_antiflood'; // Ici, on fait appel à l'alias du service
    }
}