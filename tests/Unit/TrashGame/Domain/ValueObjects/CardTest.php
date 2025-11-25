<?php

/**
 * the Card is just a value object that will hold a card info
 * as a rank, suit
 */

use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\Rank;
use App\Domain\TrashGame\Domain\ValueObjects\Suit;

test('A card can be created statically or with the new keyword', function () {
    $card = new Card(Rank::Ace, Suit::Heart);
    $card2 = Card::make(Rank::Five, Suit::Heart);

    expect($card)
        ->toBeInstanceOf(Card::class)
        ->and($card2)
        ->toBeInstanceOf(Card::class);
});

test('Once a card is set the Rank and the Suit can not be changed', function () {
    $card = new Card(Rank::Ace, Suit::Heart);
    $card->rank = Rank::King;
    $card->suit = Suit::Club;
})->throws(Error::class);

test('if a card is a Jack it is a wild card', function () {
    $card = Card::make(Rank::Jack, Suit::Heart);

    expect($card->isWild())->toBeTrue();
});

test('if a card is a King or Queen it is a garbage card', function () {
    $card = Card::make(Rank::King, Suit::Heart);
    $card2 = Card::make(Rank::Queen, Suit::Heart);

    expect($card->isGarbage())->toBeTrue()->and($card2->isGarbage())->toBeTrue();
});
