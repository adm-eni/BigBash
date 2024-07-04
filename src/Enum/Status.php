<?php

namespace App\Enum;

enum Status: string
{
  case CREATED = 'En création';
  case OPEN = 'Ouvert';
  case BOOKED = 'Complet';
  case ONGOING = 'En cours';
  case CANCELED = 'Annulé';
  case PAST = 'Terminé';
  case CLOSED = 'Clôturé';

  public static function fromString(string $status): self
  {
    foreach (self::cases() as $case) {
      if ($case->value === $status) {
        return $case;
      }
    }
    throw new \ValueError("Invalid status: $status");
  }
}