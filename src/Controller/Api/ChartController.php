<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Service\Chart\DataInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ImportData;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use GuzzleHttp\Exception\ConnectException;
use App\Exception\EmptyDataException;
use App\Exception\InputParamMissException;

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
	        $chart_dto = $form->getData();
        } catch (InputParamMissException $exception) {
            $response = [
                'error' => $exception->getMessage(),
            ];

            return $this->view($response, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
	        $response = [
		        'error' => "Unknown error: ".$exception->getMessage(),
	        ];

	        return $this->view($response, Response::HTTP_BAD_REQUEST);
        }

        try {
            $chart = $service->loadChart(
                $chart_dto
            );
        } catch (ConnectException $exception) {
		        $response = [
			        'error' => $exception->getMessage(),
		        ];

	        return $this->view($response, Response::HTTP_BAD_REQUEST);
        } catch (EmptyDataException $exception) {
	            $response = [
			        'error' => $exception->getMessage(),
		        ];

	        return $this->view($response, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
	            $response = [
			        'error' => "Unknown error: ".$exception->getMessage(),
		        ];

	        return $this->view($response, Response::HTTP_BAD_REQUEST);
        }

        $service->predictNextPoints($chart_dto, $chart);

	    $response=[];

	    foreach ($chart->getPoints() as $point){
	    	$response[]=
			    [
				    'x'	=>$point->getXPosition(),
				    'y'	=>$point->getYPosition(),
				    'predicted'	=>$point->getPredicted()
			    ];
        }

        return $this->view($response, Response::HTTP_OK);
    }
}
