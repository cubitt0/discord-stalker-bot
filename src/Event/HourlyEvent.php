<?php

declare(strict_types=1);

namespace App\Event;

use Discord\Discord;
use React\EventLoop\TimerInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class HourlyEvent extends Event
{
    public function __construct(
        private readonly Discord $discord,
        private readonly TimerInterface $timer
    ) {
    }

    public function getDiscord(): Discord
    {
        return $this->discord;
    }

    public function getTimer(): TimerInterface
    {
        return $this->timer;
    }
}