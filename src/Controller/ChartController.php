<?php

namespace App\Controller;

use App\Dto\Chart as ChartDto;
use App\Dto\DataDraw;
use App\Service\Chart\DataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ImportData;

use GuzzleHttp\Exception\ConnectException;
use App\Exception\InputParamMissException;
use App\Exception\EmptyDataException;


class ChartController extends AbstractController
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
            return $this->render('chart/error.html.twig', [
                'message' => 'Some data missing',
            ]);
        }

        try {
            $chart_dto = new ChartDto(
                $form->getViewData()['source'],
                $form->getViewData()['chart_title'],
                $form->getViewData()['x_path'],
                $form->getViewData()['y_path'],
                $form->getViewData()['x_name'],
                $form->getViewData()['y_name'],
                $form->getViewData()['predicted_count']
            );
        } catch (InputParamMissException $exception) {
            return $this->render('chart/error.html.twig', [
                'message' => $exception->getMessage(),
            ]);
        }

        try {
            $chart = $service->loadChart(
                $chart_dto
            );
        } catch (Exception $exception) {
	        if ($exception instanceof ConnectException OR $exception instanceof EmptyDataException) {
		        return $this->render('chart/error.html.twig', [
			        'message' => $exception->getMessage(),
		        ]);
	        } else {
		        return $this->render('chart/error.html.twig', [
			        'message' => 'Unknown Error: ' . $exception->getMessage()
		        ]);
	        }
        }

	    $chart = $service->predictNextPoints($chart_dto,$chart);

        $pieChart =
            DataDraw::importPieChart($chart_dto,$chart);

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
