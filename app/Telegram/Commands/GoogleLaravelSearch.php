<?php

namespace App\Telegram\Commands;

use App\GoogleCustomSearch\Facades\GoogleCSE;
use App\Telegram\Transformers\SearchResultTransformer;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

/**
 * Class GoogleSearch.
 */
class GoogleLaravelSearch extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'laravel';

    /**
     * @var string Command Description
     */
    protected $description = 'Search on laravel.com/docs';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        if(empty($arguments)) {
            $text = 'You must enter at least one search term.';
            return $this->replyWithMessage(compact('text'));
        }

        $search_response = GoogleCSE::search($arguments, 1, 1, ['siteSearch' => 'https://laravel.com/docs']);
        if($search_response->total_results <= 0) {
            $text = 'Sorry no results.';
            return $this->replyWithMessage(compact('text'));
        }

        $html = [];
        foreach ($search_response->results as $result) {
            $text = SearchResultTransformer::transform($result);
            $parse_mode = 'HTML';

            try {
                $this->replyWithMessage(compact('text', 'parse_mode'));
            } catch (\Exception $exception) {
                app('sentry')->captureException($exception);
            }
        }
        $text = implode('', $html);
    }
}
