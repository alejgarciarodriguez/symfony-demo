<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Club;

use Alejgarciarodriguez\SymfonyDemo\Application\Club\UpdateClubBudget\UpdateClubBudgetCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateClubBudgetController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/club/{name}', methods: ['PATCH'])]
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

        $name   = $request->get('name');
        $budget = $body->get('budget');

        Assert::that($name)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        Assert::that($budget)
            ->integer()
            ->min(0);

        $this->bus->dispatch(new UpdateClubBudgetCommand(
            name: $name,
            budget: $budget,
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
