<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 17.01.2019
 * Time: 12:32.
 */

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Service\Chart\DataInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Chart\Form\ImportData;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class Chart extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/chart/get_points")
     */
    public function postGetPoints(DataInterface $service, Request $request)
    {
        $form = $this->createForm(ImportData::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $response = [
                'error' => 'request is invalid',
            ];

            return $this->view($response, Response::HTTP_BAD_REQUEST);
        }

	    try {
		    $service->setSource( $form->getViewData()['source'] );
		    $service->setTitle( $form->getViewData()['chart_title'] );
		    $service->setXPath( $form->getViewData()['x_path'] );
		    $service->setXName( $form->getViewData()['x_name'] );
		    $service->setYPath( $form->getViewData()['y_path'] );
		    $service->setYName( $form->getViewData()['y_name'] );
		    $service->setPredictedPointsCount( $form->getViewData()['predicted_count'] );
	    } catch (\ErrorException $exception) {
		    $response = [
			    'error' => $exception->getMessage(),
		    ];

		    return $this->view($response, Response::HTTP_BAD_REQUEST);
	    }

        try {
            $chart = $service->loadChart();
        } catch (\Exception $exception) {
            $response = [
                'error' => $exception->getMessage(),
            ];

            return $this->view($response, Response::HTTP_BAD_REQUEST);
        }

        $service->predictNextPoints();

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function ($object, string $format = null, array $context = []) {
            //wtf! this method is described in actual docs: https://symfony.com/doc/current/components/serializer.html why deprecated method is in actual docs and no alternative presented?
            return $object->getPoints();
        });

        $serializer = new Serializer([$normalizer], [$encoder]);

        $response = $serializer->serialize($service->getChart(), 'json');

        return $this->view(json_decode($response, true), Response::HTTP_OK);
    }
}
