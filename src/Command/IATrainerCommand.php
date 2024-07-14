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

#[AsCommand(name: 'app:tic-tac-toe-train')]
final class IATrainerCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Train the IA to play Tic Tac Toe')
            ->setHelp('This command allows you to train the IA to play Tic Tac Toe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Training the IA to play Tic Tac Toe!');

        $output->writeln('Training finished!');
        $helper = $this->getHelper('question');

        $player1 = CommandHelper::choosePlayerType($input, $output, $helper, 'How is going to train as player 1?');
        $player2 = CommandHelper::choosePlayerType($input, $output, $helper, 'How is going to train as player 2?');

        $winners = [
            Board::PLAYER_1 => 0,
            Board::PLAYER_2 => 0,
            Board::DRAW => 0
        ];
        for ($i = 0; $i < 1000000000; $i++) {
            $winner = $this->playGame($player1, $player2);
            $winners[$winner]++;
        }

        $output->writeln('Player 1 wins: '.$winners[Board::PLAYER_1]);
        $output->writeln('Player 2 wins: '.$winners[Board::PLAYER_2]);
        $output->writeln('Draws: '.$winners[Board::DRAW]);

        return Command::SUCCESS;
    }

    private function playGame(PLayer $player1, PLayer $player2): string {
        $board = new Board($player1, $player2);
        while (!$board->isFinished()) {
            $board->move();
        }
        $player1->gameHasFinished($board);
        $player2->gameHasFinished($board);
        return $board->getWinner();
    }

    private function choosePlayerType(
        InputInterface $input,
        OutputInterface $output,
        int $playerNumber
    ): Player
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Who is going to train '.$playerNumber.'? (defaults human)',
            ['Human', 'Random'],
            0
        );
        $question->setErrorMessage('Option %s is invalid.');
        $playerType = $helper->ask($input, $output, $question);
        return match ($playerType) {
            'Human' => new HumanPlayer($input, $output, $helper),
            'Random' => new RandomPlayer(),
            default => throw new \RuntimeException('Invalid player type'),
        };
    }
}