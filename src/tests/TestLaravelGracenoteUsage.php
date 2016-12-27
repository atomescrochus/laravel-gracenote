<?php

class TestLaravelGracenoteUsage extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Test if default required default attributes values are set
     *
     * @return void
     */
    public function test_are_default_attributes_set()
    {
        $gracenote = new \Atomescrochus\Gracenote\Gracenote();

        // lang exists and is set to the default value
        $this->assertObjectHasAttribute('lang', $gracenote);
        $this->assertEquals('eng', $gracenote->lang);

        // query cmd exists, and is set to the default value
        $this->assertObjectHasAttribute('query_cmd', $gracenote);
        $this->assertEquals('album_search', $gracenote->query_cmd);

        // search type exists, and is set to the default value
        $this->assertObjectHasAttribute('search_type', $gracenote);
        $this->assertEquals('TRACK_TITLE', $gracenote->search_type);

        // search terms exists, but is null (no search value by default)
        $this->assertObjectHasAttribute('search_terms', $gracenote);
        $this->assertNull($gracenote->search_terms);

        // cache exists and is a number
        $this->assertObjectHasAttribute('cache', $gracenote);
        $this->assertInternalType('int', $gracenote->cache);
    }

    /**
     * Test if we can set a new cache time
     *
     * @return void
     */
    public function test_can_set_cache()
    {
        $gracenote = new \Atomescrochus\Gracenote\Gracenote();
        $gracenote->cache(120);

        $this->assertEquals(120, $gracenote->cache);
    }

    /**
     * Test if we can set a search type
     *
     * @return void
     */
    public function test_set_search_type()
    {
        $gracenote = new \Atomescrochus\Gracenote\Gracenote();
        $gracenote->searchType('album_title');

        $this->assertEquals('album_title', $gracenote->search_type);
    }

    /**
     * Test if we throw an error on wrong sarch type
     *
     * @return void
     */
    public function test_failed_search_type()
    {
        $this->expectException(\Atomescrochus\Gracenote\Exceptions\UsageErrors::class);
        $this->expectExceptionMessage("This search type is invalid.");

        $gracenote = new \Atomescrochus\Gracenote\Gracenote();
        $gracenote->searchType('this_is_wrong');
    }

    /**
     * Test if we can set a new language
     *
     * @return void
     */
    public function test_can_set_other_language()
    {
        $gracenote = new \Atomescrochus\Gracenote\Gracenote();
        $gracenote->lang('fra');

        $this->assertEquals('fra', $gracenote->lang);
    }

    /**
     * Test if we can set a search query
     *
     * @return void
     */
    public function test_can_set_search_query()
    {
        $gracenote = new \Atomescrochus\Gracenote\Gracenote();
        $gracenote->query('lady gaga');

        $this->assertEquals('lady gaga', $gracenote->search_terms);
    }

    /**
     * Test if we can set send a search
     * This assumes that the Gracenote API works.
     *
     * @return void
     */
    public function test_search_gracenote_api()
    {
        $gracenote = new \Atomescrochus\Gracenote\Gracenote();
        $gracenote->searchType('track_title');
        $gracenote->query('poker face');
        $results = $gracenote->search();

        $this->assertInternalType('object', $results);
    }

    /**
     * Test if we can set send a search
     * This assumes that the Gracenote API works.
     *
     * @return void
     */
    public function test_are_we_setting_the_cache()
    {
        $gracenote = new \Atomescrochus\Gracenote\Gracenote();
        $gracenote->searchType('track_title');
        $gracenote->query('poker face');
        $cache_key = "track_title-poker face";
        $search_and_cache = $gracenote->search();

        $this->assertTrue(Cache::has($cache_key));

        // remove it from cache, just in case it would interfer with future test
        Cache::forget($cache_key);
        $this->assertFalse(Cache::has($cache_key));
    }
}
