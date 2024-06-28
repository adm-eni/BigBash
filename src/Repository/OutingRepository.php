<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Entity\Status;
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

  public function findByDefault($user): array
  {
    $qb = $this->createQueryBuilder('o');
    $qb->leftJoin('o.attendees', 'a');
    $qb->leftJoin('o.host', 'h');
    $qb->leftJoin('o.status', 's');

    $qb->where('s.name != :statusClosed')
        ->andWhere('(s.name != :statusCreated OR (s.name = :statusCreated AND h.id = :userId))')
        ->setParameter('statusClosed', 'Clôturé')
        ->setParameter('statusCreated', 'En création')
        ->setParameter('userId', $user->getId());

    return $qb->getQuery()->getResult();
  }

  public function findByFilters(array $initialOutings, $user, $campus, $title, $dateStart, $dateEnd, $isHost, $isEntered, $isNotEntered, $isPast): array
  {
    $qb = $this->createQueryBuilder('o');
    $qb->leftJoin('o.campus', 'c');
    $qb->leftJoin('o.attendees', 'a');
    $qb->leftJoin('o.host', 'h');
    $qb->where('1 = 1');
    $qb->andwhere('o IN (:outings)')
        ->setParameter('outings', $initialOutings);
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
      $qb->andWhere('h.id = :user')
          ->setParameter('user', $user->getId());
    }
    if ($isEntered) {
      $qb->andWhere('a.id = :user')
          ->setParameter('user', $user->getId());
    }
    if ($isNotEntered) {
      $subQuery = $this->createQueryBuilder('o2')
          ->select('1')
          ->leftJoin('o2.attendees', 'a2')
          ->where('a2.id = :user AND o2.id = o.id')
          ->setParameter('user', $user->getId())
          ->getDQL();

      $qb->andWhere(sprintf('NOT EXISTS (%s) AND h.id != :user', $subQuery))
          ->setParameter('user', $user->getId());
    }
    if ($isPast) {
      $qb->andWhere('o.startAt < :now')
          ->setParameter('now', new \DateTime());
    }
    return $qb->getQuery()->getResult();
  }

// In OutingRepository.php

  public function findOutingsToUpdate(): array
  {
    return $this->createQueryBuilder('o')
        ->leftJoin('o.status', 's')
        ->where('s.name NOT IN (:excludedStatuses)')
        ->setParameter('excludedStatuses', ['En création', 'Clôturé', 'Annulé'])
        ->getQuery()
        ->getResult();
  }

  public function findStatusByName(string $name): ?Status
  {
    return $this->getEntityManager()
        ->getRepository(Status::class)
        ->findOneBy(['name' => $name]);
  }
}
