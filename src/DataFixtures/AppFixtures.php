<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Utilisateur;
use RiotAPI\LeagueAPI\LeagueAPI;
use RiotAPI\LeagueAPI\Definitions\Region;

class AppFixtures extends Fixture
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager) //create 20 user with their email, pseudo riot, password and RiotAccountID
    {
        $summonerArray = ['qweasdf', 'keduii123', 'Pyrkα', 'Umoraki', 'Τhe Inventor', 'Nijhuis', 'Viggo', 'Sysak', 'Lingwin', 'Lumerion', 'antusheng', 'Bennyy', 'Kisze', 'Aboekra', 'Manaty', 'Zedalithy', 'Kyuo', 'JealousMoon', 'eyaN', 'nikoflk'];

        $api = new LeagueAPI([
            LeagueAPI::SET_KEY => getenv('RIOT_KEY'),
            LeagueAPI::SET_REGION => Region::EUROPE_WEST,
        ]);

        $i =0;
        foreach ($summonerArray as $summonerName) {
            $summoner = $api->getSummonerByName($summonerName);
            $user = new Utilisateur();
            $user->setUsername($summonerName);
            $user->setEmail('mail'.$i.'@gmail.com');
            $user->setPassword('user');
            $user->setRiotAccountId($summoner->accountId);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $i++;
        }
    }
}
