<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ChartTest
 * If chartpoints geterates - components & modules inside script works fine.
 */
class ChartControllerTest extends WebTestCase
{
    public function testApiReturns()
    {
        $client = static::createClient();

        $to_test = ['source', 'chart_title', 'x_name', 'y_name', 'x_path', 'y_path', 'predicted_count'];
        $to_test_data = ['https://eth.nanopool.org/api/v1/price_history/0/768', 'Ethereum price ', 'Time period', 'Price value', '$.data.*.time', '$.data.*.price', '20'];
        $parameters = [];
        foreach ($to_test as $test_var_id => $test_var) {
            $parameters['import_data'][$test_var] = $to_test_data[$test_var_id];
        }

        $crawler = $client->request('POST', '/api/chart/get_points', $parameters);
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode(), 'API Page is not accessible');
        self::assertNotFalse($contents = json_decode($response->getContent(), true), 'failed json decode API');
        self::assertGreaterThan(10, $contents['points']);
    }
}
