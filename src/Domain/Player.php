<?php

namespace App\Domain;

abstract class Player
{
    protected string $symbol;
    public function setSymbol(string $symbol) {
        $this->symbol = $symbol;
    }
    public abstract function getMove(Board $board): int;

    public abstract function gameHasFinished(Board $board): void;
}