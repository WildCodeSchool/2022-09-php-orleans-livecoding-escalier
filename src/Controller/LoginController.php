<?php

namespace App\Controller;

use App\Model\UserManager;

class LoginController extends AbstractController
{
    /**
     * Display home page
     */
    public function login(): string
    {
        $errors = $credientials = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $credientials = array_map('trim', $_POST);
            if (empty($credientials['email'])) {
                $errors[] = 'L\'email est obligatoire';
            }
            if (!filter_var($credientials['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'email n\'a pas le bon format';
            }

            $userManager = new UserManager();
            $user = $userManager->selectOneByEmail($credientials['email']);

            if ($user === false) {
                $errors[] = 'L\'email est inconnu';
            } elseif (!password_verify($credientials['password'], $user['password'])) {
                $errors[] = 'Mot de passe incorrect';
            }

            if (empty($errors)) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: /');
                return '';
            }
        }
        return $this->twig->render('Login/login.html.twig', [
            'errors' => $errors,
            'credentials' => $credientials,
        ]);
    }
}
