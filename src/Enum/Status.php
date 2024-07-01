<?php

namespace App\Enum;

enum Status: string
{
  case CREATED = 'En création';
  case OPEN = 'Ouvert';
  case ONGOING = 'En cours';
  case CLOSED = 'Clôturé';
  case PAST = 'Passé';
  case CANCELED = 'Annulé';

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