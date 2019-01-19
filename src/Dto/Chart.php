<?php

namespace App\Dto;

/**
 * Data transfer object for Chart entity.
 */
final class Chart
{
    public $source;
    public $chart_title;
    public $x_path;
    public $y_path;
    public $x_name;
    public $y_name;
    public $predicted_count;

    public static $necessary_params = [
        'source',
        'chart_title',
        'x_path',
        'y_path',
        'x_name',
        'y_name',
        'predicted_count',
    ];

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getChartTitle()
    {
        return $this->chart_title;
    }

    /**
     * @return string
     */
    public function getXPath()
    {
        return $this->x_path;
    }

    /**
     * @return string
     */
    public function getYPath()
    {
        return $this->y_path;
    }

    /**
     * @return string
     */
    public function getXName()
    {
        return $this->x_name;
    }

    /**
     * @return string
     */
    public function getYName()
    {
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
