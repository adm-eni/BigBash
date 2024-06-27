<?php

namespace App\Form\Model;

use App\Entity\Campus;

class OutingsFilter
{
  private ?Campus $campusChoice = null;
  private ?string $titleSearch = null;
  private ?\DateTime $startDate = null;
  private ?\DateTime $endDate = null;
  private ?bool $isHost = false;
  private ?bool $isEntered = false;
  private ?bool $isNotEntered = false;
  private ?bool $isPast = false;

  public function getCampusChoice(): ?Campus
  {
    return $this->campusChoice;
  }

  public function setCampusChoice(?Campus $campusChoice): OutingsFilter
  {
    $this->campusChoice = $campusChoice;
    return $this;
  }

  public function getTitleSearch(): ?string
  {
    return $this->titleSearch;
  }

  public function setTitleSearch(?string $titleSearch): OutingsFilter
  {
    $this->titleSearch = $titleSearch;
    return $this;
  }

  public function getStartDate(): ?\DateTime
  {
    return $this->startDate;
  }

  public function setStartDate(?\DateTime $startDate): OutingsFilter
  {
    $this->startDate = $startDate;
    return $this;
  }

  public function getEndDate(): ?\DateTime
  {
    return $this->endDate;
  }

  public function setEndDate(?\DateTime $endDate): OutingsFilter
  {
    $this->endDate = $endDate;
    return $this;
  }

  public function getIsHost(): ?bool
  {
    return $this->isHost;
  }

  public function setIsHost(?bool $isHost): OutingsFilter
  {
    $this->isHost = $isHost;
    return $this;
  }

  public function getIsEntered(): ?bool
  {
    return $this->isEntered;
  }

  public function setIsEntered(?bool $isEntered): OutingsFilter
  {
    $this->isEntered = $isEntered;
    return $this;
  }

  public function getIsNotEntered(): ?bool
  {
    return $this->isNotEntered;
  }

  public function setIsNotEntered(?bool $isNotEntered): OutingsFilter
  {
    $this->isNotEntered = $isNotEntered;
    return $this;
  }

  public function getIsPast(): ?bool
  {
    return $this->isPast;
  }

  public function setIsPast(?bool $isPast): OutingsFilter
  {
    $this->isPast = $isPast;
    return $this;
  }
}