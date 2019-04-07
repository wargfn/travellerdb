<?php 

namespace AppBundle\Repository;

use phpDocumentor\Reflection\Types\Null_;
use AppBundle\Entity\Decklists;
use Doctrine\ORM\EntityRepository;

class DecklistRepository extends EntityRepository
{
	function __construct($entityManager)
	{
		parent::__construct($entityManager, $entityManager->getClassMetadata('AppBundle\Entity\Decklist'));
	}

	public function findDuplicate($decklist)
	{
		$qb = $this->createQueryBuilder('d')
			->select('d, f')
			->join('d.faction', 'f')
			->andWhere('d.signature = ?1');

		$qb->setParameter(1, $decklist->getSignature());
		$qb->orderBy('d.dateCreation', 'ASC');
		$qb->setMaxResults(1);

		return $qb->getQuery()->getOneOrNullResult();
	}

	//findBy([ 'parent' => $decklist->getParent() ], [ 'version' => 'DESC' ]);
	public function findVersions($decklist)
	{
		$qb = $this->createQueryBuilder('d')
			->select('d, f, ds, c')
			->join('d.faction', 'f')
			->join('d.slots', 'ds')
			->join('ds.card', 'c')
			->andWhere('d.parent = ?1');

		$qb->setParameter(1, $decklist->getParent());
		$qb->orderBy('d.version', 'DESC');

		return $qb->getQuery()->getResult();
	}
}
