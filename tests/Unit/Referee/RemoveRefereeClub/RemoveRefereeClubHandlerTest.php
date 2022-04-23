<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Referee\RemoveRefereeClub;

use Alejgarciarodriguez\SymfonyDemo\Application\Referee\RemoveRefereeClub\RemoveRefereeClubCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Referee\RemoveRefereeClub\RemoveRefereeClubHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\RefereeNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;
use PHPUnit\Framework\TestCase;

final class RemoveRefereeClubHandlerTest extends TestCase
{
    private RefereeRepository $mockRefereeRepository;
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockRefereeRepository = $this->createMock(RefereeRepository::class);
        $this->mockClubRepository    = $this->createMock(ClubRepository::class);
    }

    public function testICanRemovePlayersClub(): void
    {
        $command = new RemoveRefereeClubCommand(
            name: 'Areferee',
        );

        $currentBudget = 10;

        $club = new Club(
            name: 'Aclub',
            budget: $currentBudget,
        );

        $referee = new Referee(
            name: $command->name(),
            salary: 1,
            club: $club,
        );

        $club->addReferee($referee);

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($referee);

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('save')
            ->with($referee);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save')
            ->with($club);

        $handler = new RemoveRefereeClubHandler(
            $this->mockRefereeRepository,
            $this->mockClubRepository,
        );

        ($handler)($command);

        $this->assertNull($referee->getClub());
        $this->assertEquals(
            $currentBudget,
            $club->getBudget(),
        );
        $this->assertEmpty($club->getReferees());
    }

    public function testICantRemovePlayersClubIfPlayerDoesNotExist(): void
    {
        $command = new RemoveRefereeClubCommand(
            name: 'Areferee',
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $this->mockRefereeRepository
            ->expects($this->never())
            ->method('save');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $handler = new RemoveRefereeClubHandler(
            $this->mockRefereeRepository,
            $this->mockClubRepository,
        );

        $this->expectException(RefereeNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Referee "%s" not found', $command->name()));

        ($handler)($command);
    }

    public function testItDoesNothingIfPlayerDoesNotHaveAClubAssigned(): void
    {
        $command = new RemoveRefereeClubCommand(
            name: 'Areferee',
        );

        $referee = new Referee(
            name: $command->name(),
            salary: 1,
            club: null,
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($referee);

        $this->mockRefereeRepository
            ->expects($this->never())
            ->method('save');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $handler = new RemoveRefereeClubHandler(
            $this->mockRefereeRepository,
            $this->mockClubRepository,
        );

        ($handler)($command);

        $this->assertNull($referee->getClub());
    }
}
