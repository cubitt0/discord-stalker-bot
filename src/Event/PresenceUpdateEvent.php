<?php

declare(strict_types=1);

namespace App\Event;

use Discord\Discord;
use Discord\Parts\WebSockets\PresenceUpdate;
use Symfony\Contracts\EventDispatcher\Event;

final class PresenceUpdateEvent extends Event
{
    public function __construct(
        private readonly PresenceUpdate $presence,
        private readonly Discord $discord
    ) {
    }

    public function getPresence(): PresenceUpdate
    {
        return $this->presence;
    }

    public function getDiscord(): Discord
    {
        return $this->discord;
    }
}