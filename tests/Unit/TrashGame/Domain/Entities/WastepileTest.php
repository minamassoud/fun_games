<?php

use App\Domain\TrashGame\Domain\Entities\Wastepile;
use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\Rank;
use App\Domain\TrashGame\Domain\ValueObjects\Suit;

test('a wastepile that is originally empty can be created', function () {

    $wastepile = new Wastepile;

    expect($wastepile->count())->toBe(0);

});

test('a wastepile acts as a stack where we can push, pop and view the top', function () {

    $wastepile = new Wastepile;
    $card1 = Card::make(Rank::Ace, Suit::Club);
    $card2 = Card::make(Rank::Two, Suit::Club);

    $wastepile->push($card1);
    $wastepile->push($card2);

    $wastepile->pop();

    expect($wastepile->top())->toBe($card1);

    $wastepile->pop();

    expect($wastepile->top())->toBeNull();
});
