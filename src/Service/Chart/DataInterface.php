<?php

namespace App\Service\Chart;

use App\Entity\Chart;
use App\Dto\Chart as ChartDto;

interface DataInterface
{
    public function loadChart(ChartDto $chart_dto): Chart;

    public function predictNextPoints();

    public function importPieChart();
}
