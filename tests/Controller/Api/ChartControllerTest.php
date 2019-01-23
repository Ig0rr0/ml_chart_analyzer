<?php

namespace App\Tests\Controller;

use App\Dto\Chart as ChartDto;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ChartTest
 * If chartpoints geterates - components & modules inside script works fine.
 */
class ChartControllerTest extends WebTestCase
{
	/**
	 * @dataProvider provideData
	 * @param $to_test_data
	 */
    public function testApiReturns($to_test_data)
    {
        $client = static::createClient();

        $to_test = ChartDto::$necessary_params;
        $parameters = [];
        foreach ($to_test as $test_var_id => $test_var) {
            $parameters['import_data'][$test_var] = $to_test_data[$test_var_id];
        }

        $client->request('POST', '/api/chart/get_points', $parameters);
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode(), 'API Page is not accessible');
        self::assertNotFalse($contents = \json_decode($response->getContent(), true), 'failed json decode API');
        self::assertGreaterThan(2, $contents);
    }

	public function provideData()
	{
		return [[
			[
				'https://eth.nanopool.org/api/v1/price_history/0/768',
				'Ethereum price ',
				'$.data.*.time', '$.data.*.price',
				'Time period', 'Price value',
				'20']
		]];
	}
}
