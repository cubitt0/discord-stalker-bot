<?php

declare(strict_types=1);

namespace App\Event;

use Discord\Discord;
use Symfony\Contracts\EventDispatcher\Event;

final class BotReadyEvent extends Event
{
    public function __construct(
        private readonly Discord $discord
    )
    {
    }

    public function getDiscord(): Discord
    {
        return $this->discord;
    }
}