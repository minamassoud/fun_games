<?php

namespace App\Domain\TrashGame\Domain\Entities;

use App\Domain\TrashGame\Domain\Exceptions\InvalidAction;
use App\Domain\TrashGame\Domain\ValueObjects\Card;

class Layout
{
    /**
     * @param  array<Slot>  $slots
     */
    public function __construct(protected array $slots) {}

    public function getSlot(int $position): ?Slot
    {
        $index = $position - 1;

        if (isset($this->slots[$index])) {
            return $this->slots[$index];
        }

        return null;
    }

    public function slots(): array
    {
        return $this->slots;
    }

    public function facingDownSlotPositions(): array
    {
        $arr = [];

        foreach ($this->slots as $i => $slot) {
            if(!$slot->getCard()) {
                $arr[] = $i+1;
            }
        }

        return $arr;
    }

    public function placeCardAt(int $position, Card $newCard): Card
    {
        if (! $newCard->isWild() && $position !== $newCard->rank->value) {
            throw new InvalidAction("can not place a card with rank {$newCard->rank->value} in position: $position");
        }

        return $this->getSlot($position)->swapCard($newCard);
    }

    public function roundWon(): bool
    {
        foreach ($this->slots as $slot) {
            if ($slot->isFacingDown()) {
                return false;
            }
        }

        return true;
    }

    public function count(): int
    {
        return count($this->slots);
    }
}
