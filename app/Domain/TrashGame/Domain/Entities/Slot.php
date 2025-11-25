<?php

namespace App\Domain\TrashGame\Domain\Entities;

use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\CardOrientation;

class Slot
{
    public function __construct(
        protected Card $card,
        protected CardOrientation $orientation = CardOrientation::FaceDown
    ) {}

    public function isFacingDown(): bool
    {
        return $this->orientation == CardOrientation::FaceDown;
    }

    public function getCard(): ?Card
    {
        if (! $this->isFacingDown()) {
            return $this->card;
        }

        return null;
    }

    public function reveal(): self
    {
        $this->orientation = CardOrientation::FaceUp;

        return $this;
    }

    public function swapCard(Card $card): Card
    {
        $oldCard = $this->reveal()->getCard();
        $this->card = $card;

        return $oldCard;
    }
}
