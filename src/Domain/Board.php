<?php

namespace App\Domain;

final class Board
{
    public const PLAYER_1 = 'X';
    public const PLAYER_2 = 'O';
    public const DRAW = 'draw';

    private string $board = '---------';

    private string $turn = self::PLAYER_1;

    private array $winningPositions =
        [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // Horizontal
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // Vertical
            [0, 4, 8], [2, 4, 6] // Diagonal
        ];

    public function getWinner(): ?string {
        foreach ($this->winningPositions as $winningPosition) {
            if (in_array($this->board[$winningPosition[0]], [self::PLAYER_1, self::PLAYER_2]) &&
                $this->board[$winningPosition[0]] === $this->board[$winningPosition[1]] &&
                $this->board[$winningPosition[1]] === $this->board[$winningPosition[2]]
            ) {
                return $this->board[$winningPosition[0]];
            }
        }
        return self::DRAW;
    }

    public function isFinished(): bool {
        $allPositionsFilled = !str_contains($this->board, '-');
        return $allPositionsFilled  || $this->getWinner() !== self::DRAW;
    }

    public function addMove(int $position): void {
        $this->board[$position] = $this->turn;
        if ($this->turn === self::PLAYER_1) {
            $this->turn = self::PLAYER_2;
        } else {
            $this->turn = self::PLAYER_1;
        }
    }

    public function turn(): string
    {
        return $this->turn;
    }

    public function getBoard(): string
    {
        return $this->board;
    }

    public function isAlreadyFilled(int $number): bool
    {
        return in_array($this->board[$number], [self::PLAYER_1, self::PLAYER_2]);
    }
}