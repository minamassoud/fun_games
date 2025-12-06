<?php

namespace App\Domain\TrashGame\Domain\Entities;

use App\Domain\TrashGame\Domain\Contracts\DeckInterface;
use App\Domain\TrashGame\Domain\Contracts\LayoutFactoryInterface;
use App\Domain\TrashGame\Domain\Exceptions\InvalidAction;
use App\Domain\TrashGame\Domain\ValueObjects\Card;
use App\Domain\TrashGame\Domain\ValueObjects\CardSource;
use App\Domain\TrashGame\Domain\ValueObjects\GamePhase;
use InvalidArgumentException;

class Game
{
    private Wastepile $wastePile;

    private int $currentPlayerIndex = 0;

    private int $currentRound = 0;

    private GamePhase $phase = GamePhase::GameStart;

    /**
     * @param  array<Player>  $players
     */
    public function __construct(
        protected array $players,
        protected DeckInterface $deck,
        protected LayoutFactoryInterface $layoutFactory,
    ) {
        $playersCount = count($players);

        if ($playersCount < 1 || $playersCount > 3) {
            throw new InvalidArgumentException("The Game can not start with {$playersCount} players");
        }

        $this->wastePile = new Wastepile;
    }

    public function players(): array
    {
        return $this->players;
    }
    public function player(int $index): Player
    {
        return $this->players[$index];
    }

    public function currentPlayer(): Player
    {
        return $this->player($this->currentPlayerIndex);
    }

    public function phase(): GamePhase
    {
        return $this->phase;
    }

    public function deck(): DeckInterface
    {
        return $this->deck;
    }

    public function wastePile(): Wastepile
    {
        return $this->wastePile;
    }

    public function roundIsOver(): bool
    {
        foreach ($this->players as $player) {
            if($player->getLayout()->roundWon()) {
                return true;
            }
        }
        return false;
    }

    public function isOver(): bool
    {
        foreach ($this->players as $player) {
            if($player->getLevel() === 0 && $player->getLayout()->roundWon()) {
                return true;
            }
        }
        return false;
    }

    public function startRound(): void
    {
        // Initialize the round count
        $this->currentRound++;

        // Once the round start we need a fresh shuffled deck
        // and an empty wastepile
        $this->deck->initForNewRound();
        $this->wastePile = new Wastepile;

        // initialize the layouts
        $this->initializeLayouts();

        // Decides who is the current player of the round
        $this->currentPlayerIndex = ($this->currentRound + (count($this->players) - 1)) % count($this->players);

        // Changes the phase so the current player can draw
        $this->phase = GamePhase::WaitingForDraw;
    }

    public function initializeLayouts(): void
    {
        foreach ($this->players as $player) {
            $player->setLayout(
                $this->layoutFactory->make(
                    $this->deck->deal($player->getLevel()),
                )
            );
        }
    }

    public function drawCard(CardSource $source): void
    {
        if ($this->phase !== GamePhase::WaitingForDraw) {
            throw new InvalidAction("Can not draw on that phase: {$this->phase->name}");
        }

        if ($source == CardSource::Wastepile) {

            if ($this->wastePile->top() === null) {
                throw new InvalidAction('Player can not take a card from an empty wastepile');
            }

            if (!$this->isCardPlayable($this->wastePile->top())) {
                throw new InvalidAction('This card is not playable for the current player');
            }

            $this->currentPlayer()->receiveCard($this->wastePile->pop());

        } else {
            $this->currentPlayer()->receiveCard($this->deck->draw());
        }


        $this->phase = GamePhase::WaitingCardPlacement;
    }

    public function automaticCardDraw(): void
    {
        if ($this->phase !== GamePhase::WaitingForDraw) {
            throw new InvalidAction("Can not draw on that phase: {$this->phase->name}");
        }

        if (!$this->wastePile->top() || !$this->isCardPlayable($this->wastePile->top())) {
            $this->currentPlayer()->receiveCard($this->deck->draw());
        } else {
            $this->currentPlayer()->receiveCard($this->wastePile->pop());
        }

        $this->phase = GamePhase::WaitingCardPlacement;
    }

    public function isCardPlayable(?Card $card = null): bool
    {
        $player = $this->currentPlayer();

        if(!$card) {
            $card = $player->heldCard();
        }

        if ($card->isGarbage()) {
            return false;
        }

        if ($card->isWild()) {
            return true;
        }

        $slotPosition = $card->rank->value;
        $slot = $player->getLayout()->getSlot($slotPosition);

        if($slot?->getCard()?->isWild()) {
            return true;
        }

        return (bool) $slot?->isFacingDown();
    }

    public function placeCard($position = null): void
    {
        if ($this->phase !== GamePhase::WaitingCardPlacement) {
            throw new InvalidAction("Can not place card on that phase: {$this->phase->name}");
        }

        $this->currentPlayer()->playHeldCard($position);
    }

    public function endPlayerTurn(): void
    {
        $this->wastePile->push($this->currentPlayer()->discardCard());

        if($this->currentPlayer()->getLayout()->roundWon()) {
            $this->currentPlayer()->decLevel();
        }

        $this->currentPlayerIndex = ($this->currentPlayerIndex + 1) % count($this->players);
        $this->phase = GamePhase::WaitingForDraw;
    }

    public function winner(): ?Player
    {
        foreach ($this->players as $player) {
            if($player->getLevel() === 0) {
                return $player;
            }
        }

        return null;
    }
}
