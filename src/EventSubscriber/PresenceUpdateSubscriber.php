<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\LastNotifiedUserStatus;
use App\Enum\UserStatusEnum;
use App\Event\PresenceUpdateEvent;
use App\Repository\LastNotifiedUserStatusRepository;
use App\Repository\StalkSettingsRepository;
use Discord\Parts\WebSockets\PresenceUpdate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PresenceUpdateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly StalkSettingsRepository $stalkSettingsRepository,
        private readonly LastNotifiedUserStatusRepository $lastNotifiedUserStatusRepository,
        private readonly EntityManagerInterface $entityManager
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
        $presence       = $presenceUpdateEvent->getPresence();
        $presenceUserId = $presenceUpdateEvent->getPresence()->user->id;

        $previousStatus = $this->lastNotifiedUserStatusRepository->findOneBy(['userid' => $presenceUserId]);

        if (!$previousStatus) {
            $userStatus = new LastNotifiedUserStatus();
            $userStatus->setUserid($presenceUserId);
            $userStatus->setStatus(UserStatusEnum::from($presence->status));
            $this->entityManager->persist($userStatus);
        }

        if ($this->wasOfflineAndNowIsOnline($previousStatus, $presence)) {
            $this->sendDms($presenceUpdateEvent);
            $previousStatus->setStatus(UserStatusEnum::from($presence->status));
        } elseif ($this->isNowOnlineButNoPreviousState($previousStatus, $presence)) {
            $this->sendDms($presenceUpdateEvent);
        } else {
            $previousStatus->setStatus(UserStatusEnum::from($presence->status));

        }

        $this->entityManager->flush();
    }

    private function sendDms(PresenceUpdateEvent $presenceUpdateEvent): void
    {
        $usersToDm = $this->stalkSettingsRepository->findBy(['stalked' => $presenceUpdateEvent->getPresence()->user->id]);
        foreach ($usersToDm as $user) {
            $user = $presenceUpdateEvent->getDiscord()->users->get('id', $user->getStalker());
            $user->sendMessage('Szefie nie uwierzysz, ' . $presenceUpdateEvent->getPresence()->user->username . ' jest aktywny');
        }
    }

    private function wasOfflineAndNowIsOnline(?LastNotifiedUserStatus $previousStatus, PresenceUpdate $presence): bool
    {
        return $previousStatus
            && $previousStatus->getStatus()->value === UserStatusEnum::OFFLINE->value
            && $presence->status !== UserStatusEnum::OFFLINE->value;
    }

    private function isNowOnlineButNoPreviousState(?LastNotifiedUserStatus $previousStatus, PresenceUpdate $presence): bool
    {
        return !$previousStatus && $presence->status !== UserStatusEnum::OFFLINE->value;
    }
}