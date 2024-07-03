<?php

namespace App\Service;

use App\Entity\Outing;
use App\Enum\Status;
use App\Exception\OutingStatusException;
use App\Form\Model\OutingsFilter;
use App\Repository\OutingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class OutingService extends AbstractController
{
  private OutingRepository $outingRepo;

  public function __construct(OutingRepository $outingRepo)
  {
    $this->outingRepo = $outingRepo;
  }

  public function getDefaultFilteredOutings(?UserInterface $user = null): array
  {
    return $this->outingRepo->findByDefault($user);
  }

  public function getAllOutings(): array
  {
    return $this->outingRepo->findAll();
  }

  public function getOuting(int $id): ?Outing
  {
    return $this->outingRepo->find($id);
  }

  public function getFormFilteredOutings(array $outings, OutingsFilter $filter, ?UserInterface $user = null): array
  {
    $isHost = $user ? $filter->getIsHost() : null;
    $isEntered = $user ? $filter->getIsEntered() : null;
    $isNotEntered = $user ? $filter->getIsNotEntered() : null;

    return $this->outingRepo->findByFilters(
        $outings,
        $user,
        $filter->getCampusChoice(),
        $filter->getTitleSearch(),
        $filter->getStartDate(),
        $filter->getEndDate(),
        $isHost,
        $isEntered,
        $isNotEntered,
        $filter->getIsPast()
    );
  }

  public function updateOutingStatuses(): void
  {
    $outings = $this->outingRepo->findOutingsToUpdate();

    foreach ($outings as $outing) {
      if ($outing->getStatus() === Status::OPEN) $outing->setStatus(Status::ONGOING);
      elseif ($outing->getStatus() === Status::ONGOING) $outing->setStatus(Status::PAST);
      else $outing->setStatus(Status::CLOSED);
    }
    $this->outingRepo->getEntityManager()->flush();
  }

    /**
     * @throws OutingStatusException
     */
    public function checkOutingStatus(Outing $outing,
                                      bool   $closed,
                                      bool   $ongoing,
                                      bool   $past,
                                      bool   $canceled,
                                      bool   $open,
                                      bool   $created): void
    {
        $status = $outing->getStatus();

        if ($closed && $status === Status::CLOSED) {
            throw new OutingStatusException('Cette sortie a été clôturée.');
        }
        if ($ongoing && $status === Status::ONGOING) {
            throw new OutingStatusException('Cette sortie est en cours.');
        }
        if ($past && $status === Status::PAST) {
            throw new OutingStatusException('Cette sortie est terminée.');
        }
        if ($canceled && $status === Status::CANCELED) {
            throw new OutingStatusException('Cette sortie a été annulée.');
        }
        if ($open && $status === Status::OPEN) {
            throw new OutingStatusException('Cette sortie est déjà publiée.');
        }
        if ($created && $status === Status::CREATED) {
            throw new OutingStatusException('Cette sortie est en cours de création.');
        }
    }

    public function deleteOuting(Outing $outing): void
    {
        $this->outingRepo->getEntityManager()->remove($outing);
        $this->outingRepo->getEntityManager()->flush();
    }
}