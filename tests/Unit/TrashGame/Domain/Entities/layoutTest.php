<?php

/**
 * A Layout has Many Slots.
 * It knows the position of the slots.
 * It can swap a card inside a slot through using the slot swap behavior.
 * The chain reaction happens (card replacements) on the layout level.
 * If at the end of the chain reaction the layout has all face up slots, then the round is won.
 */

use App\Domain\TrashGame\Domain\Entities\Layout;
use App\Domain\TrashGame\Domain\Entities\Slot;
use App\Domain\TrashGame\Domain\Exceptions\InvalidAction;
use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\Rank;
use App\Domain\TrashGame\Domain\ValueObjects\Suit;
use App\Domain\TrashGame\Infrastructure\SlotFactory;

beforeEach(function () {
    $this->slotFactory = new SlotFactory;
});

describe('Layout', function () {

    it('it initializes with a cards array', function () {
        $cards = [
            Card::make(Rank::Ace, Suit::Club),
            Card::make(Rank::Two, Suit::Club),
            Card::make(Rank::Three, Suit::Club),
        ];

        $layout = new Layout($cards, $this->slotFactory);

        expect($layout->count())->toBe(3);
    });

    it('it can get a slot at a certain position', function () {
        $cards = [
            Card::make(Rank::Ace, Suit::Club),
        ];

        $layout = new Layout($cards, $this->slotFactory);

        expect($layout->getSlot(1))->toBeInstanceOf(Slot::class);
    });

    it('will return null if it can not find a slot in the specified position', function () {
        $cards = [
            Card::make(Rank::Ace, Suit::Club),
        ];

        $layout = new Layout($cards, $this->slotFactory);

        expect($layout->getSlot(2))->toBeNull();
    });

    it('will stop a card placement in the wrong position', function () {
        $cards = [
            Card::make(Rank::Ace, Suit::Club),
            Card::make(Rank::Two, Suit::Club),
        ];

        $layout = new Layout($cards, $this->slotFactory);

        $layout->placeCardAt(2, Card::make(Rank::Three, Suit::Club));

    })->throws(InvalidAction::class);

    it('will swap the card through the slot', function () {
        $cards = [
            Card::make(Rank::Ace, Suit::Club),
            Card::make(Rank::King, Suit::Club),
        ];

        $layout = new Layout($cards, $this->slotFactory);

        $card = $layout->placeCardAt(2, Card::make(Rank::Two, Suit::Club));
        $card2 = $layout->placeCardAt(1, Card::make(Rank::Jack, Suit::Club));

        expect($card->rank)->toBe(Rank::King)
            ->and($card2->rank)->toBe(Rank::Ace);
    });

    test('it will return the same card if already the slot is face up', function () {

        $cards = [
            Card::make(Rank::Three, Suit::Club),
            Card::make(Rank::Two, Suit::Club),
            Card::make(Rank::King, Suit::Club),
        ];

        $layout = new Layout($cards, $this->slotFactory);
        $layout->getSlot(2)->reveal();

        $toDiscardCard = $layout->placeCardAt(2, Card::make(Rank::Two, Suit::Club));

        expect($layout->getSlot(1)->getCard())
            ->toBeNull()
            ->and($layout->getSlot(2)->getCard()->rank)->toBe(Rank::Two)
            ->and($layout->getSlot(3)->getCard())->toBeNull()
            ->and($toDiscardCard->rank)->toBe(Rank::Two);
    });

    it('detects a win if all slots are face up', function () {
        $cards = [
            new Card(Rank::Ace, Suit::Spade),
            new Card(Rank::Two, Suit::Spade),
        ];

        $layout = new Layout($cards, $this->slotFactory);

        $layout->getSlot(1)->reveal();
        expect($layout->roundWon())->toBeFalse('Should not win with 1/2 slots revealed');

        $layout->getSlot(2)->reveal();
        expect($layout->roundWon())->toBeTrue();
    });

});
