<?php

namespace App\Controller\Api;

use App\Dto\Chart as ChartDto;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Service\Chart\DataInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ImportData;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class ChartController extends AbstractFOSRestController
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
            $chart_dto = new ChartDto(
                $form->getViewData()['source'],
                $form->getViewData()['chart_title'],
                $form->getViewData()['x_path'],
                $form->getViewData()['x_name'],
                $form->getViewData()['y_path'],
                $form->getViewData()['y_name'],
                $form->getViewData()['predicted_count']
            );
        } catch (\Exception $exception) {
            $response = [
                'error' => $exception->getMessage(),
            ];

            return $this->view($response, Response::HTTP_BAD_REQUEST);
        }

        try {
            $service->loadChart(
                $chart_dto
            );
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
            return $object->getPoints();
        });
        //https://symfony.com/doc/current/reference/configuration/framework.html#circular-reference-handler - does not work: uncomment config\packages\test\framework.yaml:5-7 and try
        //deprecated method https://symfony.com/doc/current/components/serializer.html#handling-circular-references

        $serializer = new Serializer([$normalizer], [$encoder]);

        $response = $serializer->serialize($service->getChart(), 'json');

        return $this->view(json_decode($response, true), Response::HTTP_OK);
    }
}
