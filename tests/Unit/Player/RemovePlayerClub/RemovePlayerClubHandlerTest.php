<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Player\RemovePlayerClub;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\RemovePlayerClub\RemovePlayerClubCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Player\RemovePlayerClub\RemovePlayerClubHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\PlayerNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;
use PHPUnit\Framework\TestCase;

final class RemovePlayerClubHandlerTest extends TestCase
{
    private PlayerRepository $mockPlayerRepository;
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockPlayerRepository = $this->createMock(PlayerRepository::class);
        $this->mockClubRepository   = $this->createMock(ClubRepository::class);
    }

    public function testICanRemovePlayersClub(): void
    {
        $command = new RemovePlayerClubCommand(
            name: 'Aplayer',
        );

        $currentBudget = 10;

        $club = new Club(
            name: 'Aclub',
            budget: $currentBudget,
        );

        $player = new Player(
            name: $command->name(),
            salary: 1,
            club: $club,
        );

        $club->addPlayer($player);

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($player);

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('save')
            ->with($player);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save')
            ->with($club);

        $handler = new RemovePlayerClubHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
        );

        ($handler)($command);

        $this->assertNull($player->getClub());
        $this->assertEquals(
            $currentBudget,
            $club->getBudget(),
        );
        $this->assertEmpty($club->getPlayers());
    }

    public function testICantRemovePlayersClubIfPlayerDoesNotExist(): void
    {
        $command = new RemovePlayerClubCommand(
            name: 'Aplayer',
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $this->mockPlayerRepository
            ->expects($this->never())
            ->method('save');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $handler = new RemovePlayerClubHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
        );

        $this->expectException(PlayerNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Player "%s" not found', $command->name()));

        ($handler)($command);
    }

    public function testItDoesNothingIfPlayerDoesNotHaveAClubAssigned(): void
    {
        $command = new RemovePlayerClubCommand(
            name: 'Aplayer',
        );

        $player = new Player(
            name: $command->name(),
            salary: 1,
            club: null,
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($player);

        $this->mockPlayerRepository
            ->expects($this->never())
            ->method('save');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $handler = new RemovePlayerClubHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
        );

        ($handler)($command);

        $this->assertNull($player->getClub());
    }
}
