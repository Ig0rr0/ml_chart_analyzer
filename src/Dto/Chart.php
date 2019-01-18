<?php

namespace App\Dto;

use App\Exception\InputParamMissException;

/**
 * Data transfer object for Chart entity.
 */
final class Chart
{
	private $source;
	private $chart_title;
	private $x_path;
	private $y_path;
	private $x_name;
	private $y_name;
	private $predicted_count;

	public static $necessary_params = [
		'source',
		'chart_title',
		'x_path',
		'x_name',
		'y_path',
		'y_name',
		'predicted_count'
	];

	public function __construct(
		string $source,
		string $chart_title,
		string $x_path,
		string $y_path,
		string $x_name,
		string $y_name,
		int $predicted_count
	) {
		$this->source = $source;
		$this->chart_title = $chart_title;
		$this->x_path = $x_path;
		$this->y_path = $y_path;
		$this->x_name = $x_name;
		$this->y_name = $y_name;
		$this->predicted_count = $predicted_count;

		foreach (self::$necessary_params as $necessary_param) {
			if (empty($this->$necessary_param)) {
				throw new InputParamMissException($necessary_param.' must be declared');
			}
		}
	}

	/**
	 * @return string
	 */
	public function getSource(): string {
		return $this->source;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return $this->chart_title;
	}

	/**
	 * @return string
	 */
	public function getXPath(): string {
		return $this->x_path;
	}

	/**
	 * @return string
	 */
	public function getYPath(): string {
		return $this->y_path;
	}

	/**
	 * @return string
	 */
	public function getXName(): string {
		return $this->x_name;
	}

	/**
	 * @return string
	 */
	public function getYName(): string {
		return $this->y_name;
	}

	/**
	 * @return mixed
	 */
	public function getPredictedPointsCount()
	{
		return $this->predicted_count;
	}
}
