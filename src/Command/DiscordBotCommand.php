<?php

namespace App\Command;

use App\Event\BotReadyEvent;
use App\Event\HourlyEvent;
use App\Event\PresenceUpdateEvent;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\WebSockets\PresenceUpdate;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Psr\EventDispatcher\EventDispatcherInterface;
use React\EventLoop\TimerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:discord:run',
    description: 'Discord bot main command',
)]
class DiscordBotCommand extends Command
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        #[Autowire(env: 'BOT_TOKEN')]
        private readonly string $botToken,
    ) {
        parent::__construct();
    }

    /**
     * @throws IntentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('max_execution_time', 0);

        $discord = new Discord([
            'token'          => $this->botToken,
            'intents'        => Intents::getAllIntents(),
            'loadAllMembers' => true
        ]);

        $discord->on('ready', function (Discord $discord) {
            echo "Bot is ready!", PHP_EOL;

            $discord->application->commands->clear();

            $this->dispatcher->dispatch((new BotReadyEvent($discord)));

            $discord->getLoop()->addPeriodicTimer(3600, function (TimerInterface $timer) use ($discord) {
                $this->dispatcher->dispatch(new HourlyEvent($discord, $timer));
            });

            $discord->on(Event::PRESENCE_UPDATE, function (PresenceUpdate $presence, Discord $discord) {
                $this->dispatcher->dispatch(new PresenceUpdateEvent($presence, $discord));
            });
        });

        $discord->run();

        return Command::SUCCESS;
    }
}
