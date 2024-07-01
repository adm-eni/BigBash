<?php

namespace App\Service;

use App\Enum\Status;
use App\Form\Model\OutingsFilter;
use App\Repository\OutingRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class OutingService
{
  private OutingRepository $outingRepo;

  public function __construct(OutingRepository $outingRepo)
  {
    $this->outingRepo = $outingRepo;
  }

  public function getDefaultOutings(UserInterface $user): array
  {
    return $this->outingRepo->findByDefault($user);
  }

  public function getOutings(): array
  {
    return $this->outingRepo->findAll();
  }

  public function getFilteredOutings(array $outings, UserInterface $user, OutingsFilter $filter): array
  {

    return $this->outingRepo->findByFilters(
        $outings,
        $user,
        $filter->getCampusChoice(),
        $filter->getTitleSearch(),
        $filter->getStartDate(),
        $filter->getEndDate(),
        $filter->getIsHost(),
        $filter->getIsEntered(),
        $filter->getIsNotEntered(),
        $filter->getIsPast()
    );
  }

  public function updateOutingStatuses(): void
  {
    $outings = $this->outingRepo->findOutingsToUpdate();
    $now = new \DateTime();

    foreach ($outings as $outing) {
      $startAt = $outing->getStartAt();
      $endAt = $outing->getEndAt();

      if ($startAt <= $now && $now < $endAt) {
        $outing->setStatus(Status::ONGOING);
      } elseif ($endAt <= $now) {
        $outing->setStatus(Status::PAST);
      }
    }
    $this->outingRepo->getEntityManager()->flush();
  }
}