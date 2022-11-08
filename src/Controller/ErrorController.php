<?php

namespace App\Controller;

class ErrorController extends AbstractController
{
    public function error(int $code)
    {
        return $this->twig->render('Error/error.html.twig', [
            'code' => $code
        ]);
    }
}
