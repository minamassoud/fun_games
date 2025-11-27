<?php

namespace App\Domain\TrashGame\Domain\Contracts;

use App\Domain\TrashGame\Domain\ValueObjects\Card;

interface DeckInterface
{
    public function getCards(): array;

    public function deal(int $count = 1): array;

    public function draw(): Card;

    public function count(): int;
}
