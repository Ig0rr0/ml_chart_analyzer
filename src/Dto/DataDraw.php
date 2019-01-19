<?php

namespace App\Dto;

use App\Entity\Chart;
use App\Dto\Chart as ChartDto;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;

class DataDraw
{
    /**
     * Convert Chart to PieChart.
     *
     * @param ChartDto $chart_dto
     * @param Chart    $chart
     *
     * @return LineChart
     */
    public static function importPieChart(ChartDto $chart_dto, Chart $chart): LineChart
    {
        $pieChart = new LineChart();

        $chart_array = [];

        $chart_array[] = [
            $chart_dto->getXName(),
            $chart_dto->getYName(),
            $chart_dto->getYName().' (prognosed)',
        ];

        foreach ($chart->getPoints()->filter(
            function ($entry) {
                return false === $entry->getPredicted();
            }
        )->toArray() as $point) {
            $chart_array[] = [
                0 => $point->getXPosition(),
                1 => $point->getYPosition(),
                2 => 0,
            ];
        }

        foreach ($chart->getPoints()->filter(
            function ($entry) {
                return true === $entry->getPredicted();
            }
        )->toArray() as $point) {
            $chart_array[] = [
                0 => $point->getXPosition(),
                1 => 0,
                2 => $point->getYPosition(),
            ];
        }

        $pieChart->getOptions()->setTitle($chart_dto->getChartTitle());
        $pieChart->getData()->setArrayToDataTable(
            $chart_array
        );

        $pieChart->getOptions()->setSeries([['axis' => $chart_dto->getYName()], ['axis' => $chart_dto->getYName().' (prognosed)']]);
        $pieChart->getOptions()->setVAxes(['y' => [$chart_dto->getYName() => ['label' => $chart_dto->getYName()], $chart_dto->getYName().' (prognosed)' => ['label' => $chart_dto->getYName()]]]);

        return $pieChart;
    }
}
