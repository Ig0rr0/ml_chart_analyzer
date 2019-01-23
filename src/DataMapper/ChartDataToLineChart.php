<?php

namespace App\DataMapper;

use App\Entity\Chart;
use App\Dto\Chart as ChartDto;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;

class ChartDataToLineChart
{
	private $pie_chart;
    /**
     * Convert Chart to PieChart.
     *
     * @param ChartDto $chart_dto
     * @param Chart    $chart
     *
     * @return LineChart
     */
    public function importPieChart(ChartDto $chart_dto, Chart $chart): LineChart
    {
        $this->pie_chart = new LineChart();

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

        $this->pie_chart->getOptions()->setTitle($chart_dto->getChartTitle());
        $this->pie_chart->getData()->setArrayToDataTable(
            $chart_array
        );

        $this->pie_chart->getOptions()->setSeries([['axis' => $chart_dto->getYName()], ['axis' => $chart_dto->getYName().' (prognosed)']]);
        $this->pie_chart->getOptions()->setVAxes(['y' => [$chart_dto->getYName() => ['label' => $chart_dto->getYName()], $chart_dto->getYName().' (prognosed)' => ['label' => $chart_dto->getYName()]]]);

        return $this->pie_chart;
    }

    public function setDefaultView(): LineChart
    {
		$this->pie_chart->getOptions()->setHeight(500);
        $this->pie_chart->getOptions()->setWidth(900);
        $this->pie_chart->getOptions()->getTitleTextStyle()->setBold(true);
        $this->pie_chart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $this->pie_chart->getOptions()->getTitleTextStyle()->setItalic(true);
        $this->pie_chart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $this->pie_chart->getOptions()->getTitleTextStyle()->setFontSize(20);

	    return $this->pie_chart;
    }

	/**
	 * @return LineChart
	 */
	public function getPieChart(): LineChart
	{
		return $this->pie_chart;
	}
}
