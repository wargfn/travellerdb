<?php 

namespace AppBundle\Repository;

use phpDocumentor\Reflection\Types\Null_;
use AppBundle\Entity\Decklists;
use Doctrine\ORM\EntityRepository;

class DeckRepository extends EntityRepository
{
	function __construct($entityManager)
	{
		parent::__construct($entityManager, $entityManager->getClassMetadata('AppBundle\Entity\Deck'));
	}
	
}
