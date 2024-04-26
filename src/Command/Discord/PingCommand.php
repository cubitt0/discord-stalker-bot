<?php

declare(strict_types=1);

namespace App\Command\Discord;

use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

final class PingCommand implements DiscordBotCommandInterface
{
    public const COMMAND_NAME = 'ping';

    public function getCommand(Discord $discord): CommandBuilder
    {
        return CommandBuilder::new()
            ->setName(self::COMMAND_NAME)
            ->setDescription('Pinguje bota');
    }

    public function getHandler(): callable
    {
        return static function (Interaction $interaction) {
            return $interaction->respondWithMessage((new MessageBuilder())->setContent('pong'));
        };
    }

    public function getCommandName(): string
    {
        return self::COMMAND_NAME;
    }
}