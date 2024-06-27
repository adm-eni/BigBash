<?php

namespace App\Service;

use App\Entity\User;
use App\Form\Model\OutingsFilter;
use App\Repository\OutingRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class FilterService
{
  private OutingRepository $outingRepo;

  public function __construct(OutingRepository $outingRepo)
  {
    $this->outingRepo = $outingRepo;
  }

  public function filterOutings(OutingsFilter $filter, UserInterface $user): array
  {
    return $this->outingRepo->findByFilters(
        $filter->getCampusChoice(),
        $filter->getTitleSearch(),
        $filter->getStartDate(),
        $filter->getEndDate(),
        $filter->getIsHost(),
        $filter->getIsEntered(),
        $filter->getIsNotEntered(),
        $filter->getIsPast(),
        $user
    );
  }
}