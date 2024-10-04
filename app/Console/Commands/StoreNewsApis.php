<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\NewsSourceController;

class StoreNewsApis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:store-news-apis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It Store News Apis in MySql database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('News Apis Stored Successfully!');

        $newsSourceController = new NewsSourceController();
        
        foreach (['The Guardian Api', 'News Api', 'New York Times Api'] as $source) {
            $request = new Request(['news_source' => $source]);
            $newsSourceController->store($request);
        }

    }
}
