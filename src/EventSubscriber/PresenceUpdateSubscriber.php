<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\PresenceUpdateEvent;
use App\Repository\StalkSettingsRepository;
use Discord\Parts\User\Activity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PresenceUpdateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly StalkSettingsRepository $stalkSettingsRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PresenceUpdateEvent::class => [
                ['handlePresenceUpdate', 100],
            ]
        ];
    }

    public function handlePresenceUpdate(PresenceUpdateEvent $presenceUpdateEvent): void
    {
        $presence = $presenceUpdateEvent->getPresence();
        if ($presence->status !== Activity::STATUS_ONLINE || $presence->desktop_status === null) {
            return;
        }
        $usersToDm = $this->stalkSettingsRepository->findBy(['stalked' => $presence->user->id]);
        foreach ($usersToDm as $user) {
            $user = $presenceUpdateEvent->getDiscord()->users->get('id', $user->getStalker());
            $user->sendMessage('Szefie nie uwierzysz, ' . $presence->user->username . ' jest aktywny');
        }
    }
}