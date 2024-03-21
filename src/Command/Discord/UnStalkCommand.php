<?php

declare(strict_types=1);

namespace App\Command\Discord;

use App\Entity\StalkSettings;
use App\Repository\StalkSettingsRepository;
use Discord\Builders\CommandBuilder;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\User;
use Doctrine\ORM\EntityManagerInterface;

final class UnStalkCommand implements DiscordBotCommandInterface
{
    public const COMMAND_NAME = 'unstalk';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StalkSettingsRepository $stalkSettingsRepository
    ) {
    }

    public function getCommand(Discord $discord): CommandBuilder
    {
        return CommandBuilder::new()
                             ->setName(self::COMMAND_NAME)
                             ->setDescription('Tą komendą wyłączysz stalkowanie')
                             ->addOption(
                                 (new Option($discord))
                                     ->setName('kogo')
                                     ->setDescription('Kogo chciałbyś przestać stalkowac?')
                                     ->setType(Option::USER)
                                     ->setRequired(true)
                             );
    }

    public function getHandler(): callable
    {
        return function (Interaction $interaction) {
            $stalkerId = $interaction->user->id;
            $stalked   = $interaction->data->resolved->users->first();

            if (!$stalked instanceof User) {
                return $interaction->respondWithMessage((new MessageBuilder())->setContent('No nie ma takiego'));
            }
            $stalkedId = $stalked->id;
            $result    = $this->stalkSettingsRepository->findOneBy(['stalked' => $stalkedId, 'stalker' => $stalkerId]);

            if (!$result instanceof StalkSettings) {
                return $interaction->respondWithMessage(
                    (new MessageBuilder())->setContent('Jak chcesz go odstalkować to najpierw go postalkuj')
                );
            }

            $this->entityManager->remove($result);
            $this->entityManager->flush();
            return $interaction->respondWithMessage(
                (new MessageBuilder())->setContent('Dobra już Ci nie będę spamował o nim w DMach')
            );
        };
    }

    public function getCommandName(): string
    {
        return self::COMMAND_NAME;
    }
}