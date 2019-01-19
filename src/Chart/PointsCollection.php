<?php

namespace App\Chart;

use Doctrine\Common\Collections\ArrayCollection;

final class PointsCollection extends ArrayCollection
{
    public function sortPointsByX()
    {
        $iterator = $this->getIterator();

        $iterator->uasort(function ($a, $b) {
            return ($a->getXPosition() < $b->getXPosition()) ? -1 : 1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }
}
