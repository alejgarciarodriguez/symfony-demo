<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Player;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\SetPlayerClub\SetPlayerClubCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class SetPlayerClubController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/player/{player_name}/club/{club_name}', methods: ['PUT'])]
    public function __invoke(Request $request): Response
    {
        $playerName = $request->get('player_name');
        $clubName   = $request->get('club_name');

        Assert::that($playerName)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        Assert::that($clubName)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        $this->bus->dispatch(new SetPlayerClubCommand(
            playerName: $playerName,
            clubName: $clubName,
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
