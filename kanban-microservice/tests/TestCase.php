<?php

namespace Tests;

use Illuminate\Support\Facades\Mail;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase as LumenTestCase;

/**
 * Class TestCase
 *
 * @package Tests
 */
abstract class TestCase extends LumenTestCase
{
    use DatabaseTransactions;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }
}
