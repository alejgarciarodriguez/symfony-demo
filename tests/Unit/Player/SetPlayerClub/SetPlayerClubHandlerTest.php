<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Player\SetPlayerClub;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\SetPlayerClub\SetPlayerClubCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Player\SetPlayerClub\SetPlayerClubHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\PlayerNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;
use PHPUnit\Framework\TestCase;

final class SetPlayerClubHandlerTest extends TestCase
{
    private PlayerRepository $mockPlayerRepository;
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockPlayerRepository = $this->createMock(PlayerRepository::class);
        $this->mockClubRepository   = $this->createMock(ClubRepository::class);
    }

    public function testICanModifyClubIfPlayerHasNoClub(): void
    {
        $command = new SetPlayerClubCommand(
            playerName: 'Aplayer',
            clubName: 'Aclub',
        );

        $player = new Player(
            name: $command->playerName(),
            salary: 1,
            club: null,
        );

        $club = new Club(
            name: 'Aclub',
            budget: 10,
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->playerName())
            ->willReturn($player);

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('save')
            ->with($player);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->clubName())
            ->willReturn($club);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save')
            ->with($club);

        $handler = new SetPlayerClubHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
        );

        $this->assertNull($player->getClub());
        $this->assertNotContains($player, $club->getPlayers());

        ($handler)($command);

        $this->assertSame($club, $player->getClub());
        $this->assertContains($player, $club->getPlayers());
    }

    public function testICanModifyClubIfPlayerHasAlreadyAClub(): void
    {
        $command = new SetPlayerClubCommand(
            playerName: 'Aplayer',
            clubName: 'AnotherClub',
        );

        $player = new Player(
            name: $command->playerName(),
            salary: 1,
            club: null,
        );

        $club = new Club(
            name: 'Aclub',
            budget: 10,
        );

        $anotherClub = new Club(
            name: 'AnotherClub',
            budget: 10,
        );

        $club->addPlayer($player);

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->playerName())
            ->willReturn($player);

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('save')
            ->with($player);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->clubName())
            ->willReturn($anotherClub);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save')
            ->with($anotherClub);

        $handler = new SetPlayerClubHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
        );

        $this->assertNotNull($player->getClub());
        $this->assertContains($player, $club->getPlayers());
        $this->assertNotContains($player, $anotherClub->getPlayers());

        ($handler)($command);

        $this->assertSame($anotherClub, $player->getClub());
        $this->assertContains($player, $anotherClub->getPlayers());
        $this->assertNotContains($player, $club->getPlayers());
    }

    public function testICantModifyClubIfDoesNotExist(): void
    {
        $command = new SetPlayerClubCommand(
            playerName: 'Aplayer',
            clubName: 'Aclub',
        );

        $player = new Player(
            name: $command->playerName(),
            salary: 1,
            club: null,
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->playerName())
            ->willReturn($player);

        $this->mockPlayerRepository
            ->expects($this->never())
            ->method('save');

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->clubName())
            ->willReturn(null);

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $handler = new SetPlayerClubHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
        );

        $this->expectException(ClubNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Club "%s" not found', $command->clubName()));

        ($handler)($command);
    }

    public function testICantModifyClubIfPlayerDoesNotExist(): void
    {
        $command = new SetPlayerClubCommand(
            playerName: 'Aplayer',
            clubName: 'Aclub',
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->playerName())
            ->willReturn(null);

        $this->mockPlayerRepository
            ->expects($this->never())
            ->method('save');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('search');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $handler = new SetPlayerClubHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
        );

        $this->expectException(PlayerNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Player "%s" not found', $command->playerName()));

        ($handler)($command);
    }
}
