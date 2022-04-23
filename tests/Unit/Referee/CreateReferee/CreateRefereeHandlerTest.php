<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Referee\CreateReferee;

use Alejgarciarodriguez\SymfonyDemo\Application\Referee\CreateReferee\CreateRefereeCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Referee\CreateReferee\CreateRefereeHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\InsufficientClubBudgetException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;
use PHPUnit\Framework\TestCase;

final class CreateRefereeHandlerTest extends TestCase
{
    private RefereeRepository $mockRefereeRepository;
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockRefereeRepository = $this->createMock(RefereeRepository::class);
        $this->mockClubRepository    = $this->createMock(ClubRepository::class);
    }

    public function testICantCreateARefereeIfAlreadyExists(): void
    {
        $referee = new Referee(
            name: 'sample',
            salary: 1,
            club: null,
        );

        $command = new CreateRefereeCommand(
            name: $referee->getName(),
            salary: $referee->getSalary(),
            club: null,
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($referee);

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $this->mockRefereeRepository
            ->expects($this->never())
            ->method('save');

        $handler = new CreateRefereeHandler(
            $this->mockClubRepository,
            $this->mockRefereeRepository,
        );

        ($handler)($command);
    }

    public function testICanCreateARefereeWithNoClub(): void
    {
        $command = new CreateRefereeCommand(
            name: 'sample',
            salary: 10,
            club: null,
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $savedReferee = null;

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Referee $referee) use (&$savedReferee): void {
                $savedReferee = $referee;
            });

        $handler = new CreateRefereeHandler(
            $this->mockClubRepository,
            $this->mockRefereeRepository,
        );

        ($handler)($command);

        $this->assertEquals(
            $command->name(),
            $savedReferee->getName(),
        );

        $this->assertEquals(
            $command->salary(),
            $savedReferee->getSalary(),
        );

        $this->assertNull($savedReferee->getClub());
    }

    public function testICanCreateARefereeWithClub(): void
    {
        $command = new CreateRefereeCommand(
            name: 'sample',
            salary: 10,
            club: 'club',
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $savedReferee = null;

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Referee $referee) use (&$savedReferee) {
                $savedReferee = $referee;
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

        $handler = new CreateRefereeHandler(
            $this->mockClubRepository,
            $this->mockRefereeRepository,
        );

        ($handler)($command);

        $this->assertCount(
            1,
            $club->getReferees(),
        );

        $this->assertEquals(
            $command->name(),
            $club->getReferees()->first()->getName(),
        );

        $this->assertEquals(
            $clubInitialBudget - $command->salary(),
            $club->getBudget(),
        );

        $this->assertEquals(
            $club->getName(),
            $savedReferee->getClub()->getName(),
        );

        $this->assertEquals(
            $command->salary(),
            $savedReferee->getSalary(),
        );

        $this->assertEquals(
            $command->name(),
            $savedReferee->getName(),
        );

        $this->assertEquals(
            $command->club(),
            $savedReferee->getClub()->getName(),
        );
    }

    public function testICantCreateARefereeWithClubIfClubDoesNotExist(): void
    {
        $command = new CreateRefereeCommand(
            name: 'sample',
            salary: 10,
            club: 'club',
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->club())
            ->willReturn(null);

        $handler = new CreateRefereeHandler(
            $this->mockClubRepository,
            $this->mockRefereeRepository,
        );

        $this->expectException(ClubNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Club "%s" not found', $command->club()));

        ($handler)($command);
    }

    public function testICantCreateARefereeWithClubIfClubDoesNotHaveEnoughBudget(): void
    {
        $command = new CreateRefereeCommand(
            name: 'sample',
            salary: 10,
            club: 'club',
        );

        $this->mockRefereeRepository
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

        $handler = new CreateRefereeHandler(
            $this->mockClubRepository,
            $this->mockRefereeRepository,
        );

        ($handler)($command);
    }
}
