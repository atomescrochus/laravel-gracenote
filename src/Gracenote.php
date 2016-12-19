<?php
namespace Atomescrochus\Gracenote;

use Atomescrochus\Gracenote\Exceptions\RequiredConfigMissing;
use Illuminate\Support\Facades\Cache;

class Gracenote
{
    private $client_id;
    private $client_tag;
    private $user_id;
    private $request_url;

    public $query_cmd;
    public $lang;
    public $search_type;
    public $search_terms;
    public $cache;

    public function __construct()
    {
        $this->setParameters();

        $this->lang = "eng";
        $this->search_terms = "";
        $this->query_cmd = "album_search"; // curently only possible option.
        $this->search_type = "TRACK_TITLE";
    }

    /**
     * Sets the time in minutes to cache the search results
     * @param  integer $cache A number of minutes
     */
    public function cache(int $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Set the "prefered natural language of metadata"
     * @param  string $type One of the search type as defined by Gracenote API docs.
     */
    public function searchType($type)
    {
        $this->search_type = $type;
        return $this;
    }

    /**
     * Set the "prefered natural language of metadata"
     * @param  string $lang A three-character language code as defined by ISO 639-2
     */
    public function lang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * Set the query to send to Gracenote
     * @param  string $query The search query
     */
    public function query($query)
    {
        $this->search_terms = $query;
        return $this;
    }

    /**
     * Sends the search
     * @return collection A collection of results
     */
    public function search()
    {
        $results = Cache::remember($this->search_terms, $this->cache, function () {
            return $this->searchGracenote();
        });

        return $results;
    }

    /**
     * Send a request for search to Gracenote WebAPI
     * @return collection A collection of results
     */
    private function searchGracenote()
    {
        $response = \Httpful\Request::post($this->request_url)
        ->body($this->xmlPayload())
        ->sendsXml()
        ->send();
        
        $results = collect($response->body->RESPONSE[0]->ALBUM);

        return $results;
    }

    /**
     * Sets the XML payload
     */
    private function xmlPayload()
    {
        $lang = "<LANG>{strtoupper($this->lang)}</LANG>";
        $auth= "<AUTH><CLIENT>{$this->client_id}-{$this->client_tag}</CLIENT><USER>{$this->user_id}</USER></AUTH>";
        $search = '<TEXT TYPE="'.strtoupper($this->search_type).'">'.$this->search_terms.'</TEXT>';
        $query = '<QUERY CMD="'.$this->query_cmd.'">'.$search.'</QUERY>';
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
