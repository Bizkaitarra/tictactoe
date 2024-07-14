<?php

namespace App\Infrastructure;

use App\Domain\Board;
use App\Domain\Player;

final class SimpleIAPlayer extends Player
{
    const POSITIVE_KNOWLEDGE = 1;
    const NEGATIVE_KNOWLEDGE = -1;

    private array $knowledge = [];

    public function __construct()
    {
        if (file_exists('knowledge.json')) {
            $this->knowledge = json_decode(file_get_contents('knowledge.json'), true);
        }
    }


    public function getMove(Board $board): int
    {
        $risk = rand(0, 100);
        if ($risk < 10 || !isset($this->knowledge[$board->getBoard()])) {
            return $this->getRandomMove($board);
        }
        $moves = $this->knowledge[$board->getBoard()];
        $bestMovesValue = max($moves);
        $positions = array_keys($moves, $bestMovesValue);
        return $positions[array_rand($positions)];
    }

    private function getRandomMove(Board $board): int
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
        $isWinnerMove = $board->getWinner() === $board::PLAYER_1;
        $moves = $board->getMoves();
        for ($i = 0; $i < count($moves); $i++) {
            $move = $moves[$i];
            $nextMove = $moves[$i + 1] ?? null;
            if ($nextMove === null) {
                break;
            }
            $nextMovePosition = $this->nextMovePosition($move, $nextMove);
            if (!isset($this->knowledge[$move])) {
                $this->knowledge[$move] = [];
            }
            if (!isset($this->knowledge[$move][$nextMovePosition])) {
                $this->knowledge[$move][$nextMovePosition] = 0;
            }

            if ($isWinnerMove) {
                $this->knowledge[$move][$nextMovePosition] += self::POSITIVE_KNOWLEDGE;
            } else {
                $this->knowledge[$move][$nextMovePosition] += self::NEGATIVE_KNOWLEDGE;
            }

            $isWinnerMove = !$isWinnerMove;

        }
        file_put_contents('knowledge.json', json_encode($this->knowledge));

    }

    private function nextMovePosition(string $currentMove, string $nextMove): int
    {
        for ($i = 0; $i < strlen($currentMove); $i++) {
            if ($currentMove[$i] === '-' && $nextMove[$i] !== '-') {
                return $i;
            }
        }
        throw new \Exception('Invalid moves');
    }
}