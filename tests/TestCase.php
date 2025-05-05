<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, CreatesUsers;
    
    protected function setUp(): void
    {
        parent::setUp();
    }
}
