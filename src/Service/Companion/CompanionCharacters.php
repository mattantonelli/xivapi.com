<?php

namespace App\Service\Companion;

use App\Entity\CompanionCharacter;
use App\Repository\CompanionCharacterRepository;
use App\Service\Content\GameServers;
use Doctrine\ORM\EntityManagerInterface;
use Lodestone\Api;
use Symfony\Component\Console\Output\ConsoleOutput;

class CompanionCharacters
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var CompanionCharacterRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository(CompanionCharacter::class);
    }

    public function populate()
    {
        $console    = new ConsoleOutput();
        $characters = $this->repository->findBy([ 'lodestoneId' => null ], [ 'added' => 'asc' ], 20);

        $console->writeln(count($characters) ." characters");
        $section = $console->section();

        $api = new Api();

        /** @var CompanionCharacter $character */
        foreach ($characters as $character) {
            $server = GameServers::LIST[$character->getServer()];
            $name   = $character->getName();
            $date   = date('H:i:s');

            $section->overwrite("[{$date}] {$name} - {$server}");
            $results = $api->searchCharacter($name, $server);

            // found none
            if ($results->Pagination->ResultsTotal == 0) {
                continue;
            }

            // loop through
            foreach ($results->Results as $res) {
                if ($res->Name == $name && $res->Server == $server) {
                    $character->setLodestoneId($res->ID);
                    $this->em->persist($character);
                    $this->em->flush();
                    break;
                }
            }
        }


    }
}
