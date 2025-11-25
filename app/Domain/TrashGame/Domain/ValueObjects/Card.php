<?php

namespace App\Domain\TrashGame\Domain\ValueObjects;

final readonly class Card
{
    public function __construct(
        public Rank $rank,
        public Suit $suit
    ) {}

    public static function make(Rank $rank, Suit $suit): self
    {
        return new self($rank, $suit);
    }

    public function isWild(): bool
    {
        return $this->rank == Rank::Jack;
    }

    public function isGarbage(): bool
    {
        return in_array($this->rank, [Rank::King, Rank::Queen]);
    }
}
