<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 15.01.2019
 * Time: 18:15
 */

namespace App\Service\Chart;

use App\Entity\Chart;
use App\Entity\Point;
use Flow\JSONPath\JSONPath;
use Phpml\Classification\KNearestNeighbors;


final class Data implements DataInterface {
	private $source;
	private $title;
	private $x_path;
	private $y_path;
	private $chart;

	public function getChart(): Chart {
		return $this->chart;
	}

	public function setChart( Chart $chart ): void {
		$this->chart = $chart;
	}

	public function setTitle( string $title ): void {
		$this->title = $title;
	}
	private $x_name;
	private $y_name;

	public function setSource( string $source ) {
		$this->source=$source;
	}

	public function setXPath( string $x_path ): void {
		$this->x_path = $x_path;
	}

	public function setYPath( string $y_path ): void {
		$this->y_path = $y_path;
	}

	public function setXName( string $x_name ): void {
		$this->x_name = $x_name;
	}

	public function setYName( string $y_name ): void {
		$this->y_name = $y_name;
	}

	/**
	 * @return Chart
	 * @throws \Flow\JSONPath\JSONPathException
	 * @todo new chart
	 * @todo throw non-declared prop exception
	 * Creates a chart with data from set outter source Json
	 *
	 */
	public function loadChart(): Chart {

		$necessary_params = [
			'source',
			'title',
			'x_path',
			'x_name',
			'y_path',
			'y_name'
		];

		foreach ($necessary_params as $necessary_param) {
			if ( empty( $this->$necessary_param ) ) {
				throw new \Exception( $necessary_param.' must be declared' );
			}
		}

		$data = file_get_contents($this->source);
		$json_data = json_decode($data);
		$json_path = new JSONPath($json_data);

		$x_data = $json_path->find($this->x_path);
		$y_data = $json_path->find($this->y_path);

		$chart = new Chart();

		foreach ($x_data as $i=>$x_row) {

			$point = new Point();
			$point
				->setXPosition($x_data[$i])
				->setYPosition($y_data[$i]);

			$chart->addPoint(
				$point
			);

		}

		$this->setChart($chart);

		dump($chart);

		return $chart;
	}

	/**
	 * @param Chart $chart
	 * @param PieChart $pieChart
	 * Convert Chaert to PieChart
	 * @todo to Dto
	 * @return PieChart
	 */
	public function importPieChart($pieChart)
	{

		$chart_array = [];

		$chart_array[] = [
			$this->x_name,
			$this->y_name
		];

		foreach ($this->getChart()->getPoints()->toArray() as $point){
			$chart_array[]=[
					0=>$point->getXPosition(),
					1=>$point->getYPosition()
				];
		}

		$pieChart->getOptions()->setTitle($this->title);
		$pieChart->getData()->setArrayToDataTable(
				$chart_array
		);

		$pieChart->getOptions()->setSeries([['axis' => $this->y_name]]);
		$pieChart->getOptions()->setVAxes(['y' => [$this->y_name => ['label' =>$this->y_name]]]);

		return $pieChart;
	}

	public function predictNextPoints(){

	}

}