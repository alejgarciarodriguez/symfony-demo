<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Referee\RemoveReferee;

use Alejgarciarodriguez\SymfonyDemo\Application\Referee\RemoveReferee\RemoveRefereeCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Referee\RemoveReferee\RemoveRefereeHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Event\RefereeWasRemovedEvent;
use Alejgarciarodriguez\SymfonyDemo\Domain\EventBus;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\RefereeNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;
use PHPUnit\Framework\TestCase;

final class RemoveRefereeHandlerTest extends TestCase
{
    private RefereeRepository $mockRefereeRepository;
    private ClubRepository $mockClubRepository;
    private EventBus $eventBus;

    protected function setUp(): void
    {
        $this->mockRefereeRepository = $this->createMock(RefereeRepository::class);
        $this->mockClubRepository    = $this->createMock(ClubRepository::class);
        $this->eventBus              = $this->createMock(EventBus::class);
    }

    public function testICanRemoveARefereeWithNoClub(): void
    {
        $command = new RemoveRefereeCommand(
            name: 'AReferee',
        );

        $referee = new Referee(
            name: $command->name(),
            salary: 100,
            club: null,
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($referee);

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('remove')
            ->with($referee);

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(new RefereeWasRemovedEvent($referee));

        $handler = new RemoveRefereeHandler(
            $this->mockRefereeRepository,
            $this->mockClubRepository,
            $this->eventBus,
        );

        ($handler)($command);
    }

    public function testICanRemoveARefereeWithClub(): void
    {
        $command = new RemoveRefereeCommand(
            name: 'AReferee',
        );

        $currentBudget = 100;

        $club = new Club(
            name: 'Aclub',
            budget: $currentBudget,
        );

        $referee = new Referee(
            name: $command->name(),
            salary: 10,
            club: $club,
        );

        $club->addReferee($referee);

        $this->assertEquals(
            $currentBudget - $referee->getSalary(),
            $club->getBudget(),
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($referee);

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('remove')
            ->with($referee);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save')
            ->with($club);

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(new RefereeWasRemovedEvent($referee));

        $handler = new RemoveRefereeHandler(
            $this->mockRefereeRepository,
            $this->mockClubRepository,
            $this->eventBus,
        );

        ($handler)($command);

        $this->assertEquals(
            $currentBudget,
            $club->getBudget(),
        );

        $this->assertEmpty($club->getReferees());
        $this->assertNull($referee->getClub());
    }

    public function testICantRemoveARefereeIfDoesNotExists(): void
    {
        $command = new RemoveRefereeCommand(
            name: 'AReferee',
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $this->eventBus
            ->expects($this->never())
            ->method('dispatch');

        $handler = new RemoveRefereeHandler(
            $this->mockRefereeRepository,
            $this->mockClubRepository,
            $this->eventBus,
        );

        $this->expectException(RefereeNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Referee "%s" not found', $command->name()));

        ($handler)($command);
    }
}
