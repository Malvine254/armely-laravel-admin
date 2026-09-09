<?php

namespace Tests\Feature;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServicesController;
use Tests\TestCase;

class ServiceTitleTest extends TestCase
{
    public function test_fractional_dba_service_title_keeps_dba_capitalized(): void
    {
        $response = app(ServicesController::class)->show('fractional-dba');

        $this->assertSame('Fractional DBA', $response->getData()['title']);
    }

    public function test_home_service_details_title_keeps_fractional_dba_capitalized(): void
    {
        $response = app(HomeController::class)->serviceDetails('fractional-dba');

        $this->assertSame('Fractional DBA', $response->getData()['service']->title);
    }
}
