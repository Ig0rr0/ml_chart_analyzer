<?php
namespace App\Serializer;

class MyCustomCircularReferenceHandler{

	/**
	 * @param $object
	 *
	 * @return mixed
	 */
	public function __invoke($object) {
		return $object->getPoints();
	}
}