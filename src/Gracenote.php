<?php

namespace Atomescrochus\Gracenote;

use Illuminate\Support\Facades\Cache;
use Atomescrochus\Gracenote\Exceptions\UsageErrors;
use Atomescrochus\Gracenote\Exceptions\RequiredConfigMissing;
use Atomescrochus\Gracenote\Exceptions\MissingRequiredParameters;

class Gracenote
{
    private $client_id;
    private $client_tag;
    private $user_id;
    private $request_url;

    public $query_cmd;
    public $lang;
    public $search_type;
    public $search_mode;
    public $search_terms;
    public $cache;
    public $options;
    public $possible_search_types;
    public $possible_search_modes;

    public function __construct()
    {
        $this->setParameters();

        $this->lang = 'eng';
        $this->search_terms = null;
        $this->query_cmd = 'album_search'; // default
        $this->search_type = 'TRACK_TITLE';
        $this->search_mode = '';
        $this->possible_search_types = ['track_title', 'album_title', 'artist'];
        $this->possible_search_modes = ['SINGLE_BEST', 'SINGLE_BEST_COVER'];
        $this->options = '<OPTION><PARAMETER>SELECT_EXTENDED</PARAMETER><VALUE>COVER,REVIEW,ARTIST_BIOGRAPHY,ARTIST_IMAGE,ARTIST_OET,MOOD,TEMPO</VALUE></OPTION>';
    }

