<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Player\CreatePlayer;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\CreatePlayer\CreatePlayerCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Player\CreatePlayer\CreatePlayerHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\InsufficientClubBudgetException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;
use PHPUnit\Framework\TestCase;

final class CreatePlayerHandlerTest extends TestCase
{
    private PlayerRepository $mockPlayerRepository;
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockPlayerRepository = $this->createMock(PlayerRepository::class);
        $this->mockClubRepository   = $this->createMock(ClubRepository::class);
    }

    public function testICantCreateAPlayerIfAlreadyExists(): void
    {
        $player = new Player(
            name: 'sample',
            salary: 1,
            club: null,
        );

        $command = new CreatePlayerCommand(
            name: $player->getName(),
            salary: $player->getSalary(),
            club: null,
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($player);

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $this->mockPlayerRepository
            ->expects($this->never())
            ->method('save');

        $handler = new CreatePlayerHandler(
            $this->mockClubRepository,
            $this->mockPlayerRepository,
        );

        ($handler)($command);
    }

    public function testICanCreateAPlayerWithNoClub(): void
    {
        $command = new CreatePlayerCommand(
            name: 'sample',
            salary: 10,
            club: null,
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $savedPlayer = null;

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Player $player) use (&$savedPlayer): void {
                $savedPlayer = $player;
            });

        $handler = new CreatePlayerHandler(
            $this->mockClubRepository,
            $this->mockPlayerRepository,
        );

        ($handler)($command);

        $this->assertEquals(
            $command->name(),
            $savedPlayer->getName(),
        );

        $this->assertEquals(
            $command->salary(),
            $savedPlayer->getSalary(),
        );

        $this->assertNull($savedPlayer->getClub());
    }

    public function testICanCreateAPlayerWithClub(): void
    {
        $command = new CreatePlayerCommand(
            name: 'sample',
            salary: 10,
            club: 'club',
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $savedPlayer = null;

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Player $player) use (&$savedPlayer) {
                $savedPlayer = $player;
            });

        $clubInitialBudget = 100;

        $club = new Club(
            name: $command->club(),
            budget: $clubInitialBudget,
        );

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->club())
            ->willReturn($club);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save');

        $handler = new CreatePlayerHandler(
            $this->mockClubRepository,
            $this->mockPlayerRepository,
        );

        ($handler)($command);

        $this->assertCount(
            1,
            $club->getPlayers(),
        );

        $this->assertEquals(
            $command->name(),
            $club->getPlayers()->first()->getName(),
        );

        $this->assertEquals(
            $clubInitialBudget - $command->salary(),
            $club->getBudget(),
        );

        $this->assertEquals(
            $club->getName(),
            $savedPlayer->getClub()->getName(),
        );

        $this->assertEquals(
            $command->salary(),
            $savedPlayer->getSalary(),
        );

        $this->assertEquals(
            $command->name(),
            $savedPlayer->getName(),
        );

        $this->assertEquals(
            $command->club(),
            $savedPlayer->getClub()->getName(),
        );
    }

    public function testICantCreateAPlayerWithClubIfClubDoesNotExist(): void
    {
        $command = new CreatePlayerCommand(
            name: 'sample',
            salary: 10,
            club: 'club',
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->club())
            ->willReturn(null);

        $handler = new CreatePlayerHandler(
            $this->mockClubRepository,
            $this->mockPlayerRepository,
        );

        $this->expectException(ClubNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Club "%s" not found', $command->club()));

        ($handler)($command);
    }

    public function testICantCreateAPlayerWithClubIfClubDoesNotHaveEnoughBudget(): void
    {
        $command = new CreatePlayerCommand(
            name: 'sample',
            salary: 10,
            club: 'club',
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $club = new Club(
            name: 'club',
            budget: 9,
        );

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->club())
            ->willReturn($club);

        $this->expectException(InsufficientClubBudgetException::class);
        $this->expectDeprecationMessage(\sprintf('Insufficient budget for club "%s"', $command->club()));

        $handler = new CreatePlayerHandler(
            $this->mockClubRepository,
            $this->mockPlayerRepository,
        );

        ($handler)($command);
    }
}
