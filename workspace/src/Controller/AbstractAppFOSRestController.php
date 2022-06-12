<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractAppFOSRestController.
 */
abstract  class AbstractAppFOSRestController extends AbstractFOSRestController
{
    protected function getFormErrorsView(FormInterface $form, int $statusCode): View
    {
        return View::create([
            'error' => (string) $form->getErrors(true, true),
        ], $statusCode);
    }

    protected function getFormattedMessage(string $status, string $message): array
    {
        return [$status => $message];
    }
}
