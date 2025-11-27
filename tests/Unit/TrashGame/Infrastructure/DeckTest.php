<?php

use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Infrastructure\ShuffledDeck;

test('a deck of cards should have 52 cards', function () {
    $deck = new ShuffledDeck;
    expect($deck->count())->toBe(52);
});

test('A deck can get all its cards', function () {
    $deck = new ShuffledDeck;
    expect(count($deck->getCards()))->toBe(52);
});

test('A deck can draw only one card', function () {
    $deck = new ShuffledDeck;
    $card = $deck->draw();
    expect($card)->toBeInstanceOf(Card::class)->and($deck->count())->toBe(51);
});

test('A deck can deal many cards', function () {
    $deck = new ShuffledDeck;
    $cards = $deck->deal(10);
    expect(count($cards))->toBe(10)->and($deck->count())->toBe(42);
});
