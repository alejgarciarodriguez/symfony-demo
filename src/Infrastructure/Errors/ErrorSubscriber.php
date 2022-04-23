<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Errors;

use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ResourceNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class ErrorSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['__invoke'],
        ];
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof HandlerFailedException) {
            $throwable = $throwable->getNestedExceptions()[0];
        }

        if ($throwable instanceof \InvalidArgumentException) {
            $event->setResponse(new JsonResponse(null, Response::HTTP_BAD_REQUEST));
        }

        if ($throwable instanceof ResourceNotFoundException) {
            $event->setResponse(new JsonResponse(null, Response::HTTP_NOT_FOUND));
        }
    }
}
