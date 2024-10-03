<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\NewsSource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetNewsArticleRequest;
use App\Http\Requests\SetNewsArticleRequest;

class NewsArticleController extends Controller
{
    protected $user;
    protected $newsSource;

    public function __construct()
    {
        $this->user = new User();
        $this->newsSource = new NewsSource();
    }

    public function setNewsArticles(SetNewsArticleRequest $request)
    {
        $setThrough = $request->set_through;
        $favorite = $request->favorite;
        $userId = $request->user()->id; 
        
        if($setThrough == 'source') {
            return $this->setNewsArticlesBy($favorite, $userId, $setThrough);
        } elseif($setThrough == 'category') {
            return $this->setNewsArticlesBy($favorite, $userId, $setThrough);
        } elseif($setThrough == 'author') {
            return $this->setNewsArticlesBy($favorite, $userId, $setThrough);
        }
    }

    public function index(GetNewsArticleRequest $request)
    {
        $myFavoriteArticles = $this->user->with('newsSources')->where('id', auth()->user()->id)->first();
        $getThrough = $request->get_through;
        $myFavorite = $request->my_favorite;
        
        if (in_array($getThrough, ['source', 'category', 'author'])) {
            return $this->getNewsArticlesBy($myFavorite, $myFavoriteArticles, $getThrough);
        }
    
        return response()->json([
            'status' => false,
            'message' => 'Invalid filtering option provided.',
        ]);
    }

    protected function setNewsArticlesBy($favorite, $userId, $setThrough)
    {
        $articles = $this->newsSource->where($setThrough,'like','%'.$favorite.'%')->get();
            if($articles->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No articles found related to your given favorite ' .$setThrough,
                ]);
            }
            foreach ($articles as $article) {
                if (!$article->users()->where('user_id', $userId)->exists()) {
                    $article->users()->attach($userId);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Articles set through '.$setThrough.' successfully',
            ]);
    }

    protected function getNewsArticlesBy($myFavorite, $myFavoriteArticles, $getThrough)
    {
        if ($myFavoriteArticles && $myFavoriteArticles->newsSources->isNotEmpty()) {
            $filteredArticles = $myFavoriteArticles->newsSources->filter(function ($newsSource) use ($myFavorite, $getThrough) {

                switch ($getThrough) {
                    case 'source':
                        return stripos($newsSource->source, $myFavorite) !== false;
                    case 'category':
                        return stripos($newsSource->category, $myFavorite) !== false;
                    case 'author':
                        return stripos($newsSource->author, $myFavorite) !== false;
                    default:
                        return false;
                }
            });
    
            if ($filteredArticles->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No articles found related to your given favorite ' . $getThrough,
                ]);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Filtered articles retrieved successfully',
                'data' => $filteredArticles->values(),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No news articles found for the user',
            ]);
        }
    }
}
