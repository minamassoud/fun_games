<?php

use App\Domain\TrashGame\Domain\Entities\Layout;
use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\Rank;
use App\Domain\TrashGame\Domain\ValueObjects\Suit;
use App\Domain\TrashGame\Infrastructure\LayoutFactory;
use App\Domain\TrashGame\Infrastructure\SlotFactory;

test('a layout factory can instantiate a layout', function () {
    $factory = new LayoutFactory(new SlotFactory);
    $layout = $factory->make([Card::make(Rank::Jack, Suit::Club)]);

    expect($layout)->toBeInstanceOf(Layout::class);
});
