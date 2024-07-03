<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Entity\User;
use App\Enum\Status;
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

  public function findOutingsToUpdate(): array
  {
    $now = new \DateTime();
    $oneMonthAgo = date_modify($now, '-1 month');

    $qb = $this->createQueryBuilder('o')
        ->where('o.status = :openStatus AND o.startAt <= :now AND :now < (o.startAt + o.duration)')
        ->orWhere('o.status = :ongoingStatus AND (o.startAt + o.duration) <= :now AND :oneMonthAgo < (o.startAt + o.duration)')
        ->orWhere('o.status IN (:endStatuses) AND (o.startAt + o.duration) <= :oneMonthAgo')
        ->setParameter('now', $now)
        ->setParameter('oneMonthAgo', $oneMonthAgo)
        ->setParameter('openStatus', Status::OPEN)
        ->setParameter('ongoingStatus', Status::ONGOING)
        ->setParameter('endStatuses', [Status::CANCELED, Status::PAST]);

    $query = $qb->getQuery();

    return $query->getResult();
  }

  public function findByDefault(?User $user = null): array
  {
    $qb = $this->createQueryBuilder('o');
    $qb->leftJoin('o.attendees', 'a');
    $qb->leftJoin('o.host', 'h');

    if ($user) {
      $qb->where('o.status != :statusClosed')
          ->andWhere('(o.status != :statusCreated OR (o.status = :statusCreated AND h.id = :userId))')
          ->setParameter('statusClosed', Status::CLOSED)
          ->setParameter('statusCreated', Status::CREATED)
          ->setParameter('userId', $user->getId());
    } else {
      $qb->where('o.status NOT IN (:excludedStatuses)')
          ->setParameter('excludedStatuses', [Status::CLOSED, Status::CREATED]);
    }

    return $qb->getQuery()->getResult();
  }

  public function findByFilters(array $initialOutings, ?User $user, $campus, $title, $dateStart, $dateEnd, $isHost, $isEntered, $isNotEntered, $isPast): array
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
    if ($isPast) {
      $qb->andWhere('o.startAt < :now')
          ->setParameter('now', new \DateTime());
    }

    if ($user) {
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
            ->getDQL();

        $qb->andWhere(sprintf('NOT EXISTS (%s) AND h.id != :user', $subQuery))
            ->setParameter('user', $user->getId());
      }
    }

    return $qb->getQuery()->getResult();
  }
}
