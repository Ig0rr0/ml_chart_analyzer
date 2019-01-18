<?php
namespace App\Model\Chart;

use App\Entity\Chart;
use Doctrine\Common\Collections\ArrayCollection;

class ModifiedChartEntity extends Chart{

	private $points;

	public function sortPointsByX()
	{
		$iterator = $this->getPoints()->getIterator();
		$iterator->uasort(function ($a, $b) {
			return ($a->getXPosition() < $b->getXPosition()) ? -1 : 1;
		});
		$this->points = new ArrayCollection(iterator_to_array($iterator));
	}
}