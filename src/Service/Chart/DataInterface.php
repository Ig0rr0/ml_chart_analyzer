<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 15.01.2019
 * Time: 18:12
 */

namespace App\Service\Chart;

use App\Entity\Point;
use App\Entity\Chart;

interface DataInterface {

	public function setSource(string $source);
	public function setTitle(string $path);
	public function setXPath(string $path);
	public function setXName(string $name);
	public function setYPath(string $path);
	public function setYName(string $name);
	public function getChart(): Chart;
	public function setChart(Chart $chart);
	public function loadChart(): Chart;
	public function predictNextPoints();
	public function setPredictedPointsCount(int $count);

}