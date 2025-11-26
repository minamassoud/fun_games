<?php

namespace App\Domain\TrashGame\Domain\Entities;

use App\Domain\TrashGame\Domain\Exceptions\InvalidAction;
use App\Domain\TrashGame\Domain\ValueObjects\Card;

class Player
{
    protected Layout $layout;

    protected ?Card $heldCard = null;

    public function __construct(
        public readonly string $name,
        protected int $level
    ) {}

    public function setLayout(Layout $layout): self
    {
        if ($layout->count() != $this->level) {
            throw new InvalidAction('Given Layout is not consistent with the player level');
        }
        $this->layout = $layout;

        return $this;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function receiveCard(Card $card): self
    {
        if ($this->hasHeldCard()) {
            throw new InvalidAction('Player already has a card in hand');
        }

        $this->heldCard = $card;

        return $this;
    }

    public function heldCard(): ?Card
    {
        return $this->heldCard;
    }

    public function hasHeldCard(): bool
    {
        return (bool) $this->heldCard;
    }

    public function playHeldCard(?int $targetPosition = null): Card
    {
        if (! $this->hasHeldCard()) {
            throw new InvalidAction('Player does not have a card in hand');
        }

        if ($this->heldCard->isWild()) {
            if ($targetPosition === null) {
                throw new InvalidAction('Player must provide a target position to play');
            }
            $position = $targetPosition;
        } else {
            $position = $this->heldCard->rank->value;
        }

        return $this->heldCard = $this->getLayout()->placeCardAt($position, $this->heldCard);
    }

    public function discardCard(): Card
    {
        if (! $this->hasHeldCard()) {
            throw new InvalidAction('Player must have a card in hand');
        }

        $card = $this->heldCard;
        $this->heldCard = null;

        return $card;
    }
}
