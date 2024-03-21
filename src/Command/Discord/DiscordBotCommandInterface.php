<?php

declare(strict_types=1);

namespace App\Command\Discord;

use Discord\Builders\CommandBuilder;
use Discord\Discord;

interface DiscordBotCommandInterface
{
    public function getCommand(Discord $discord): CommandBuilder;

    public function getHandler(): callable;

    public function getCommandName(): string;

}