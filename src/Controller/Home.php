<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 16.01.2019
 * Time: 23:58
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Home\HomePageServiceInterface;
use App\Service\Chart\Form\ImportData;

class Home extends AbstractController {

	public function index(HomePageServiceInterface $service): Response
	{
		$form = $this->createForm(ImportData::class, null, [
			'action' => $this->generateUrl('chart')
		]);

		return $this->render('home/index.html.twig', [
			'form' => $form->createView(),
		]);
	}
}