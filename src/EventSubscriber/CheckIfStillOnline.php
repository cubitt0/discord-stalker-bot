<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\LastNotifiedUserStatus;
use App\Event\HourlyEvent;
use App\Repository\LastNotifiedUserStatusRepository;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Activity;
use Discord\Parts\User\Member;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CheckIfStillOnline implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LastNotifiedUserStatusRepository $lastNotifiedUserStatusRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HourlyEvent::class => [
                ['verifyIfStalkedStillOnline', 50]
            ]
        ];
    }

    public function verifyIfStalkedStillOnline(HourlyEvent $hourlyEvent): void
    {
        $onlineUsers = $this->lastNotifiedUserStatusRepository->findAllNotOfflineUsers();

        if (!count($onlineUsers)) {
            return;
        }

        $activeUsersId = array_map(static function (LastNotifiedUserStatus $userStatus) {
            return $userStatus->getUserid();
        }, $onlineUsers);

        $inactiveUsers = [];

        /** @var Guild $guild */
        foreach ($hourlyEvent->getDiscord()->guilds->getIterator() as $guild) {
            $members = $guild->members->filter(function (Member $member) use ($activeUsersId) {
                return in_array($member->id, $activeUsersId, true) && $member->status === Activity::STATUS_INVISIBLE;
            });
            /** @var Member $member */
            foreach ($members as $member) {
                $inactiveUsers[] = $member->id;
            }
        }

        foreach (array_unique($inactiveUsers) as $inactiveUserId) {
            foreach ($onlineUsers as $onlineUser) {
                if ($onlineUser->getUserid() === $inactiveUserId) {
                    $onlineUser->setStatus(Activity::STATUS_INVISIBLE);
                }
            }
        }

        $this->entityManager->flush();
    }
}