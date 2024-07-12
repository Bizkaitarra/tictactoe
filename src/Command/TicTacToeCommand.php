<?php

namespace App\Command;

use App\Domain\Board;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:tic-tac-toe')]
final class TicTacToeCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Play a game of Tic Tac Toe')
            ->setHelp('This command allows you to play a game of Tic Tac Toe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Welcome to Tic Tac Toe!');

        $output->writeln('First player will play with X and second one with O');

        $output->writeln('GO!');

        $board = new Board();
        while (!$board->isFinished()) {
            $this->drawBoard($output, $board->getBoard());
            $this->playerTurn($input, $output, $board);
        }

        $this->drawBoard($output, $board->getBoard());

        $winner = $board->getWinner();
        if ($winner === Board::DRAW) {
            $output->writeln('It\'s a draw!');
        } else {
            $output->writeln('Player ' . $winner . ' wins!');
        }

        return Command::SUCCESS;
    }

    private function playerTurn(InputInterface $input, OutputInterface $output, Board $board): void
    {
        $output->writeln('Enter the position you want to place your mark (0-8)');

        while (true) {
            $helper = $this->getHelper('question');
            $question = new Question('Please enter a number between 0 and 8: ');

            $number = $helper->ask($input, $output, $question);

            // Verificar que el número esté entre 0 y 8
            if (!is_numeric($number) || $number < 0 || $number > 8) {
                $output->writeln('<error>The number must be between 0 and 8.</error>');
                continue;
            }
            if ($board->isAlreadyFilled((int) $number)) {
                $output->writeln('<error>The position must be empty.</error>');
                continue;
            }
            break;

        }
        $board->addMove((int) $number);
    }

    private function drawBoard(OutputInterface $output, string $boardString): void
    {
        $board = str_split($boardString);
        $output->writeln('-----');
        for ($i = 0; $i < 9; $i += 3) {
            $line = $this->formatCell($board[$i], $i) . '|' . $this->formatCell($board[$i + 1], $i+1) . '|' . $this->formatCell($board[$i + 2], $i+2);
            $output->writeln($line);
            $output->writeln('-----');
        }
    }

    private function formatCell(string $char, int $position): string
    {
        return in_array($char, ['X', 'O']) ? $char : $position;
    }
}