<?php

namespace App\Controller;

use RiotAPI\LeagueAPI\Exceptions\GeneralException;
use RiotAPI\LeagueAPI\Exceptions\RequestException;
use RiotAPI\LeagueAPI\Exceptions\ServerException;
use RiotAPI\LeagueAPI\Exceptions\ServerLimitException;
use RiotAPI\LeagueAPI\Exceptions\SettingsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Routing\Annotation\Route;
use RiotAPI\LeagueAPI\LeagueAPI;
use RiotAPI\LeagueAPI\Definitions\Region;

class UserController extends AbstractController
{
    /**
     * @Route("/api/request/summoner/{summonerName}", name="summoner")
     */
    public function getSummoner($summonerName): JsonResponse
    {
        $api = new LeagueAPI([
            LeagueAPI::SET_KEY    => getenv('RIOT_KEY'),
            LeagueAPI::SET_REGION => Region::EUROPE_WEST,
        ]);

        try {
            $summoner = $api->getSummonerByName($summonerName);
        } catch (\Throwable $e) {
            $summoner = $e;
            return JsonResponse::create($summoner, '404');
        }

        return JsonResponse::create($summoner, '200');
    }

    /**
     * @Route("/api/request/match/{summonerID}", name="match")
     */
    public function match($summonerID): JsonResponse
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
     * @Route("/api/request/test/{summonerID}", name="test")
     */
    public function test($summonerID): JsonResponse
    {

    }
}
