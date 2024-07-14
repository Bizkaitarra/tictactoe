<?php

namespace App\Infrastructure;

use App\Domain\Board;
use App\Domain\Player;

final class RandomPlayer extends Player
{
    public function getMove(Board $board): int
    {
        while (true) {
            $move = rand(0, 8);
            if (!$board->isAlreadyFilled($move)) {
                return $move;
            }
        }
    }

    public function gameHasFinished(Board $board): void
    {
    }
}