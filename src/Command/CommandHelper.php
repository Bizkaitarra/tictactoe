<?php

namespace App\Command;

use App\Domain\Player;
use App\Infrastructure\HumanPlayer;
use App\Infrastructure\RandomPlayer;
use App\Infrastructure\SimpleIAPlayer;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

final class CommandHelper
{
    public static function choosePlayerType(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        string $questionText,
    ): Player
    {
        $question = new ChoiceQuestion(
            $questionText,
            ['Human', 'Random','SimpleIA'],
            0
        );
        $question->setErrorMessage('Option %s is invalid.');
        $playerType = $questionHelper->ask($input, $output, $question);
        return match ($playerType) {
            'Human' => new HumanPlayer($input, $output, $questionHelper),
            'Random' => new RandomPlayer(),
            'SimpleIA' => new SimpleIAPlayer(),
            default => throw new \RuntimeException('Invalid player type'),
        };
    }
}