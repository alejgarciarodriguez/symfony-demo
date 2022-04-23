<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Player;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\RemovePlayer\RemovePlayerCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class RemovePlayerController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/player/{name}', methods: ['DELETE'])]
    public function __invoke(Request $request): Response
    {
        $name = $request->get('name');

        Assert::that($name)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        $this->bus->dispatch(new RemovePlayerCommand(
            name: $name,
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
