<?php

namespace App\Service;

use App\Form\Model\OutingsFilter;
use App\Repository\OutingRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class OutingService
{
  private OutingRepository $outingRepo;
  private UserInterface $user;

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

// In OutingService.php

  public function updateOutingStatuses(): void
  {
    $outings = $this->outingRepo->findOutingsToUpdate();
    $now = new \DateTime();

    $ongoingStatus = $this->outingRepo->findStatusByName('En cours');
    $pastStatus = $this->outingRepo->findStatusByName('Passé');

    foreach ($outings as $outing) {
      $startAt = $outing->getStartAt();
      $endAt = $outing->getEndAt();

      if ($now >= $startAt && $now < $endAt) {
        $outing->setStatus($ongoingStatus);
      } elseif ($now >= $endAt) {
        $outing->setStatus($pastStatus);
      }
    }

    $this->outingRepo->getEntityManager()->flush();
  }
}
