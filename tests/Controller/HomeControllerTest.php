<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 17.01.2019
 * Time: 14:42.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        $to_test = ['source', 'chart_title', 'x_name', 'y_name', 'x_path', 'y_path', 'predicted_count'];
        foreach ($to_test as $test_var) {
            self::assertEquals(
                1,
                $crawler->filter('#import_data_'.$test_var)->count(),
                'Contact '.$test_var.' information have to be count 1'
            );
        }
    }
}
