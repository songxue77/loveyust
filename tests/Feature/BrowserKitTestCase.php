<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;
use Tests\CreatesApplication;
use Tests\CreatesUsers;
use Tests\HttpAssertions;

abstract class BrowserKitTestCase extends BaseTestCase
{
    use CreatesApplication, CreatesUsers, HttpAssertions;

    public $baseUrl = 'http://localhost';

    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[WithFaker::class])) {
            $this->setUpFaker();
        }

        return $uses;
    }

    protected function dispatch($job)
    {
        return $job->handle();
    }
}
