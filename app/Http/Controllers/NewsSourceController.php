<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\common;
use App\Models\NewsSource;
use Illuminate\Http\Request;

class NewsSourceController extends Controller
{
    protected $user;
    protected $newsSource;

    public function __construct()
    {
        $this->user = new User();
        $this->newsSource = new NewsSource();
    }

    public function index(Request $request)
    {
        try{
            $newsSources = $this->filterNewsSources($request)->get();

            return response()->json([
                'status' => true,
                'message' => 'list of news articles',
                'data' => $newsSources,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while storing news articles: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {

            if($request->news_source && $request->news_source == "The Guardian Api") {

                return $this->storeTheGuardianNews();
            }
            if($request->news_source && $request->news_source == "News Api") {

                return $this->storeNewsApi();
            }
            if($request->news_source && $request->news_source == "New York Times Api") {

                return $this->storeNewYorkTimesApi();
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid source. Please provide valid source.',
                ], 400);
            }
            
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while storing news articles: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try{
            $newsSource = $this->newsSource->find($id);

            if(!$newsSource)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'news article not found',
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'show of news article',
                'data' => $newsSource,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while storing news articles: ' . $e->getMessage(),
            ], 500);
        }
    }    

    private function getAuthors($tags)
    {
        $authors = [];
        foreach ($tags as $tag) {
            if ($tag->type === 'contributor') {
                $authors[] = $tag->webTitle;
            }
        }
        return implode(', ', $authors);
    }

    private function storeTheGuardianNews()
    {
        $apiKey = env('THE_GUARDIANS_API_KEY');
        $response = common::fetchTheGuardianArticles($apiKey);
        
        if ($response->response->status === 'ok' && !empty($response->response->results)) {
            foreach ($response->response->results as $article) {

                $this->newsSource->updateOrCreate(
                    ['unique_id' => $article->id],
                    [
                        'source' => 'The Guardian',
                        'category' => $article->sectionName ?? null,
                        'author' => $this->getAuthors($article->tags),
                        'keyword' => 'bitcoin',
                        'publish_date' => $this->formatPublishDate($article->webPublicationDate ?? null),
                        'article' => json_encode($article),
                    ]
                );
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'The Guardian News Api articles stored successfully',
        ]); 
    }

    private function storeNewsApi()
    {
        $apiKey = env('NEWS_API_KEY'); 
        
        $response = common::fetchNewsApiArticles($apiKey);

        if ($response->status === 'ok' && !empty($response->articles)) {
            foreach ($response->articles as $article) {

                $this->newsSource->updateOrCreate(
                    ['unique_id' => $article->url],
                    [
                        'source' => 'News API',
                        'category' => $article->source->name ?? null,
                        'author' => $article->author ?? null,
                        'publish_date' => $this->formatPublishDate($article->publishedAt ?? null),
                        'keyword' => 'bitcoin',
                        'article' => json_encode($article),
                    ]
                );
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'News Api articles stored successfully',
        ]);
    }

    private function storeNewYorkTimesApi()
    {
        $apiKey = env('NEW_YORK_TIMES_API_KEY'); 
        
        $response = common::fetchNewYorkTimesApiArticles($apiKey);

        if (isset($response->response->docs) && !empty($response->response->docs)) {
            $articles = $response->response->docs;

            $count = 0; 
            foreach ($articles as $article) {
                if ($count >= 200) {
                    break;
                }
                $this->newsSource->updateOrCreate(
                    ['unique_id' => $article->_id ?? null],
                    [
                        'source' => 'The New York Times',
                        'category' => $article->section_name ?? null,
                        'author' => $this->getNYTAuthors($article->byline->person ?? []),
                        'keyword' => 'bitcoin',
                        'publish_date' => $this->formatPublishDate($article->pub_date ?? null),
                        'article' => json_encode($article),
                    ]
                );

                $count++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'News Api articles stored successfully',
        ]);
    }

    private function getNYTAuthors($persons)
    {
        return implode(', ', array_map(function($person) {
            return trim($person->firstname . ' ' . $person->lastname);
        }, $persons));
    }

    protected function filterNewsSources(Request $request)
    {
        $query = $this->newsSource->newQuery();

        if ($request->has('keyword')) {
            $query->where('keyword', 'like', '%' . $request->input('keyword') . '%')
                ->orWhere('article', 'like', '%' . $request->input('keyword') . '%');
        }

        if ($request->has('date')) {
            $date = Carbon::parse($request->input('date'))->format('Y-m-d');
            $query->whereDate('publish_date', $date);
        }

        if ($request->has('category')) {
            $query->where('category','like', '%'.$request->input('category').'%');
        }

        if ($request->has('source')) {
            $query->where('source', 'like', '%'.$request->input('source').'%');
        }

        return $query;
    }

    protected function formatPublishDate($pubDate)
    {
        if ($pubDate) {

            return Carbon::parse($pubDate)->format('Y-m-d H:i:s');
        }
        return null;
    }

}
