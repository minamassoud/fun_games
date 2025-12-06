<?php

namespace App\Console\Commands;

use App\Domain\TrashGame\Domain\Entities\Game;
use App\Domain\TrashGame\Domain\Entities\Player;
use App\Domain\TrashGame\Domain\Exceptions\InvalidAction;
use App\Domain\TrashGame\Domain\ValueObjects\CardSource;
use App\Domain\TrashGame\Domain\ValueObjects\GamePhase;
use App\Domain\TrashGame\Infrastructure\LayoutFactory;
use App\Domain\TrashGame\Infrastructure\ShuffledDeck;
use App\Domain\TrashGame\Infrastructure\SlotFactory;
use Illuminate\Console\Command;

class PlayTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:play-trash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'the console command for the game Trash';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $playerName = $this->ask('what is your name?');

        $player = new Player($playerName, 10);
        $computer = new Player('Computer', 10);

        $game = new Game([$player, $computer], new ShuffledDeck, new LayoutFactory(new SlotFactory));

        while (! $game->isOver()) {

            // the current Phase should be waiting for a draw
            $game->startRound();
            $this->alert('Starting new Round');

            while (! $game->roundIsOver()) {

                $currentPlayer = $game->currentPlayer();
                $this->info('------------------------------------------------');
                $this->alert('Current Player: '.$currentPlayer->name. " level ". $currentPlayer->getLevel());

                $this->renderBoard($currentPlayer);

                // give the player the option to draw a card from the wastepile or the deck
                // once he chooses the game phase should change to awaiting placement
                $this->tryActionWithRetry(function () use ($game, $currentPlayer) {
                    $wastepileCard = $this->renderWastepileTopCard($game);

                    if($currentPlayer->name === 'Computer') {
                        $game->automaticCardDraw();
                    } else {
                        $source = $this->choice("{$currentPlayer->name}, will you draw from the WastePile or the Deck",
                            ['d' => 'Deck', 'w' => "WastePile (Card: $wastepileCard )"]);

                        $game->drawCard(CardSource::from($source));
                    }
                });

                while ($game->phase() === GamePhase::WaitingCardPlacement) {

                    $heldCard = $currentPlayer->heldCard();
                    $this->info("You are holding: $heldCard");

                    if ($currentPlayer->getLayout()->roundWon()) {
                        $this->renderBoard($currentPlayer);
                        $this->alert("Round Won by Player: {$currentPlayer->name}");
                        $this->line('------------------------------------------------');
                        $game->endPlayerTurn();
                        break;
                    }

                    if (! $game->isCardPlayable()) {
                        $this->error('This card is not playable, will be discarded');
                        $this->renderBoard($currentPlayer);
                        $this->info("End of turn");
                        $this->info("------------------------------------------------");
                        $this->line('');
                        $game->endPlayerTurn();
                        sleep(1);
                        break;
                    }

                    if ($heldCard->isWild()) {
                        $availablePositions = $currentPlayer->getLayout()->facingDownSlotPositions();

                        if($currentPlayer->name === 'Computer') {
                            $this->info("Wild Card obtained, Auto-placing into slot {$availablePositions[0]} ...");
                            $targetSlot = $availablePositions[0];
                        } else {
                            $targetSlot = $this->choice('Where would you like to place this card', $availablePositions);
                        }

                    } else {
                        // an automatic placement of the card according to its rank
                        $targetSlot = null;
                        $this->line("Auto-placing into slot {$heldCard->rank->value} ...");
                        sleep(1);
                    }

                    $game->placeCard($targetSlot);
                }

            }

        }

        $this->alert('Game Over');
        $this->alert('Winner is '.$game->winner()?->name);
    }

    public function renderWastepileTopCard(Game $game): string
    {
        $topCard = $game->wastePile()->top();

        if ($topCard) {
            return (string) $topCard;
        }

        return '__';
    }

    public function renderBoard(Player $player): void
    {
        $board = [];
        foreach ($player->getLayout()->slots() as $slot) {
            if ($card = $slot->getCard()) {
                $board[] = (string) $card;
            } else {
                $board[] = '__';
            }
        }

        $this->line('Current Board Layout: ');
        $this->line(implode(', ', $board));
    }

    public function tryActionWithRetry(\Closure $action): void
    {
        $result = false;

        while (! $result) {
            try {
                $action();
                $result = true;
            } catch (InvalidAction $a) {
                $this->error($a->getMessage());
            }
        }
    }
}
