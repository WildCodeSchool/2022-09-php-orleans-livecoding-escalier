<?php

namespace App\Controller;

use App\Model\DishManager;

class MenuController extends AbstractController
{
    public function index()
    {
        $dishManager = new DishManager();
        $dishes = $dishManager->selectAll('title');

        return $this->twig->render('Menu/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }
}
