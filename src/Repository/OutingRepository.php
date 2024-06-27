<?php

namespace App\Repository;

use App\Entity\Outing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Outing>
 */
class OutingRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Outing::class);
  }

  public function findByFilters($campus, $title, $dateStart, $dateEnd, $isHost, $isEntered, $isNotEntered, $isPast, $user): array
  {
    $qb = $this->createQueryBuilder('o');
    $qb->leftJoin('o.campus', 'c');
    $qb->leftJoin('o.attendees', 'a');
    $qb->leftJoin('o.host', 'u');
    $qb->where('1 = 1');
    if ($campus) {
      $qb->andWhere('c.id = :campus')
          ->setParameter('campus', $campus->getId());
    }
    if ($title) {
      $qb->andWhere('o.title LIKE :title')
          ->setParameter('title', "%$title%");
    }
    if ($dateStart) {
      $qb->andWhere('o.startAt >= :dateStart')
          ->setParameter('dateStart', $dateStart);
    }
    if ($dateEnd) {
      $qb->andWhere('o.startAt <= :dateEnd')
          ->setParameter('dateEnd', $dateEnd);
    }
    if ($isHost) {
      $qb->andWhere('u.id = :user')
          ->setParameter('user', $user->getId());
    }
    if ($isEntered) {
      $qb->andWhere('a.id = :user')
          ->setParameter('user', $user->getId());
    }
    if ($isNotEntered) {
      $qb->andWhere('a.id != :user')
          ->setParameter('user', $user->getId());
    }
    if ($isPast) {
      $qb->andWhere('o.startAt < :now')
          ->setParameter('now', new \DateTime());
    }
    return $qb->getQuery()->getResult();
  }
}
