<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Player\RemovePlayer;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\RemovePlayer\RemovePlayerCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Player\RemovePlayer\RemovePlayerHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Event\PlayerWasRemovedEvent;
use Alejgarciarodriguez\SymfonyDemo\Domain\EventBus;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\PlayerNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;
use PHPUnit\Framework\TestCase;

final class RemovePlayerHandlerTest extends TestCase
{
    private PlayerRepository $mockPlayerRepository;
    private ClubRepository $mockClubRepository;
    private EventBus $eventBus;

    protected function setUp(): void
    {
        $this->mockPlayerRepository = $this->createMock(PlayerRepository::class);
        $this->mockClubRepository   = $this->createMock(ClubRepository::class);
        $this->eventBus             = $this->createMock(EventBus::class);
    }

    public function testICanRemoveAPlayerWithNoClub(): void
    {
        $command = new RemovePlayerCommand(
            name: 'APlayer',
        );

        $player = new Player(
            name: $command->name(),
            salary: 100,
            club: null,
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($player);

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('remove')
            ->with($player);

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(new PlayerWasRemovedEvent($player));

        $handler = new RemovePlayerHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
            $this->eventBus,
        );

        ($handler)($command);
    }

    public function testICanRemoveAPlayerWithClub(): void
    {
        $command = new RemovePlayerCommand(
            name: 'AReferee',
        );

        $currentBudget = 100;

        $club = new Club(
            name: 'Aclub',
            budget: $currentBudget,
        );

        $player = new Player(
            name: $command->name(),
            salary: 10,
            club: $club,
        );

        $club->addPlayer($player);

        $this->assertEquals(
            $currentBudget - $player->getSalary(),
            $club->getBudget(),
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($player);

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('remove')
            ->with($player);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save')
            ->with($club);

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(new PlayerWasRemovedEvent($player));

        $handler = new RemovePlayerHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
            $this->eventBus,
        );

        ($handler)($command);

        $this->assertEquals(
            $currentBudget,
            $club->getBudget(),
        );

        $this->assertEmpty($club->getReferees());
        $this->assertNull($player->getClub());
    }

    public function testICantRemoveAPlayerIfDoesNotExists(): void
    {
        $command = new RemovePlayerCommand(
            name: 'APlayer',
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $this->mockPlayerRepository
            ->expects($this->never())
            ->method('remove');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $this->eventBus
            ->expects($this->never())
            ->method('dispatch');

        $handler = new RemovePlayerHandler(
            $this->mockPlayerRepository,
            $this->mockClubRepository,
            $this->eventBus,
        );

        $this->expectException(PlayerNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Player "%s" not found', $command->name()));

        ($handler)($command);
    }
}
