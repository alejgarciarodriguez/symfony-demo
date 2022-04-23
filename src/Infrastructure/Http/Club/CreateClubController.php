<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Club;

use Alejgarciarodriguez\SymfonyDemo\Application\Club\CreateClub\CreateClubCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CreateClubController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/club', methods: ['PUT'])]
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
        $budget = $body->get('budget');

        Assert::that($name)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        Assert::that($budget)
            ->integer()
            ->min(0);

        $this->bus->dispatch(new CreateClubCommand(
            name: $name,
            budget: $budget,
        ));

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
