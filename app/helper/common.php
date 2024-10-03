<?php

namespace App\Helpers;

use DateTimeImmutable;
use GuzzleHttp\Client;
use Guardian\GuardianAPI;
use App\Models\NewsSource;
use GuzzleHttp\Exception\RequestException;

class common
{
    public static function fetchTheGuardianArticles($apiKey, $pageSize = 100)
    {
        $api = new GuardianAPI($apiKey);
        $page = 1;
            return $api->content()
                ->setQuery("bitcoin")
                ->setTag("tone/news")
                ->setFromDate(new DateTimeImmutable("01/01/2010"))
                ->setToDate(new DateTimeImmutable())
                ->setShowTags("contributor,section")
                ->setShowFields("starRating,headline,thumbnail,short-url,sectionId,sectionName")
                ->setPageSize($pageSize)
                ->setPage($page)
                ->fetch();
    }

    public static function fetchNewsApiArticles($apiKey)
    {
        $url = 'https://newsapi.org/v2/everything?q=bitcoin&apiKey=' . $apiKey;

        $client = new Client();

        try {
            $response = $client->request('GET', $url);

            $body = $response->getBody();

            $data = json_decode($body);

            return $data;
        } catch (RequestException $e) {

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching news articles: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public static function fetchNewYorkTimesApiArticles($apiKey)
    {
        $url = 'https://api.nytimes.com/svc/archive/v1/2024/1.json?api-key=' . $apiKey;

        $client = new Client();

        try {
            $response = $client->request('GET', $url);

            $body = $response->getBody();

            $data = json_decode($body);

            return $data;
        } catch (RequestException $e) {

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching news articles: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
