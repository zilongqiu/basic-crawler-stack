<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewController extends AbstractAppFOSRestController
{

    /**
     * @Rest\Get("/data")
     */
    public function getData(Request $request, $version): View
    {
        return View::create([], Response::HTTP_OK);
    }
}
