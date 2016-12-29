<?php

namespace Atomescrochus\Gracenote\Commands;

use Illuminate\Console\Command;
use Atomescrochus\Gracenote\Exceptions\RequiredConfigMissing;

class GetGracenoteUserId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gracenote:user-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch your Gracenote user id.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("This command will fail if you didn't fill your .env file with the 'GRACENOTE_CLIENT_ID' and the 'GRACENOTE_CLIENT_TAG.");

        if ($this->confirm('Check that those values are set before continuing.')) {
            $this->getGracenoteUserId();
        }
    }

    private function getGracenoteUserId()
    {
        if (empty(config('laravel-gracenote.client_id'))) {
            throw RequiredConfigMissing::cantFindClientId();
        }

        if (empty(config('laravel-gracenote.client_tag'))) {
            throw RequiredConfigMissing::cantFindClientTag();
        }

        $client_id = config('laravel-gracenote.client_id');
        $client_tag = config('laravel-gracenote.client_tag');
        $request_url = "https://c{$client_id}.web.cddbp.net/webapi/json/1.0/";
        $payload = '<QUERIES><QUERY CMD="REGISTER"><CLIENT>'.$client_id.'-'.$client_tag.'</CLIENT></QUERY></QUERIES>';

        $response = \Httpful\Request::post($request_url)
        ->body($payload)
        ->sendsXml()
        ->send();
        if (! $response) {
            $this->error('Something went wrong.');
        }

        $this->info('Your Gracenote user id is:');
        $this->comment($response->body->RESPONSE[0]->USER[0]->VALUE);
        $this->info("Please add it to your .env file as 'GRACENOTE_USER_ID' then you are all set!");
    }
}
