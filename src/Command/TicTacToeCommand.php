<?php

namespace App\Command;

use App\Domain\Board;
use App\Domain\Player;
use App\Infrastructure\HumanPlayer;
use App\Infrastructure\RandomPlayer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

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

        $player1 = $this->choosePlayerType($input, $output, 1);
        $player2 = $this->choosePlayerType($input, $output, 2);

        $output->writeln('First player will play with X and second one with O');

        $output->writeln('GO!');

        $board = new Board($player1, $player2);
        while (!$board->isFinished()) {
            $currentPlayerSymbol = $board->turn();
            $output->writeln('Player with symbol ' . $currentPlayerSymbol . ' turn');
            $this->drawBoard($output, $board->getBoard());
            $position = $board->move();
            $output->writeln(
                sprintf(
                    "Player with symbol %s puts in position %s",
                    $currentPlayerSymbol,
                    $position
                )
            );
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

    private function choosePlayerType(
        InputInterface $input,
        OutputInterface $output,
        int $playerNumber
    ): Player
    {
        $helper = $this->getHelper('question');
        return CommandHelper::choosePlayerType($input, $output, $helper, 'Who is going to play as player '.$playerNumber.'? (defaults human)');
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