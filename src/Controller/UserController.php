<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Dotenv\Dotenv;
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
     * @Route("/api/request/summoner/{summonerName}", name="summonerAPI")
     */
    public function summonerAPI($summonerName): JsonResponse
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

        return JsonResponse::create($summoner, '200');
    }

    /**
     * @Route("/api/request/match/{summonerID}", name="matchAPI")
     */
    public function matchAPI($summonerID): JsonResponse
    {
        $api = new LeagueAPI([
            LeagueAPI::SET_KEY    => getenv('RIOT_KEY'),
            LeagueAPI::SET_REGION => Region::EUROPE_WEST,
        ]);

        try {
            $matchList = $api->getMatchlistByAccount($summonerID);
        } catch (\Throwable $e) {
            $matchList = $e;
            return JsonResponse::create($matchList, '404');
        }

        foreach ($matchList->matches as $game) {
            $games[] = $game;
        }

        return JsonResponse::create($games, '200');
    }

    /**
     * @Route("/api/request/register", name="register", methods="POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);

        if (!empty($data['username']) && !empty($data['email']) != null && !empty($data['password'])) {

            $api = new LeagueAPI([
                LeagueAPI::SET_KEY    => getenv('RIOT_KEY'),
                LeagueAPI::SET_REGION => Region::EUROPE_WEST,
            ]);

            try {
                $api->getSummonerByName($data['username']);
            } catch (\Throwable $e) {
                return JsonResponse::create('no summoner with this username', '404');
            }

            $user = new Utilisateur();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return JsonResponse::create('user created','200');
        }
        else {
            return JsonResponse::create('username, email and password are needed','400');
        }
    }

    /**
     * @Route("/api/request/login", name="login", methods="POST")
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
                    return JsonResponse::create($user,'200');
                }
                else {
                    return JsonResponse::create('this user is ban','404');
                }
            }
            else {
                return JsonResponse::create('bad password','400');
            }
        }
        else {
            return JsonResponse::create('no user find','404');
        }
    }
}
