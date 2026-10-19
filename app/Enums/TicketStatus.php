<?php

namespace App\Enums;

enum TicketStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Waiting = 'waiting';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Nuevo',
            self::InProgress => 'En proceso',
            self::Waiting => 'En espera',
            self::Resolved => 'Resuelto',
            self::Closed => 'Cerrado',
        };
    }
}
