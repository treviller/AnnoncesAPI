<?php
namespace AnnoncesBundle\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use AnnoncesBundle\GeonamesAPI\GeonamesInterface;

class CityExistValidator extends ConstraintValidator
{
	private $geonamesAPI;
	
	public function __construct(GeonamesInterface $geonamesService)
	{
		$this->geonamesAPI = $geonamesService;
	}
	
	public function validate($value, Constraint $constraint)
	{
		if(!$this->geonamesAPI->checkCity($value))
		{
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
	}
}
