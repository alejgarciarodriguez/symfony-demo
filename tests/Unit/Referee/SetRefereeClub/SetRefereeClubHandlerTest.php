<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Referee\SetRefereeClub;

use Alejgarciarodriguez\SymfonyDemo\Application\Referee\SetRefereeClub\SetRefereeClubCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Referee\SetRefereeClub\SetRefereeClubHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\RefereeNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;
use PHPUnit\Framework\TestCase;

final class SetRefereeClubHandlerTest extends TestCase
{
    private RefereeRepository $mockrefereeRepository;
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockrefereeRepository = $this->createMock(RefereeRepository::class);
        $this->mockClubRepository    = $this->createMock(ClubRepository::class);
    }

    public function testICanModifyClubIfRefereeHasNoClub(): void
    {
        $command = new SetRefereeClubCommand(
            refereeName: 'Areferee',
            clubName: 'Aclub',
        );

        $player = new Referee(
            name: $command->refereeName(),
            salary: 1,
            club: null,
        );

        $club = new Club(
            name: 'Aclub',
            budget: 10,
        );

        $this->mockrefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->refereeName())
            ->willReturn($player);

        $this->mockrefereeRepository
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

        $handler = new SetRefereeClubHandler(
            $this->mockrefereeRepository,
            $this->mockClubRepository,
        );

        $this->assertNull($player->getClub());
        $this->assertNotContains($player, $club->getReferees());

        ($handler)($command);

        $this->assertSame($club, $player->getClub());
        $this->assertContains($player, $club->getReferees());
    }

    public function testICanModifyClubIfRefereeHasAlreadyAClub(): void
    {
        $command = new SetRefereeClubCommand(
            refereeName: 'Areferee',
            clubName: 'AnotherClub',
        );

        $referee = new Referee(
            name: $command->refereeName(),
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

        $club->addReferee($referee);

        $this->mockrefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->refereeName())
            ->willReturn($referee);

        $this->mockrefereeRepository
            ->expects($this->once())
            ->method('save')
            ->with($referee);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->clubName())
            ->willReturn($anotherClub);

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save')
            ->with($anotherClub);

        $handler = new SetRefereeClubHandler(
            $this->mockrefereeRepository,
            $this->mockClubRepository,
        );

        $this->assertNotNull($referee->getClub());
        $this->assertContains($referee, $club->getReferees());
        $this->assertNotContains($referee, $anotherClub->getReferees());

        ($handler)($command);

        $this->assertSame($anotherClub, $referee->getClub());
        $this->assertContains($referee, $anotherClub->getReferees());
        $this->assertNotContains($referee, $club->getReferees());
    }

    public function testICantModifyClubIfDoesNotExist(): void
    {
        $command = new SetRefereeClubCommand(
            refereeName: 'Areferee',
            clubName: 'Aclub',
        );

        $player = new Referee(
            name: $command->refereeName(),
            salary: 1,
            club: null,
        );

        $this->mockrefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->refereeName())
            ->willReturn($player);

        $this->mockrefereeRepository
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

        $handler = new SetRefereeClubHandler(
            $this->mockrefereeRepository,
            $this->mockClubRepository,
        );

        $this->expectException(ClubNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Club "%s" not found', $command->clubName()));

        ($handler)($command);
    }

    public function testICantModifyClubIfPlayerDoesNotExist(): void
    {
        $command = new SetRefereeClubCommand(
            refereeName: 'Areferee',
            clubName: 'Aclub',
        );

        $this->mockrefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($command->refereeName())
            ->willReturn(null);

        $this->mockrefereeRepository
            ->expects($this->never())
            ->method('save');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('search');

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $handler = new SetRefereeClubHandler(
            $this->mockrefereeRepository,
            $this->mockClubRepository,
        );

        $this->expectException(RefereeNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Referee "%s" not found', $command->refereeName()));

        ($handler)($command);
    }
}
