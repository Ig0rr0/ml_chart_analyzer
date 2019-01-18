<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Chart\Form\ImportData;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        $form = $this->createForm(ImportData::class, null, [
            'action' => $this->generateUrl('chart'),
        ]);

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
