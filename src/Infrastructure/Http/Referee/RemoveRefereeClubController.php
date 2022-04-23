<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Referee;

use Alejgarciarodriguez\SymfonyDemo\Application\Referee\RemoveRefereeClub\RemoveRefereeClubCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class RemoveRefereeClubController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/referee/{name}/club', methods: ['DELETE'])]
    public function __invoke(Request $request): Response
    {
        $name = $request->get('name');

        Assert::that($name)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        $this->bus->dispatch(new RemoveRefereeClubCommand(
            name: $name,
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
