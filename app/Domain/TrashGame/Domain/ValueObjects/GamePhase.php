<?php

namespace App\Domain\TrashGame\Domain\ValueObjects;

enum GamePhase
{
    case GameStart;
    case WaitingForDraw;
    case WaitingCardPlacement;
    case RoundWon;
    case GameWon;
}
