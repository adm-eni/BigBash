<?php

namespace App\Service;

use App\Form\Model\OutingsFilter;
use App\Repository\OutingRepository;

class FilterService
{
  private OutingRepository $outingRepo;

  public function __construct(OutingRepository $outingRepo)
  {
    $this->outingRepo = $outingRepo;
  }

  public function filterOutings(OutingsFilter $filter): array
  {
    return $this->outingRepo->findByFilters(
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
}