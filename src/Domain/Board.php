<?php

namespace App\Domain;

final class Board
{
    public const PLAYER_1 = 'X';
    public const PLAYER_2 = 'O';
    public const DRAW = 'draw';

    private string $board = '---------';

    private array $moves = [];

    private string $turn = self::PLAYER_1;

    private array $winningPositions =
        [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // Horizontal
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // Vertical
            [0, 4, 8], [2, 4, 6] // Diagonal
        ];

    public function __construct(private readonly Player $player1, private readonly Player $player2)
    {
        $this->player1->setSymbol(self::PLAYER_1);
        $this->player2->setSymbol(self::PLAYER_2);
        $this->moves[] = $this->board;
    }

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

    public function move(): int {
        if ($this->turn === self::PLAYER_1) {
            $position = $this->player1->getMove($this);
            $this->board[$position] = $this->turn;
            $this->moves[] = $this->board;
            $this->turn = self::PLAYER_2;
            return $position;
        }
        $position = $this->player2->getMove($this);
        $this->board[$position] = $this->turn;
        $this->moves[] = $this->board;
        $this->turn = self::PLAYER_1;
        return $position;
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

    public function getMoves(): array
    {
        return $this->moves;
    }
}