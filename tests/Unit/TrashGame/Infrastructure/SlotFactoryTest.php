<?php

use App\Domain\TrashGame\Domain\Entities\Slot;
use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\Rank;
use App\Domain\TrashGame\Domain\ValueObjects\Suit;
use App\Domain\TrashGame\Infrastructure\SlotFactory;

it('creates a slot from a card', function () {

    $factory = new SlotFactory;
    $card = Card::make(Rank::Jack, Suit::Club);

    $slot = $factory->make($card);

    expect($slot)->toBeInstanceOf(Slot::class)
        ->and($slot->reveal()->getCard())->toBe($card);

});
