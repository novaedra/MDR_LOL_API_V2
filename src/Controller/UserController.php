<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use RiotAPI\LeagueAPI\LeagueAPI;
use RiotAPI\LeagueAPI\Definitions\Region;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/user/summoner/{summonerName}", name="summonerAPI")
     */
    public function summonerAPI($summonerName): JsonResponse //need username and return summoner info
    {
        $api = new LeagueAPI([
            LeagueAPI::SET_KEY    => getenv('RIOT_KEY'),
            LeagueAPI::SET_REGION => Region::EUROPE_WEST,
        ]);

        try {
            $summoner = $api->getSummonerByName($summonerName);
        } catch (\Throwable $e) {
            $summoner = $e;
            return JsonResponse::create('no summoner with this username', '404');
        }

        return JsonResponse::create($summoner, 200);
    }

    /**
     * @Route("/api/user/match/{RIOTaccountID}/{pagination}", name="matchAPI")
     */
    public function matchAPI($RIOTaccountID, $pagination = 0): JsonResponse //need riotAccountID and return 5 matches
    {
        $api = new LeagueAPI([
            LeagueAPI::SET_KEY    => getenv('RIOT_KEY'),
            LeagueAPI::SET_REGION => Region::EUROPE_WEST,
        ]);

        try {
            $matchList = $api->getMatchlistByAccount($RIOTaccountID, null, null, null, null, null, 0 + ($pagination * 5), 5 + ($pagination * 5));
        } catch (\Throwable $e) {
            return JsonResponse::create($e, 404);
        }

        $i = 1;
        foreach ($matchList->matches as $game) {
            $games['match : '.$i] = $game;
            $games['stats : '.$i] = $api->getMatch($game->gameId);
            $i++;
        }

        return JsonResponse::create($games, 200);
    }

    /**
     * @Route("/api/user/register", name="register", methods="POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse //register with RIOT_username and email/password
    {
        $data = json_decode($request->getContent(),true);

        if (!empty($data['username']) && !empty($data['email']) != null && !empty($data['password'])) {

            $api = new LeagueAPI([
                LeagueAPI::SET_KEY    => getenv('RIOT_KEY'),
                LeagueAPI::SET_REGION => Region::EUROPE_WEST,
            ]);

            $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
            $emailTaken = $repository->findOneBy([
                'email' => 'mail9@gmail.com',
            ]);
            if ($emailTaken === null) {
                try {
                    $riotUser = $api->getSummonerByName($data['username']);
                } catch (\Throwable $e) {
                    return JsonResponse::create('no summoner with this username', 404);
                }

                $user = new Utilisateur();
                $user->setUsername($data['username']);
                $user->setEmail($data['email']);
                $user->setPassword($data['password']);
                $user->setRiotAccountId($riotUser->accountId);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return JsonResponse::create('user created','200');
            }
            else {
                return JsonResponse::create('email already taken', 400);
            }
        }
        else {
            return JsonResponse::create('username, email and password are needed',400);
        }
    }

    /**
     * @Route("/api/user/login", name="login", methods="POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($request->getContent(),true);

        if (!empty($data['email']) != null && !empty($data['password'])) {
            $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
            $user = $repository->findOneBy([
                'email' => $data['email'],
            ]);

            if (password_verify($data['password'], $user->getPassword())) {
                $user = $serializer->serialize($user,'json');
                $user = json_decode($user, true);

                if ($user['active'] === true) {
                    return JsonResponse::create($user,200);
                }
                else {
                    return JsonResponse::create('this user is ban',404);
                }
            }
            else {
                return JsonResponse::create('bad password',400);
            }
        }
        else {
            return JsonResponse::create('no user find',404);
        }
    }
}
