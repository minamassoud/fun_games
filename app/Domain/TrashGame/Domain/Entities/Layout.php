<?php

namespace App\Domain\TrashGame\Domain\Entities;

use App\Domain\TrashGame\Domain\Contracts\SlotFactoryInterface;
use App\Domain\TrashGame\Domain\Exceptions\InvalidAction;
use App\Domain\TrashGame\Domain\ValueObjects\Card;

class Layout
{
    /** @var array<Slot> */
    protected array $slots;

    public function __construct(array $cards, protected SlotFactoryInterface $slotFactory)
    {
        foreach ($cards as $card) {
            $this->slots[] = $this->slotFactory->make($card);
        }
    }

    public function getSlot(int $position): ?Slot
    {
        $index = $position - 1;

        if (isset($this->slots[$index])) {
            return $this->slots[$index];
        }

        return null;
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
