<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Command\Discord\DiscordBotCommandInterface;
use App\Command\Discord\PingCommand;
use App\Command\Discord\StalkCommand;
use App\Command\Discord\UnStalkCommand;
use App\Event\BotReadyEvent;
use App\Repository\StalkSettingsRepository;
use Discord\Discord;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CreateBotCommandsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StalkSettingsRepository $stalkSettingsRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BotReadyEvent::class => [
                ['registerCommands', 100],
                ['registerCommandHandlers', 50]
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public function registerCommands(BotReadyEvent $event): void
    {
        $discord  = $event->getDiscord();
        $commands = $this->getCommands($discord);

        foreach ($commands as $command) {
            $discord->application->commands->save(
                $discord->application->commands->create($command->getCommand($discord)->toArray())
            );
        }
    }

    public function registerCommandHandlers(BotReadyEvent $event): void
    {
        $discord  = $event->getDiscord();
        $commands = $this->getCommands($discord);

        foreach ($commands as $command) {
            $discord->listenCommand($command->getCommandName(), $command->getHandler());
        }
    }

    /**
     * @param Discord $discord
     * @return DiscordBotCommandInterface[]
     */
    private function getCommands(Discord $discord): array
    {
        return [
            new StalkCommand($this->entityManager, $this->stalkSettingsRepository),
            new UnStalkCommand($this->entityManager, $this->stalkSettingsRepository),
            new PingCommand()
        ];
    }
}