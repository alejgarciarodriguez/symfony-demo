<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Club\CreateClub;

use Alejgarciarodriguez\SymfonyDemo\Application\Club\CreateClub\CreateClubCommand;
use Alejgarciarodriguez\SymfonyDemo\Application\Club\CreateClub\CreateClubHandler;
use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use PHPUnit\Framework\TestCase;

final class CreateClubHandlerTest extends TestCase
{
    private ClubRepository $mockClubRepository;

    protected function setUp(): void
    {
        $this->mockClubRepository = $this->createMock(ClubRepository::class);
    }

    public function testICanCreateAClub(): void
    {
        $command = new CreateClubCommand(
            name: 'sample',
            budget: 0,
        );

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn(null)
            ->with($command->name());

        $savedClub = null;

        $this->mockClubRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Club $club) use (&$savedClub) {
                $savedClub = $club;
            });

        $handler = new CreateClubHandler($this->mockClubRepository);

        ($handler)($command);

        $this->assertEquals($command->name(), $savedClub->getName());
        $this->assertEquals($command->budget(), $savedClub->getBudget());
    }

    public function testClubIsNotCreatedIfAlreadyExists(): void
    {
        $command = new CreateClubCommand(
            name: 'sample',
            budget: 0,
        );

        $club = new Club(
            $command->name(),
            $command->budget(),
        );

        $this->mockClubRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn($club)
            ->with($command->name());

        $this->mockClubRepository
            ->expects($this->never())
            ->method('save');

        $handler = new CreateClubHandler($this->mockClubRepository);

        ($handler)($command);
    }
}
