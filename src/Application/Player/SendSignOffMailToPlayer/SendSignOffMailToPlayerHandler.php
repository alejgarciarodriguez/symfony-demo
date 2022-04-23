<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\SendSignOffMailToPlayer;

use Alejgarciarodriguez\SymfonyDemo\Domain\Event\PlayerWasRemovedEvent;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;

final class SendSignOffMailToPlayerHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(PlayerWasRemovedEvent $event): void
    {
        $me = new Address('alejgarciarodriguez@gmail.com');
        $this->mailer->send(
            new RawMessage('TODO: email to player'),
            new Envelope($me, recipients: [$me]),
        );
    }
}
