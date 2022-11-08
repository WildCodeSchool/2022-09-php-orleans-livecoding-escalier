<?php

namespace App\Controller;

use App\Model\DishManager;

class AdminDishController extends AbstractController
{
    public const INPUT_MAX_LENGTH = 255;

    public function index(): string
    {
        $this->isAuthenticated();

        $dishManager = new DishManager();
        $dishes = $dishManager->selectAll('title');

        return $this->twig->render('Admin/Dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    public function add(): string
    {
        $errors = $dish = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dish = array_map('trim', $_POST);
            $errors = $this->validate($dish);

            if (empty($errors)) {
                $dishManager = new DishManager();
                $dishManager->insert($dish);

                header('Location: /admin/menu');
                return '';
            }
        }

        return $this->twig->render('Admin/Dish/add.html.twig', [
            'errors' => $errors,
            'dish' => $dish,
        ]);
    }

    public function edit(int $id): string
    {
        $errors = [];
        $dishManager = new DishManager();
        $dish = $dishManager->selectOneById($id);

        if ($dish && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $dish = array_map('trim', $_POST);
            $dish['id'] = $id;
            $errors = $this->validate($dish);

            if (empty($errors)) {
                $dishManager = new DishManager();
                $dishManager->update($dish);

                header('Location: /admin/menu');
                return '';
            }
        }

        return $this->twig->render('Admin/Dish/edit.html.twig', [
            'errors' => $errors,
            'dish' => $dish,
        ]);
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) trim($_POST['id']);

            $dishManager = new DishManager();
            $dishManager->delete($id);

            header('Location: /admin/menu');
        }
    }


    private function validate(array $dish): array
    {
        $errors = [];

        if (empty($dish['title'])) {
            $errors[] = 'Le nom du plat est obligatoire';
        }

        if (empty($dish['price'])) {
            $errors[] = 'Le prix du plat est obligatoire';
        }

        if (!is_numeric($dish['price']) || $dish['price'] < 0) {
            $errors[] = 'Le prix doit être un nombre positif';
        }

        if (strlen($dish['title']) > self::INPUT_MAX_LENGTH) {
            $errors[] = 'Le titre doit faire moins de ' . self::INPUT_MAX_LENGTH . ' caractères';
        }

        if (strlen($dish['description']) > self::INPUT_MAX_LENGTH) {
            $errors[] = 'La description doit faire moins de ' . self::INPUT_MAX_LENGTH . ' caractères';
        }

        return $errors;
    }
}
