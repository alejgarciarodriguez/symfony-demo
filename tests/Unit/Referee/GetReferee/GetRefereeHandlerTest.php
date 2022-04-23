<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Unit\Referee\GetReferee;

use Alejgarciarodriguez\SymfonyDemo\Application\Referee\GetReferee\GetRefereeHandler;
use Alejgarciarodriguez\SymfonyDemo\Application\Referee\GetReferee\GetRefereeQuery;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\RefereeNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;
use PHPUnit\Framework\TestCase;

final class GetRefereeHandlerTest extends TestCase
{
    private RefereeRepository $mockRefereeRepository;

    protected function setUp(): void
    {
        $this->mockRefereeRepository = $this->createMock(RefereeRepository::class);
    }

    public function testICanFindAnExistingReferee(): void
    {
        $referee = new Referee(
            name: 'sample',
            salary: 10,
            club: null,
        );

        $handler = new GetRefereeHandler(
            $this->mockRefereeRepository,
        );

        $query = new GetRefereeQuery(
            name: 'sample',
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($query->name())
            ->willReturn($referee);

        $result = ($handler)($query);

        $this->assertSame(
            $referee,
            $result,
        );
    }

    public function testICantFindANonExistingReferee(): void
    {
        $handler = new GetRefereeHandler(
            $this->mockRefereeRepository,
        );

        $query = new GetRefereeQuery(
            name: 'sample',
        );

        $this->mockRefereeRepository
            ->expects($this->once())
            ->method('search')
            ->with($query->name())
            ->willReturn(null);

        $this->expectException(RefereeNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Referee "%s" not found', $query->name()));

        ($handler)($query);
    }
}
