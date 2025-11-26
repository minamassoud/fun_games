<?php

namespace App\Domain\TrashGame\Domain\Contracts;

use App\Domain\TrashGame\Domain\Entities\Slot;
use App\Domain\TrashGame\Domain\ValueObjects\Card;

interface SlotFactoryInterface
{
    public function make(Card $card): Slot;
}
