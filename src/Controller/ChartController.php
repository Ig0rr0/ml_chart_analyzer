<?php

namespace App\Controller;

use App\DataMapper\ChartDataToLineChart;
use App\Service\Chart\DataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ImportData;
use GuzzleHttp\Exception\ConnectException;
use App\Exception\EmptyDataException;
use App\Exception\InputParamMissException;

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
            $chart_dto = $form->getData();
        } catch (InputParamMissException $exception) {
            return $this->render('chart/error.html.twig', [
                'message' => $exception->getMessage(),
            ]);
        } catch (\Exception $exception) {
            return $this->render('chart/error.html.twig', [
                'message' => 'Unknown Error: '.$exception->getMessage(),
            ]);
        }

        try {
            $chart = $service->loadChart(
                $chart_dto
            );
        } catch (ConnectException $exception) {
            return $this->render('chart/error.html.twig', [
                'message' => $exception->getMessage(),
            ]);
        } catch (EmptyDataException $exception) {
            return $this->render('chart/error.html.twig', [
                'message' => $exception->getMessage(),
            ]);
        } catch (\Exception $exception) {
            return $this->render('chart/error.html.twig', [
                'message' => 'Unknown Error: '.$exception->getMessage(),
            ]);
        }

        $chart = $service->predictNextPoints($chart_dto, $chart);

        $pieChart = new ChartDataToLineChart();
	    $pieChart->importPieChart($chart_dto, $chart);
	    $pieChart->setDefaultView();

        return $this->render('chart/view.html.twig', [
            'piechart' => $pieChart->getPieChart(),
        ]);
    }
}
