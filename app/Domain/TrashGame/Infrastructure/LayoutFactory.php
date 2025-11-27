<?php

namespace App\Domain\TrashGame\Infrastructure;

use App\Domain\TrashGame\Domain\Contracts\LayoutFactoryInterface;
use App\Domain\TrashGame\Domain\Contracts\SlotFactoryInterface;
use App\Domain\TrashGame\Domain\Entities\Layout;

final readonly class LayoutFactory implements LayoutFactoryInterface
{
    public function __construct(
        private SlotFactoryInterface $slotFactory
    ) {}

    public function make(array $cards): Layout
    {
        $slots = [];

        foreach ($cards as $card) {
            $slots[] = $this->slotFactory->make($card);
        }

        return new Layout($slots);
    }
}
