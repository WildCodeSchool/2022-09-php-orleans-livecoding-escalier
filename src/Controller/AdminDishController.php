<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\DishManager;

class AdminDishController extends AbstractController
{
    public const INPUT_MAX_LENGTH = 255;
    public const UPLOAD_DIR = __DIR__ . '/../../public/uploads/';

    public function index(): string
    {
        $this->isAuthenticated();

        $dishManager = new DishManager();
        $dishes = $dishManager->selectAllWithCategory();

        return $this->twig->render('Admin/Dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    public function add(): string
    {
        $this->isAuthenticated();

        $errors = $dish = [];

        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('title');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dish = array_map('trim', $_POST);
            $image = $_FILES['image'];

            $dishErrors = $this->validate($dish, $categories);
            $uploadErrors = $this->validateUpload($image);

            $errors = array_merge($dishErrors, $uploadErrors);

            if (empty($errors)) {
                $dish['image'] = null;

                if (!empty($image)) {
                    $uniqName = uniqid() . $image['name'];
                    move_uploaded_file($image['tmp_name'], self::UPLOAD_DIR . $uniqName);

                    $dish['image'] = $uniqName;
                }

                $dishManager = new DishManager();
                $dishManager->insert($dish);

                header('Location: /admin/menu');
                return '';
            }
        }

        return $this->twig->render('Admin/Dish/add.html.twig', [
            'errors' => $errors,
            'dish' => $dish,
            'categories' => $categories,
        ]);
    }

    public function edit(int $id): string
    {
        $errors = [];
        $dishManager = new DishManager();
        $dish = $dishManager->selectOneById($id);

        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('title');

        if ($dish && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $dish = array_map('trim', $_POST);
            $image = $_FILES['image'];

            $dish['id'] = $id;

            $dishErrors = $this->validate($dish, $categories);
            $uploadErrors = $this->validateUpload($image);

            $errors = array_merge($dishErrors, $uploadErrors);

            if (empty($errors)) {
                $dishManager = new DishManager();

                $dish['image'] = null;

                if (!empty($image['name'])) {
                    $uniqName = uniqid() . $image['name'];
                    move_uploaded_file($image['tmp_name'], self::UPLOAD_DIR . $uniqName);

                    $dish['image'] = $uniqName;
                }

                $dishManager->update($dish);

                header('Location: /admin/menu');
                return '';
            }
        }

        return $this->twig->render('Admin/Dish/edit.html.twig', [
            'errors' => $errors,
            'dish' => $dish,
            'categories' => $categories,
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

    private function validateUpload(array $file)
    {
        $errors = [];
        if (!empty($file['name'])) {
            if ($file['error'] != 0) {
                $errors[] = 'Upload problem';
                return $errors;
            }

            $maxFileSize = 1000000;
            if ($file['size'] > $maxFileSize) {
                $errors[] = 'Le fichier doit faire moins de ' . $maxFileSize / 1000000 . 'Mo';
            }

            $authorizedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mime = mime_content_type($file['tmp_name']);

            if (!in_array($mime, $authorizedMimes)) {
                $mimeError = str_replace('image/', '', implode(', ', $authorizedMimes));
                $errors[] = 'Le fichier doit être un des types suivants : ' . $mimeError;
            }
        }

        return $errors;
    }

    private function validate(array $dish, array $categories): array
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

        $categoryIds = array_column($categories, 'id');

        if (!in_array($dish['category'], $categoryIds)) {
            $errors[] = 'Mauvaise catégorie';
        }

        return $errors;
    }
}
