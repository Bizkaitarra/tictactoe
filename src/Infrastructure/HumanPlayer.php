<?php

namespace App\Infrastructure;

use App\Domain\Board;
use App\Domain\Player;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class HumanPlayer extends Player
{
    public function __construct(
        private readonly InputInterface $input,
        private readonly OutputInterface $output,
        private readonly HelperInterface $questionHelper,
    )
    {

    }

    public function getMove(Board $board): int
    {
        $this->output->writeln('Enter the position you want to place your mark (0-8)');

        while (true) {
            $question = new Question('Please enter a number between 0 and 8: ');

            $number = $this->questionHelper->ask($this->input, $this->output, $question);

            // Verificar que el número esté entre 0 y 8
            if (!is_numeric($number) || $number < 0 || $number > 8) {
                $this->output->writeln('<error>The number must be between 0 and 8.</error>');
                continue;
            }
            if ($board->isAlreadyFilled((int) $number)) {
                $this->output->writeln('<error>The position must be empty.</error>');
                continue;
            }
            return $number;
        }
    }

    public function gameHasFinished(Board $board): void
    {
    }
}