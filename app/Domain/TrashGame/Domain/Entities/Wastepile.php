<?php

namespace App\Domain\TrashGame\Domain\Entities;

use App\Domain\TrashGame\Domain\ValueObjects\Card;

class Wastepile
{
    public function __construct(protected array $cards = []) {}

    public function count(): int
    {
        return count($this->cards);
    }

    public function push(Card $card): void
    {
        $this->cards[] = $card;
    }

    public function pop()
    {
        return array_pop($this->cards);
    }

    public function top(): ?Card
    {
        if (empty($this->cards)) {
            return null;
        }

        return end($this->cards);
    }
}
