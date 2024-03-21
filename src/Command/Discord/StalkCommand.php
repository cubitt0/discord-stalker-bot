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

final class StalkCommand implements DiscordBotCommandInterface
{
    public const COMMAND_NAME = 'stalk';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StalkSettingsRepository $stalkSettingsRepository
    ) {
    }

    public function getCommand(Discord $discord): CommandBuilder
    {
        return CommandBuilder::new()
                             ->setName(self::COMMAND_NAME)
                             ->setDescription('Wysyła DMa jak pojawi się online')
                             ->addOption(
                                 (new Option($discord))
                                     ->setName('kogo')
                                     ->setDescription('Kogo chcesz stalkować?')
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

            if ($result instanceof StalkSettings) {
                return $interaction->respondWithMessage((new MessageBuilder())->setContent('Już stalkujesz tego byczka'));
            }
            //TODO: We want to move it so it doesn't block event loop
            //TODO: Check if stalker and stalked id are not the same
            $stalkSettings = new StalkSettings();
            $stalkSettings->setStalked($stalkedId);
            $stalkSettings->setStalker($stalkerId);
            $this->entityManager->persist($stalkSettings);
            $this->entityManager->flush();

            return $interaction->respondWithMessage((new MessageBuilder())->setContent('Jak pojawi się online dostaniesz DMa'));
        };
    }

    public function getCommandName(): string
    {
        return self::COMMAND_NAME;
    }
}