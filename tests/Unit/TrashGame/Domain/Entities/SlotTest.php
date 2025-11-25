<?php

/**
 * A slot is a space on the layout that can hold only one card
 * The layout is the domain entity that can interact with the slot.
 */

use App\Domain\TrashGame\Domain\Entities\Slot;
use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\CardOrientation;
use App\Domain\TrashGame\Domain\ValueObjects\Rank;
use App\Domain\TrashGame\Domain\ValueObjects\Suit;

describe('Slot', function () {

    it('can be instantiated with a card inside with default face down orientation', function () {
        $slot = new Slot(Card::make(Rank::Ace, Suit::Heart));

        expect($slot)->toBeInstanceOf(Slot::class)
            ->and($slot->isFacingDown())->toBeTrue();
    });

    it('can not show the card as long as its facing down', function () {
        $slot = new Slot(Card::make(Rank::Ace, Suit::Heart));

        expect($slot->getCard())->toBeNull();
    });

    it('can show the card as long as its facing up', function () {
        $slot = new Slot(Card::make(Rank::Ace, Suit::Heart), CardOrientation::FaceUp);

        expect($slot->getCard())->toBeInstanceOf(Card::class)
            ->and($slot->getCard()->rank)->toBe(Rank::Ace);
    });

    it('can reveal a facing down card', function () {
        $slot = new Slot(Card::make(Rank::Ace, Suit::Heart));
        $slot->reveal();

        expect($slot->isFacingDown())->toBeFalse()->and($slot->getCard())->toBeInstanceOf(Card::class);
    });

    it('can swap card with a given card and return the swapped card', function () {
        $slot = new Slot(Card::make(Rank::Ace, Suit::Heart));
        $card = $slot->swapCard(Card::make(Rank::King, Suit::Heart));

        expect($card->rank)->toBe(Rank::Ace)->and($slot->getCard()->rank)->toBe(Rank::King);
    });

});
