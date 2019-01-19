<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Dto\Chart;

/**
 * Class HomeTest
 * Tests main page accessed.
 */
class HomeControllerTest extends WebTestCase
{
    public function testFormPresented()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        self::assertEquals(200, $client->getResponse()->getStatusCode(), 'Home Page is not accessible');
        $to_test = Chart::$necessary_params;
        foreach ($to_test as $test_var) {
            self::assertEquals(
                1,
                $crawler->filter('#import_data_'.$test_var)->count(),
                'Contact '.$test_var.' information have to be count 1'
            );
        }
    }
}
