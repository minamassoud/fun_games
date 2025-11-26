<?php

namespace App\Domain\TrashGame\Infrastructure;

use App\Domain\TrashGame\Domain\Contracts\SlotFactoryInterface;
use App\Domain\TrashGame\Domain\Entities\Slot;
use App\Domain\TrashGame\Domain\ValueObjects\Card;

class SlotFactory implements SlotFactoryInterface
{
    public function make(Card $card): Slot
    {
        return new Slot($card);
    }
}
