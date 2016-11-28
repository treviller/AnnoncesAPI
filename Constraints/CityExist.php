<?php
namespace AnnoncesBundle\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CityExist extends Constraint
{
	public $message = 'La ville spécifiée n\'existe pas';
	
	public function validatedBy()
	{
		return get_class($this).'Validator';
	}
}
