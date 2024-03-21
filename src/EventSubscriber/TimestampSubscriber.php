<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Doctrine\ORM\Interfaces\TimestampInterface;
use DateTime;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TimestampSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof TimestampInterface) {
            $now = new DateTime('now');

            $entity
                ->setCreatedAt($now)
                ->setUpdatedAt($now);
        }
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof TimestampInterface) {
            $now = new DateTime('now');

            $entity->setUpdatedAt($now);
        }
    }
}