<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 15.01.2019
 * Time: 18:15.
 */

namespace App\Service\Chart;

use App\Entity\Chart;
use App\Entity\Point;
use Flow\JSONPath\JSONPath;
use Phpml\Regression\LeastSquares;

final class Data implements DataInterface
{
    private $source;
    private $title;
    private $x_path;
    private $y_path;
    private $chart;
    private $predicted_points_count;

    /**
     * @return mixed
     */
    public function getPredictedPointsCount()
    {
        return $this->predicted_points_count;
    }

    /**
     * @param mixed $predicted_points_count
     */
    public function setPredictedPointsCount(int $predicted_points_count): void
    {
        $this->predicted_points_count = $predicted_points_count;
    }

    public function getChart(): Chart
    {
        return $this->chart;
    }

    public function setChart(Chart $chart): void
    {
        $this->chart = $chart;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    private $x_name;
    private $y_name;

    public function setSource(string $source)
    {
        $this->source = $source;
    }

    public function setXPath(string $x_path): void
    {
        $this->x_path = $x_path;
    }

    public function setYPath(string $y_path): void
    {
        $this->y_path = $y_path;
    }

    public function setXName(string $x_name): void
    {
        $this->x_name = $x_name;
    }

    public function setYName(string $y_name): void
    {
        $this->y_name = $y_name;
    }

    /**
     * @return Chart
     *
     * @throws \Flow\JSONPath\JSONPathException
     *
     * @todo new chart
     * @todo throw non-declared prop exception
     * Creates a chart with data from set outter source Json
     */
    public function loadChart(): Chart
    {
        $necessary_params = [
            'source',
            'title',
            'x_path',
            'x_name',
            'y_path',
            'y_name',
        ];

        foreach ($necessary_params as $necessary_param) {
            if (empty($this->$necessary_param)) {
                throw new \Exception($necessary_param.' must be declared');
            }
        }

        $data = file_get_contents($this->source);
        $json_data = json_decode($data);
        $json_path = new JSONPath($json_data);

        $x_data = $json_path->find($this->x_path);
        $y_data = $json_path->find($this->y_path);

        $chart = new Chart();

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

	    if($chart->getPoints()->count()<2){
		    throw new \Exception('There are no points in chart');
	    }

        //dump($chart);

        return $chart;
    }

    /**
     * @param Chart    $chart
     * @param PieChart $pieChart
     *                           Convert Chaert to PieChart
     *
     * @todo to Dto
     *
     * @return PieChart
     */
    public function importPieChart($pieChart)
    {
        $chart_array = [];

        $chart_array[] = [
            $this->x_name,
            $this->y_name,
            $this->y_name.' (prognosed)',
        ];

        foreach ($this->getChart()->getPoints()->filter(
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

        foreach ($this->getChart()->getPoints()->filter(
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

        $pieChart->getOptions()->setTitle($this->title);
        $pieChart->getData()->setArrayToDataTable(
                $chart_array
        );

        $pieChart->getOptions()->setSeries([['axis' => $this->y_name], ['axis' => $this->y_name.' (prognosed)']]);
        $pieChart->getOptions()->setVAxes(['y' => [$this->y_name => ['label' => $this->y_name], $this->y_name.' (prognosed)' => ['label' => $this->y_name]]]);

        return $pieChart;
    }

    /**
     * @todo: optimize (!) important
     */
    public function predictNextPoints()
    {
        $samples = [];
        $labels = [];

        $i = 0;

	    if($this->getChart()->getPoints()->count()<2){
		    throw new \Exception('There are no points in chart');
	    }

        foreach ($this->getChart()->getPoints()->filter(
            function ($entry) {
                return false === $entry->getPredicted();
            }
        )->toArray() as $point) {
            $samples[] = [$point->getXPosition()];
            $labels[] = $point->getYPosition();
            if (0 == $i) {
                $first_x_position = $point->getXPosition();
            }
            ++$i;
        }

        $last_x_position = $point->getXPosition();

        $point_step = abs($last_x_position - $first_x_position) / $i;

        $classifier = new LeastSquares();
        $classifier->train($samples, $labels);

        $samples_to_predict = [];

        for ($predicted_point_id = 1; $predicted_point_id < ($this->predicted_points_count + 1); ++$predicted_point_id) {
            $new_sample = $last_x_position + $point_step * $predicted_point_id;
            $samples_to_predict[] = [$new_sample];
        }

        $predicted_point_label = $classifier->predict($samples_to_predict);

        $chart = $this->getChart();
        foreach ($samples_to_predict as $sample_id => $sample) {
            $point = new Point();
            $point
                ->setXPosition($sample[0])
                ->setYPosition($predicted_point_label[$sample_id])
                ->setPredicted(true);

            $chart->addPoint(
                $point
            );
        }
    }
}
