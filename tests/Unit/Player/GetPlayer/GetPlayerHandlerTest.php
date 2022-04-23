<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Player\GetPlayer;

use Alejgarciarodriguez\SymfonyDemo\Application\Player\GetPlayer\GetPlayerHandler;
use Alejgarciarodriguez\SymfonyDemo\Application\Player\GetPlayer\GetPlayerQuery;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\PlayerNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;
use PHPUnit\Framework\TestCase;

final class GetPlayerHandlerTest extends TestCase
{
    private PlayerRepository $mockPlayerRepository;

    protected function setUp(): void
    {
        $this->mockPlayerRepository = $this->createMock(PlayerRepository::class);
    }

    public function testICanFindAPlayerIfExists(): void
    {
        $player = new Player(
            name: 'sample',
            salary: 10,
            club: null,
        );

        $handler = new GetPlayerHandler(
            $this->mockPlayerRepository,
        );

        $query = new GetPlayerQuery(
            name: 'sample',
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($query->name())
            ->willReturn($player);

        $result = ($handler)($query);

        $this->assertSame(
            $player,
            $result,
        );
    }

    public function testICantFindAPlayerIfDoesNotExists(): void
    {
        $handler = new GetPlayerHandler(
            $this->mockPlayerRepository,
        );

        $query = new GetPlayerQuery(
            name: 'sample',
        );

        $this->mockPlayerRepository
            ->expects($this->once())
            ->method('search')
            ->with($query->name())
            ->willReturn(null);

        $this->expectException(PlayerNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Player "%s" not found', $query->name()));

        ($handler)($query);
    }
}
