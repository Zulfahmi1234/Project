<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\NominatimService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\ConnectionException;

class NominatimServiceTest extends TestCase
{
    protected NominatimService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NominatimService();
        Cache::flush();
    }

    public function test_get_boundary_success()
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'place_id' => 12345,
                    'osm_id' => 67890,
                    'display_name' => 'Jakarta, Indonesia',
                    'boundingbox' => ['-6.3', '-6.1', '106.7', '106.9'],
                    'geojson' => [
                        'type' => 'Polygon',
                        'coordinates' => [[[106.7, -6.3], [106.9, -6.3], [106.9, -6.1], [106.7, -6.1], [106.7, -6.3]]]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->getBoundary('Jakarta');

        $this->assertNotNull($result);
        $this->assertEquals('Jakarta', $result['query']);
        $this->assertEquals('Feature', $result['boundary']['type']);
        $this->assertEquals('Jakarta, Indonesia', $result['boundary']['properties']['display_name']);
        $this->assertEquals('Polygon', $result['boundary']['geometry']['type']);
        $this->assertFalse($result['cached']);

        // Second call should hit cache
        $cachedResult = $this->service->getBoundary('Jakarta');
        $this->assertTrue($cachedResult['cached']);
    }

    public function test_get_boundary_returns_null_on_empty_results()
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 200)
        ]);

        $result = $this->service->getBoundary('UnknownPlace123');

        $this->assertNull($result);
    }

    public function test_get_boundary_throws_exception_on_failure()
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 500)
        ]);

        $this->expectException(ConnectionException::class);
        $this->service->getBoundary('Jakarta');
    }
}
