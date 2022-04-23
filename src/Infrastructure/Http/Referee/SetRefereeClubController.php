<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Http\Referee;

use Alejgarciarodriguez\SymfonyDemo\Application\Referee\SetRefereeClub\SetRefereeClubCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class SetRefereeClubController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[Route('/api/referee/{referee_name}/club/{club_name}', methods: ['PUT'])]
    public function __invoke(Request $request): Response
    {
        $refereeName = $request->get('referee_name');
        $clubName    = $request->get('club_name');

        Assert::that($refereeName)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        Assert::that($clubName)
            ->string()
            ->notEmpty()
            ->maxLength(255);

        $this->bus->dispatch(new SetRefereeClubCommand(
            refereeName: $refereeName,
            clubName: $clubName,
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
