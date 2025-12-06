<?php

namespace Tests\DummyClasses;

use App\Domain\TrashGame\Domain\Contracts\DeckInterface;
use App\Domain\TrashGame\Domain\ValueObjects\Card;

class FixedDeck implements DeckInterface
{
    protected array $cards;

    public function __construct($cards)
    {
        $this->initForNewRound($cards);
    }

    public function initForNewRound($cards = []): void
    {
        $this->cards = $cards;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function deal(int $count = 1): array
    {
        return array_splice($this->cards, 0, $count);
    }

    public function draw(): Card
    {
        return array_shift($this->cards);
    }

    public function count(): int
    {
        return count($this->cards);
    }
}
