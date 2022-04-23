<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Club\UpdateClubBudget;

use Alejgarciarodriguez\SymfonyDemo\Application\Club\UpdateClubBudget\UpdateClubBudgetCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Club\UpdateClubBudget\UpdateClubBudgetHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\InsufficientClubBudgetException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use PHPUnit\Framework\TestCase;

final class UpdateClubBudgetHandlerTest extends TestCase
{
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockClubRepository = $this->createMock(ClubRepository::class);
    }

    public function testICantUpdateBudgetIfClubDoesNotExist(): void
    {
        $command = new UpdateClubBudgetCommand(
            name: 'Aclub',
            budget: 100,
        );

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn(null);

        $handler = new UpdateClubBudgetHandler(
            $this->mockClubRepository,
        );

        $this->expectException(ClubNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Club "%s" not found', $command->name()));

        ($handler)($command);
    }

    public function testICanUpdateBudgetIfIsLowerThanClubTotalSalaries(): void
    {
        $command = new UpdateClubBudgetCommand(
            name: 'Aclub',
            budget: 15,
        );

        $club = new Club(
            name: $command->name(),
            budget: 10,
        );

        $club->addPlayer(new Player(
            name: $club->getName(),
            salary: 10,
            club: $club,
        ));

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($club);

        $handler = new UpdateClubBudgetHandler(
            $this->mockClubRepository,
        );

        ($handler)($command);

        $this->assertEquals(
            $command->budget(),
            $club->getBudget(),
        );
    }

    public function testICantUpdateBudgetWhenItsGreaterThanTotalSalaries(): void
    {
        $command = new UpdateClubBudgetCommand(
            name: 'Aclub',
            budget: 5,
        );

        $club = new Club(
            name: $command->name(),
            budget: 10,
        );

        $club->addPlayer(new Player(
            name: $club->getName(),
            salary: 10,
            club: $club,
        ));

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->name())
            ->willReturn($club);

        $handler = new UpdateClubBudgetHandler(
            $this->mockClubRepository,
        );

        $this->expectException(InsufficientClubBudgetException::class);
        $this->expectExceptionMessage(\sprintf('Insufficient budget for club "%s"', $command->name()));

        ($handler)($command);
    }
}
