<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\SendSignOffMailToReferee;

use Alejgarciarodriguez\SymfonyDemo\Domain\Event\RefereeWasRemovedEvent;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;

final class SendSignOffMailToRefereeHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(RefereeWasRemovedEvent $event): void
    {
        $me = new Address('alejgarciarodriguez@gmail.com');
        $this->mailer->send(
            new RawMessage('TODO: email to Referee'),
            new Envelope($me, recipients: [$me]),
        );
    }
}
