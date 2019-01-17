<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 09.01.2019
 * Time: 18:53.
 */

namespace App\Controller;

use App\Service\Chart\DataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use App\Service\Chart\Form\ImportData;

class Chart extends AbstractController
{
    /**
     * Puts data from request to service
     * Defines chart view configuration.
     *
     * @param DataInterface $service
     * @param Request       $request
     *
     * @return Response
     */
    public function index(DataInterface $service, Request $request): Response
    {
        $form = $this->createForm(ImportData::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('failure', 'Some data missing');
            $this->redirect('/');
        }


	    try {
	        $service->setSource($form->getViewData()['source']);
	        $service->setTitle($form->getViewData()['chart_title']);
	        $service->setXPath($form->getViewData()['x_path']);
	        $service->setXName($form->getViewData()['x_name']);
	        $service->setYPath($form->getViewData()['y_path']);
	        $service->setYName($form->getViewData()['y_name']);
	        $service->setPredictedPointsCount($form->getViewData()['predicted_count']);
	    } catch (\ErrorException $exception) {
		    return $this->render('chart/error.html.twig', [
			    'message' => $exception->getMessage(),
		    ]);
	    }

        try {
            $chart = $service->loadChart();
        } catch (\Exception $exception) {
            return $this->render('chart/error.html.twig', [
                'message' => $exception->getMessage(),
            ]);
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
            'piechart' => $pieChart,
        ]);
    }
}
