<?php

namespace App\Service\Chart;

use App\Dto\Chart as ChartDto;
use App\Entity\Chart;
use App\Model\Chart\ModifiedChartEntity;
use App\Entity\Point;
use App\Model\Chart\DataDraw;
use App\Model\Chart\DataLearn;
use Flow\JSONPath\JSONPath;
use Flow\JSONPath\JSONPathException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Client;

class DataLoader implements DataInterface
{
    private $chart;
    private $chart_dto;

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
        $this->chart_dto = $chart_dto;

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
        $this->setChart($chart);

        if ($chart->getPoints()->count() < 2) {
            throw new EmptyDataException('There are no points in chart');
        }

        return $chart;
    }

    public function predictNextPoints()
    {
        $this->setChart(
            DataLearn::predictNextPoints($this->getChart(), $this->chart_dto->getPredictedPointsCount())
        );
    }

    public function importPieChart()
    {
        return
            DataDraw::importPieChart($this->getChartDto(), $this->getChart());
    }

    /**
     * @return mixed
     */
    public function getChart(): Chart
    {
        return $this->chart;
    }

    /**
     * @return mixed
     */
    public function getChartDto()
    {
        return $this->chart_dto;
    }

    /**
     * @param mixed $chart_dto
     */
    public function setChartDto($chart_dto): void
    {
        $this->chart_dto = $chart_dto;
    }

    /**
     * @param mixed $chart
     */
    public function setChart(Chart $chart): void
    {
        $this->chart = $chart;
    }
}
