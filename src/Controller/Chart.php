<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 09.01.2019
 * Time: 18:53
 */

namespace App\Controller;

use App\Service\Chart\DataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;


class Chart extends AbstractController {

	public function index(DataInterface $service) : Response
	{

		// 1. set source
		$source = 'https://eth.nanopool.org/api/v1/price_history/0/768';
		$service->setSource($source);
		$service->setTitle('Ethereum price prognose');
		$service->setXPath('$.data.*.time');
		$service->setXName('Time');
		$service->setYPath('$.data.*.price');
		$service->setYName('Name');

		try{
			$chart = $service->loadChart();
		} catch (\Exception $exception){
			dump($exception->getMessage());
			die();
			//todo return form to complete
		}

		$service->predictNextPoints();

		$pieChart = new LineChart();

		$pieChart =
			$service->importPieChart($pieChart);

		$pieChart->getOptions()->setHeight(500);
		$pieChart->getOptions()->setWidth(900);
		$pieChart->getOptions()->getTitleTextStyle()->setBold(true);
		$pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
		$pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
		$pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
		$pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

		return $this->render('chart/view.html.twig', [
			'piechart' => $pieChart
		]);
	}

}