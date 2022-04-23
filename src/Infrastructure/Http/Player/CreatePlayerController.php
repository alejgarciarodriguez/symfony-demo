<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Player;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\CreatePlayer\CreatePlayerCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CreatePlayerController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/player', methods: ['PUT'])]
    public function __invoke(Request $request): Response
    {
        $content = $request->getContent();

        Assert::that($content)->isJsonString();

        $body = new ParameterBag(
            \json_decode(
                $content,
                true,
            ),
        );

        $name   = $body->get('name');
        $club   = $body->get('club');
        $salary = $body->get('salary');

        Assert::that($name)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        Assert::that($club)
            ->nullOr()
            ->string()
            ->notEmpty();

        Assert::that($salary)
            ->integer()
            ->min(0);

        $this->bus->dispatch(new CreatePlayerCommand(
            name: $name,
            salary: $salary,
            club: $club,
        ));

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
