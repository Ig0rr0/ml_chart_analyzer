<?php
namespace App\Model\Chart;

use Phpml\Regression\LeastSquares;
use App\Exception\EmptyDataException;
use App\Entity\Chart;
use App\Entity\Point;

class DataLearn {

	/**
	 * Logic of how prediction works
	 * @param Chart $chart
	 * @param $predicted_points_count
	 *
	 * @return Chart
	 */
	public static function predictNextPoints(Chart $chart, $predicted_points_count)
	{
		$samples = [];
		$labels = [];

		$i = 0;

		if ($chart->getPoints()->count() < 2) {
			throw new EmptyDataException('There are no points in chart');
		}

		foreach ($chart->getPoints()->filter(
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

		for ($predicted_point_id = 1; $predicted_point_id < ($predicted_points_count + 1); ++$predicted_point_id) {
			$new_sample = $last_x_position + $point_step * $predicted_point_id;
			$samples_to_predict[] = [$new_sample];
		}

		$predicted_point_label = $classifier->predict($samples_to_predict);

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
		return $chart;
	}
}