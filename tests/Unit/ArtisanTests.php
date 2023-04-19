<?php

namespace Tests\Unit;

use App\Models\Reports;
use App\Models\ResetPin;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ArtisanTests extends TestCase
{
    use DatabaseMigrations;
    public function setUp(): void
    {
        parent::setUp();
        $this->seed('DatabaseSeeder');
    }
    public function testReports(): void
    {
        $before = Reports::all()->count();
        Artisan::call('create:report');
        $this->assertTrue($before < Reports::all()->count());
    }
    public function testPinsClear(): void
    {
        ResetPin::create(
            [
                'email' => "test@test.test",
                'pin_code' => '111111',
                'expires_at' => Carbon::yesterday()
            ]
        );
        $before = ResetPin::all()->count();
        Artisan::call('pin:clear');
        $this->assertTrue($before > ResetPin::all()->count());
    }
}
