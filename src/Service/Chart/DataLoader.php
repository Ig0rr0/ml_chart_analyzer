<?php

namespace App\Service\Chart;

use App\Dto\Chart as ChartDto;
use App\Entity\Chart;
use App\Model\Chart\ModifiedChartEntity;
use App\Entity\Point;
use Flow\JSONPath\JSONPath;
use Flow\JSONPath\JSONPathException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Client;

class DataLoader implements DataInterface
{
    /**
     * Creates a chart with data from set outter source Json.
     *
     * @param ChartDto $chart_dto
     *
     * @return Chart
     *
     * @throws \Flow\JSONPath\JSONPathException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \App\Exception\InputParamMissException
     * @throws \App\Exception\EmptyDataException
     */
    public function loadChart(ChartDto $chart_dto): Chart
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $chart_dto->getSource());

            $data = (string) $response->getBody();
        } catch (ConnectException $exception) {
            throw $exception;
        }

        $json_data = json_decode($data);
        $json_path = new JSONPath($json_data);

        try {
            $x_data = $json_path->find($chart_dto->getXPath());
            $y_data = $json_path->find($chart_dto->getYPath());
        } catch (JSONPathException $exception) {
            throw $exception;
        }

        $chart = new ModifiedChartEntity();

        foreach ($x_data as $i => $x_row) {
            $point = new Point();
            $point
                ->setXPosition($x_data[$i])
                ->setYPosition($y_data[$i]);

            $chart->addPoint(
                $point
            );
        }

        $chart->sortPointsByX();

        if ($chart->getPoints()->count() < 2) {
            throw new EmptyDataException('There are no points in chart');
        }

        return $chart;
    }

    public function predictNextPoints(ChartDto $chart_dto, Chart $chart)
    {
        return
            DataLearn::predictNextPoints($chart, $chart_dto->getPredictedPointsCount());
    }
}
