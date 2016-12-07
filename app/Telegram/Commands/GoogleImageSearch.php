<?php

namespace App\Telegram\Commands;

use App\GoogleCustomSearch\Facades\GoogleCSE;
use App\Telegram\Transformers\SearchResultTransformer;
use Telegram\Bot\Commands\Command;

/**
 * Class GoogleSearch.
 */
class GoogleImageSearch extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'images';

    /**
     * @var string Command Description
     */
    protected $description = 'Search on images.google.com';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        if(empty($arguments)) {
            $text = 'You must enter at least one search term.';
            return $this->replyWithMessage(compact('text'));
        }

        $search_response = GoogleCSE::search($arguments, 1, 3, ['searchType' => 'image']);

        if($search_response->total_results <= 0) {
            $text = 'Sorry no results.';
            return $this->replyWithMessage(compact('text'));
        }

        $html = [];
        foreach ($search_response->results as $result) {
            // $text = '<a href="' . $result->link . '">' . $result->title . '</a>';
            // $parse_mode = 'HTML';
            $text = SearchResultTransformer::transform($result);
            $parse_mode = 'HTML';

            $this->replyWithMessage(compact('text', 'parse_mode'));
        }
        $text = implode('', $html);
    }
}