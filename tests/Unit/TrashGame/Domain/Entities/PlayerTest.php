<?php

/**
 * A player should be aware of his name and level.
 * A player should set his layout according to his level
 * A player should be able to receive a card from the game.
 * A player should be able to pass the card to the layout for processing.
 */

use App\Domain\TrashGame\Domain\Entities\Layout;
use App\Domain\TrashGame\Domain\Entities\Player;
use App\Domain\TrashGame\Domain\Exceptions\InvalidAction;
use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\Rank;
use App\Domain\TrashGame\Domain\ValueObjects\Suit;
use App\Domain\TrashGame\Infrastructure\SlotFactory;

beforeEach(function () {
    $this->slotFactory = new SlotFactory;
});

test('A player should be instantiated using a name and a level', function () {
    $player = new Player('mina', 10);

    expect($player)->toBeInstanceOf(Player::class)
        ->and($player->getLevel())->toBe(10)
        ->and($player->name)->toBe('mina');
});

test('we should be able to set the level of the player', function () {
    $player = new Player('mina', 10);
    $player->setLevel(9);

    expect($player->getLevel())->toBe(9);
});

test('a player should have a layout of cards according to his level', function () {

    $player = new Player('Computer', 9);

    $player->setLayout(new Layout(
        array_fill(0, 9, Card::make(Rank::Ace, Suit::Heart)),
        $this->slotFactory
    ));

    expect($player->getLayout()->count())->toBe(9);
});

test('a player can not have a layout of cards diff than his level', function () {

    $player = new Player('Computer', 9);

    $player->setLayout(new Layout(
        array_fill(0, 10, Card::make(Rank::Ace, Suit::Heart)),
        $this->slotFactory
    ));

})->throws(InvalidAction::class);

test('a player should be able to receive a card and know what is it', function () {
    $player = new Player('Human', 10);
    $player->setLayout(new Layout(array_fill(0, 10, Card::make(Rank::Ace, Suit::Heart)), $this->slotFactory));

    $card = Card::make(Rank::Ace, Suit::Heart);
    $player->receiveCard($card);

    expect($player->heldCard())->toBe($card);
});

test('Player should not be able to receive a card if he has a card already', function () {
    $player = new Player('Human', 10);
    $player->setLayout(new Layout(array_fill(0, 10, Card::make(Rank::Ace, Suit::Heart)), $this->slotFactory));

    $card = Card::make(Rank::Ace, Suit::Heart);
    $player->receiveCard($card);
    $player->receiveCard($card);

})->throws(InvalidAction::class);

test('a player should be able to play his held card', function () {
    $cards = [
        Card::make(Rank::King, Suit::Heart),
        Card::make(Rank::Three, Suit::Heart),
    ];
    $player = new Player('Human', 2);
    $player = $player->setLayout(new Layout($cards, $this->slotFactory));

    $card = Card::make(Rank::Ace, Suit::Heart);
    $player->receiveCard($card);
    $card = $player->playHeldCard();

    expect($card->rank)->toBe(Rank::King);
});

test('A player must specify a position if he is holding a wild card', function () {
    $cards = [
        Card::make(Rank::Ace, Suit::Heart),
        Card::make(Rank::Three, Suit::Heart),
    ];
    $player = new Player('Human', 2);
    $player = $player->setLayout(new Layout($cards, $this->slotFactory));

    $card = Card::make(Rank::Jack, Suit::Heart);
    $player->receiveCard($card);
    $player->playHeldCard(1);
    $player->playHeldCard();
    $player->playHeldCard();

})->throws(InvalidAction::class);

test('a Player cannot play a card if he does not have a held card', function () {
    $cards = [
        Card::make(Rank::King, Suit::Heart),
        Card::make(Rank::Three, Suit::Heart),
    ];
    $player = new Player('Human', 2);
    $player = $player->setLayout(new Layout($cards, $this->slotFactory));
    $player->playHeldCard();

})->throws(InvalidAction::class);

test('A player should be able to discard his held card', function () {
    $cards = [
        Card::make(Rank::King, Suit::Heart),
        Card::make(Rank::Three, Suit::Heart),
    ];
    $player = new Player('Human', 2);
    $player = $player->setLayout(new Layout($cards, $this->slotFactory));
    $card = Card::make(Rank::Ace, Suit::Heart);
    $player->receiveCard($card);
    $player->discardCard();

    expect($player->hasHeldCard())->toBeFalse();
});

test('A player can not discard a card if he is not holding anything', function () {
    $cards = [
        Card::make(Rank::King, Suit::Heart),
        Card::make(Rank::Three, Suit::Heart),
    ];
    $player = new Player('Human', 2);
    $player = $player->setLayout(new Layout($cards, $this->slotFactory));
    $player->discardCard();

})->throws(InvalidAction::class);
