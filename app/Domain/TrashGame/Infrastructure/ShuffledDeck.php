<?php

namespace App\Domain\TrashGame\Infrastructure;

use App\Domain\TrashGame\Domain\Contracts\DeckInterface;
use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\Rank;
use App\Domain\TrashGame\Domain\ValueObjects\Suit;

class ShuffledDeck implements DeckInterface
{
    protected array $cards;

    public function __construct()
    {
        $this->initForNewRound();
    }

    public function initForNewRound(): void
    {
        $this->cards = [];

        foreach (Rank::cases() as $rank) {
            foreach (Suit::cases() as $suite) {
                $this->cards[] = Card::make($rank, $suite);
            }
        }

        shuffle($this->cards);
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function deal(int $count = 1): array
    {
        return array_splice($this->cards, -$count);
    }

    public function draw(): Card
    {
        return array_pop($this->cards);
    }

    public function count(): int
    {
        return count($this->cards);
    }
}
