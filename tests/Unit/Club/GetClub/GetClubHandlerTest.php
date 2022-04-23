<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Club\GetClub;

use Alejgarciarodriguez\SymfonyDemo\Application\Club\GetClub\GetClubHandler;
use Alejgarciarodriguez\SymfonyDemo\Application\Club\GetClub\GetClubQuery;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use PHPUnit\Framework\TestCase;

final class GetClubHandlerTest extends TestCase
{
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockClubRepository = $this->createMock(ClubRepository::class);
    }

    public function testICanFindAClubThatExists(): void
    {
        $query = new GetClubQuery(
            name: 'club',
        );

        $club = new Club(
            name: $query->name(),
            budget: 100,
        );

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($query->name())
            ->willReturn($club);

        $handler = new GetClubHandler(
            $this->mockClubRepository,
        );

        $result = ($handler)($query);

        $this->assertSame(
            $club,
            $result,
        );
    }

    public function testICantFindAClubThatDoesNotExist(): void
    {
        $query = new GetClubQuery(
            name: 'club',
        );

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->with($query->name())
            ->willReturn(null);

        $handler = new GetClubHandler(
            $this->mockClubRepository,
        );

        $this->expectException(ClubNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Club "%s" not found', $query->name()));

        ($handler)($query);
    }
}