    /**
     * Sets the time in minutes to cache the search results.
     * @param  int $cache A number of minutes
     */
    public function cache(int $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    public function getTrackById($id)
    {
        // need refacotring into everything else.
        $this->query_cmd = 'ALBUM_FETCH';
        $lang = "<LANG>{strtoupper($this->lang)}</LANG>";
        $auth = "<AUTH><CLIENT>{$this->client_id}-{$this->client_tag}</CLIENT><USER>{$this->user_id}</USER></AUTH>";
        $query = '<QUERY CMD="'.$this->query_cmd.'"><GN_ID>'.$id.'</GN_ID></QUERY>';
        $payload = "<QUERIES>{$lang}{$auth}{$query}</QUERIES>";

        $cache_name = bcrypt("gn_track_id-{$id}");

        $result = Cache::remember($cache_name, $this->cache, function () use ($payload) {
            return $this->searchGracenote($payload);
        });

        return $result;
    }

    /**
     * Set the search mode.
     * @param  string $type One of the search mode as defined by Gracenote API docs.
     */
    public function searchMode($mode)
    {
        if (! in_array($mode, $this->possible_search_modes)) {
            throw UsageErrors::searchMode();
        }

        $this->search_mode = "<MODE>{$mode}</MODE>";

        return $this;
    }

    /**
     * Set the "prefered natural language of metadata".
     * @param  string $type One of the search type as defined by Gracenote API docs.
     */
    public function searchType($type)
    {
        if (! in_array($type, $this->possible_search_types)) {
            throw UsageErrors::searchType();
        }

        $this->search_type = $type;

        return $this;
    }

    /**
     * Set the "prefered natural language of metadata".
     * @param  string $lang A three-character language code as defined by ISO 639-2
     */
    public function lang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Set the query to send to Gracenote.
     * @param  string $query The search query
     */
    public function query($query)
    {
        $this->search_terms = $query;

        return $this;
    }

    /**
     * Sends the search.
     * @return collection A collection of results
     */
    public function search()
    {
        if (is_null($this->search_terms)) {
            throw MissingRequiredParameters::searchTerms();
        }

        $mode = $this->search_mode != '' ? $this->search_mode : 'no-search-mode';

        $cache_name = bcrypt("{$mode}-{$this->search_type}-{$this->search_terms}");
        
        $results = Cache::remember($cache_name, $this->cache, function () {
            return $this->searchGracenote();
        });

        return $results;
    }

    /**
     * Send a request for search to Gracenote WebAPI.
     * @return collection A collection of results
     */
    private function searchGracenote($payload = null)
    {
        $payload = is_null($payload) ? $this->xmlPayload() : $payload;
        $response = \Httpful\Request::post($this->request_url)
        ->body($payload)
        ->sendsXml()
        ->send();

        return $this->formatApiResults($response);
    }

    private function formatApiResults($results)
    {
        $raw = $results->raw_body;
        $status = $results->body->RESPONSE[0]->STATUS ? $results->body->RESPONSE[0]->STATUS : null;

        if ($status != "OK") {
            return (object) [
                'status' => $status,
                'raw' => json_decode($raw),
            ];
        }

        if ($this->query_cmd == 'ALBUM_FETCH') {
            $albums = $this->formatSearchTrackById($results->body->RESPONSE[0]->ALBUM);
        }

        if ($this->search_type == 'track_title') {
            $albums = $this->formatSearchTrackTitle($results->body->RESPONSE[0]->ALBUM);
        }

        if ($this->search_type == 'album_title') {
            $albums = $this->formatSearchAlbumTitle($results->body->RESPONSE[0]->ALBUM);
        }

        if ($this->search_type == 'artist') {
            $albums = $this->formatSearchArtist($results->body->RESPONSE[0]->ALBUM);
        }

        return (object) [
            'status' => $status,
            'results' => $albums,
            'raw' => json_decode($raw),
        ];
    }

    private function formatSearchTrackById($raw_albums)
    {
        return collect($raw_albums)->map(function ($item, $key) {
            $formatted = (object) [];

            if (isset($item->GN_ID)) {
                $formatted->gracenote_album_id = $item->GN_ID;
            }

            if (isset($item->TITLE[0]->VALUE)) {
                $formatted->album_title = $item->TITLE[0]->VALUE;
            }

            if (isset($item->ARTIST[0]->VALUE)) {
                $formatted->album_artist = $item->ARTIST[0]->VALUE;
            }

            if (isset($item->GENRE[0]->VALUE)) {
                $formatted->album_genre = $item->GENRE[0]->VALUE;
            }

            if (isset($item->DATE[0]->VALUE)) {
                $formatted->album_year = $item->DATE[0]->VALUE;
            }

            if (isset($item->TRACK_COUNT)) {
                $formatted->track_count = $item->TRACK_COUNT;
            }

            if (isset($item->TRACK)) {
                $formatted->tracks = collect($item->TRACK)->map(function ($item, $key) {
                    $formatted = (object) [];
                    if (isset($item->TRACK_NUM)) {
                        $formatted->track_number = $item->TRACK_NUM;
                    }

                    if (isset($item->GN_ID)) {
                        $formatted->gracenote_track_id = $item->GN_ID;
                    }

                    if (isset($item->TITLE[0]->VALUE)) {
                        $formatted->title = $item->TITLE[0]->VALUE;
                    }

                    return $formatted;
                })->toArray();
            }

            return $formatted;
        });
    }

    private function formatSearchArtist($raw_albums)
    {
        return collect($raw_albums)->map(function ($item, $key) {
            $formatted = (object) [];

            if (isset($item->GN_ID)) {
                $formatted->gracenote_album_id = $item->GN_ID;
            }

            if (isset($item->TITLE[0]->VALUE)) {
                $formatted->album_title = $item->TITLE[0]->VALUE;
            }

            if (isset($item->ARTIST[0]->VALUE)) {
                $formatted->album_artist = $item->ARTIST[0]->VALUE;
            }

            if (isset($item->GENRE[0]->VALUE)) {
                $formatted->album_genre = $item->GENRE[0]->VALUE;
            }

            if (isset($item->DATE[0]->VALUE)) {
                $formatted->album_year = $item->DATE[0]->VALUE;
            }

            if (isset($item->TRACK_COUNT)) {
                $formatted->track_count = $item->TRACK_COUNT;
            }

            if (isset($item->TRACK)) {
                $formatted->tracks = collect($item->TRACK)->map(function ($item, $key) {
                    $formatted = (object) [];
                    if (isset($item->TRACK_NUM)) {
                        $formatted->track_number = $item->TRACK_NUM;
                    }

                    if (isset($item->GN_ID)) {
                        $formatted->gracenote_track_id = $item->GN_ID;
                    }

                    if (isset($item->ARTIST[0]->VALUE)) {
                        $formatted->artist = $item->ARTIST[0]->VALUE;
                    }

                    if (isset($item->TITLE[0]->VALUE)) {
                        $formatted->title = $item->TITLE[0]->VALUE;
                    }

                    return $formatted;
                })->toArray();
            }

            if (isset($item->ARTIST_ORIGIN[0])) {
                $formatted->artist_origin = $item->ARTIST_ORIGIN[0]->VALUE;
            }

            if (isset($item->ARTIST_TYPE[0])) {
                $formatted->artist_type = $item->ARTIST_TYPE[0]->VALUE;
            }

            if (isset($item->ARTIST_ERA[0])) {
                $formatted->artist_era = $item->ARTIST_ERA[0]->VALUE;
            }

            if (isset($item->URL)) {
                foreach ($item->URL as $image) {
                    $formatted->{strtolower($image->TYPE)} = (object) [
                        'size' => strtolower($image->SIZE),
                        'width' => $image->WIDTH,
                        'height' => $image->HEIGHT,
                        'url' => $image->VALUE,
                    ];
                }
            }

            return $formatted;
        });
    }

    private function formatSearchAlbumTitle($raw_albums)
    {
        return collect($raw_albums)->map(function ($item, $key) {
            $formatted = (object) [];

            if (isset($item->GN_ID)) {
                $formatted->gracenote_album_id = $item->GN_ID;
            }

            if (isset($item->TITLE[0]->VALUE)) {
                $formatted->album_title = $item->TITLE[0]->VALUE;
            }

            if (isset($item->ARTIST[0]->VALUE)) {
                $formatted->album_artist = $item->ARTIST[0]->VALUE;
            }

            if (isset($item->GENRE[0]->VALUE)) {
                $formatted->album_genre = $item->GENRE[0]->VALUE;
            }

            if (isset($item->DATE[0]->VALUE)) {
                $formatted->album_year = $item->DATE[0]->VALUE;
            }

            if (isset($item->TRACK_COUNT)) {
                $formatted->track_count = $item->TRACK_COUNT;
            }

            if (isset($item->TRACK)) {
                $formatted->tracks = collect($item->TRACK)->map(function ($item, $key) {
                    $formatted = (object) [];
                    if (isset($item->TRACK_NUM)) {
                        $formatted->track_number = $item->TRACK_NUM;
                    }

                    if (isset($item->GN_ID)) {
                        $formatted->gracenote_track_id = $item->GN_ID;
                    }

                    if (isset($item->ARTIST[0]->VALUE)) {
                        $formatted->artist = $item->ARTIST[0]->VALUE;
                    }

                    if (isset($item->TITLE[0]->VALUE)) {
                        $formatted->title = $item->TITLE[0]->VALUE;
                    }

                    return $formatted;
                })->toArray();
            }

            if (isset($item->ARTIST_ORIGIN[0])) {
                $formatted->artist_origin = $item->ARTIST_ORIGIN[0]->VALUE;
            }

            if (isset($item->ARTIST_TYPE[0])) {
                $formatted->artist_type = $item->ARTIST_TYPE[0]->VALUE;
            }

            if (isset($item->ARTIST_ERA[0])) {
                $formatted->artist_era = $item->ARTIST_ERA[0]->VALUE;
            }

            if (isset($item->URL)) {
                foreach ($item->URL as $image) {
                    $formatted->{strtolower($image->TYPE)} = (object) [
                        'size' => strtolower($image->SIZE),
                        'width' => $image->WIDTH,
                        'height' => $image->HEIGHT,
                        'url' => $image->VALUE,
                    ];
                }
            }

            return $formatted;
        });
    }

    private function formatSearchTrackTitle($raw_albums)
    {
        return collect($raw_albums)->map(function ($item, $key) {
            $formatted = (object) [];

            if (isset($item->GN_ID)) {
                $formatted->gracenote_album_id = $item->GN_ID;
            }

            if (isset($item->TITLE[0]->VALUE)) {
                $formatted->album_title = $item->TITLE[0]->VALUE;
            }

            if (isset($item->ARTIST[0]->VALUE)) {
                $formatted->album_artist = $item->ARTIST[0]->VALUE;
            }

            if (isset($item->GENRE[0]->VALUE)) {
                $formatted->album_genre = $item->GENRE[0]->VALUE;
            }

            if (isset($item->DATE[0]->VALUE)) {
                $formatted->album_year = $item->DATE[0]->VALUE;
            }

            if (isset($item->TRACK[0]->TRACK_NUM)) {
                $formatted->track_number = $item->TRACK[0]->TRACK_NUM;
            }

            if (isset($item->TRACK[0]->GN_ID)) {
                $formatted->gracenote_track_id = $item->TRACK[0]->GN_ID;
            }

            if (isset($item->TRACK[0]->TITLE)) {
                $formatted->track_title = $item->TRACK[0]->TITLE[0]->VALUE;
            }

            if (isset($item->ARTIST_ORIGIN[0])) {
                $formatted->artist_origin = $item->ARTIST_ORIGIN[0]->VALUE;
            }

            if (isset($item->ARTIST_TYPE[0])) {
                $formatted->artist_type = $item->ARTIST_TYPE[0]->VALUE;
            }

            if (isset($item->ARTIST_ERA[0])) {
                $formatted->artist_era = $item->ARTIST_ERA[0]->VALUE;
            }

            if (isset($item->URL)) {
                foreach ($item->URL as $image) {
                    $formatted->{strtolower($image->TYPE)} = (object) [
                        'size' => strtolower($image->SIZE),
                        'width' => $image->WIDTH,
                        'height' => $image->HEIGHT,
                        'url' => $image->VALUE,
                    ];
                }
            }

            return $formatted;
        });
    }

    /**
     * Sets the XML payload.
     */
    private function xmlPayload()
    {
        $lang = "<LANG>{strtoupper($this->lang)}</LANG>";
        $auth = "<AUTH><CLIENT>{$this->client_id}-{$this->client_tag}</CLIENT><USER>{$this->user_id}</USER></AUTH>";
        $search = '<TEXT TYPE="'.strtoupper($this->search_type).'">'.$this->search_terms.'</TEXT>';
        $query = '<QUERY CMD="'.$this->query_cmd.'">'.$this->search_mode.$search.$this->options.'</QUERY>';
        $payload = "<QUERIES>{$lang}{$auth}{$query}</QUERIES>";

        return $payload;
    }

    /**
     * This will try to set required information to be found in config/env file,
     * and throw an exception if nothing is found.
     */
    private function setParameters()
    {
        if (empty(config('laravel-gracenote.client_id'))) {
            throw RequiredConfigMissing::cantFindClientId();
        }

        if (empty(config('laravel-gracenote.client_tag'))) {
            throw RequiredConfigMissing::cantFindClientTag();
        }

        if (empty(config('laravel-gracenote.user_id'))) {
            throw RequiredConfigMissing::cantFindUserId();
        }

        $this->cache = empty(config('laravel-gracenote.cache')) ? 60 : config('laravel-gracenote.cache');
        $this->client_id = config('laravel-gracenote.client_id');
        $this->client_tag = config('laravel-gracenote.client_tag');
        $this->user_id = config('laravel-gracenote.user_id');
        $this->request_url = "https://c{$this->client_id}.web.cddbp.net/webapi/json/1.0/";
    }
}
