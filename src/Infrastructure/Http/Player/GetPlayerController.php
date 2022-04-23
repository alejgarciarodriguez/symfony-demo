<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Player;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\GetPlayer\GetPlayerQuery;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

final class GetPlayerController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/player/{name}', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $name = $request->get('name');

        Assert::that($name)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        $stamp = $this->bus->dispatch(new GetPlayerQuery(
            name: $name,
        ))->last(HandledStamp::class);

        \assert($stamp instanceof HandledStamp);
        $result = $stamp->getResult();
        return new JsonResponse($result, Response::HTTP_OK);
    }
}
