<?php

namespace App\Domain\TrashGame\Domain\ValueObjects;

enum CardSource: string
{
    case Deck = 'd';
    case Wastepile = 'w';
}
