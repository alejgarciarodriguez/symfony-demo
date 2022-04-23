<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Fixtures;

use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class Fixtures extends Fixture
{
    public function load(ObjectManager $referee): void
    {
        $this->saveEmptyClub($referee);
        $this->savePlayerWithNoClub($referee);
        $this->saveManagerWithNoClub($referee);
        $this->saveClubWithPlayersAndManagers($referee);
    }

    private function saveEmptyClub(ObjectManager $referee): void
    {
        $emptyClub = new Club(
            name: 'Córdoba',
            budget: 1_000_000,
        );

        $referee->persist($emptyClub);
        $referee->flush();
    }

    private function saveClubWithPlayersAndManagers(ObjectManager $referee): void
    {
        $clubWithPlayersAndManagers = new Club(
            name: 'Jaén',
            budget: 1_000_000,
        );

        $player1 = new Player(
            name: 'Alejandro',
            salary: 10_000,
            club: $clubWithPlayersAndManagers,
        );
        $player2 = new Player(
            name: 'Andrés',
            salary: 8_000,
            club: $clubWithPlayersAndManagers,
        );
        $referee1 = new Referee(
            name: 'Jesús',
            salary: 20_000,
            club: $clubWithPlayersAndManagers,
        );
        $referee2 = new Referee(
            name: 'Víctor',
            salary: 16_000,
            club: $clubWithPlayersAndManagers,
        );

        $clubWithPlayersAndManagers->addReferee($referee1);
        $clubWithPlayersAndManagers->addReferee($referee2);
        $clubWithPlayersAndManagers->addPlayer($player1);
        $clubWithPlayersAndManagers->addPlayer($player2);

        $referee->persist($clubWithPlayersAndManagers);

        $referee->persist($player1);
        $referee->persist($player2);

        $referee->persist($referee1);
        $referee->persist($referee2);

        $referee->flush();
    }

    private function savePlayerWithNoClub(ObjectManager $referee): void
    {
        $player = new Player(
            name: 'José',
            salary: 12_000,
            club: null,
        );

        $referee->persist($player);
        $referee->flush();
    }

    private function saveManagerWithNoClub(ObjectManager $om): void
    {
        $referee = new Referee(
            name: 'Sergio',
            salary: 14_000,
            club: null,
        );

        $om->persist($referee);
        $om->flush();
    }
}
