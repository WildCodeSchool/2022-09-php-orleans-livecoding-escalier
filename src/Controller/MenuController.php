<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\DishManager;

class MenuController extends AbstractController
{
    public function index()
    {
        $dishManager = new DishManager();
        $categoryManager = new CategoryManager();
        $dishes = $dishManager->selectAllWithCategory();

        $categories = $categoryManager->selectAll();
        $categoryTitles = array_column($categories, 'title');

        return $this->twig->render('Menu/index.html.twig', [
            'dishes' => $dishes,
            'categoryTitles' => $categoryTitles,
        ]);
    }
}
