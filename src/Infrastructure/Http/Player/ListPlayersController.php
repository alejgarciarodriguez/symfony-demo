<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Player;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\ListPlayers\ListPlayersQuery;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

final class ListPlayersController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/player', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $sortBy  = $request->query->get('sortBy', 'salary');
        $orderBy = $request->query->get('orderBy', 'desc');
        $club    = $request->query->get('club');
        $limit   = $request->query->getInt('limit', 50);
        $page    = $request->query->getInt('page', 0);

        Assert::that($sortBy)
            ->inArray(['salary', 'name']);

        Assert::that($orderBy)
            ->inArray(['desc', 'asc']);

        Assert::that($club)
            ->nullOr()
            ->string()
            ->maxLength(255);

        Assert::that($limit)
            ->integer()
            ->min(0)
            ->max(100)
            ->notNull();

        Assert::that($page)
            ->integer()
            ->min(0)
            ->notNull();

        $stamp = $this->bus->dispatch(new ListPlayersQuery(
            sortBy: $sortBy,
            orderBy: $orderBy,
            club: $club,
            limit: $limit,
            page: $page,
        ))->last(HandledStamp::class);

        \assert($stamp instanceof HandledStamp);
        $result = $stamp->getResult();

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
