<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use App\Model\UserManager;
use Exception;

/**
 * Initialized some Controller common features (Twig...)
 */
abstract class AbstractController
{
    protected Environment $twig;


    public function __construct()
    {
        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $this->twig = new Environment(
            $loader,
            [
                'cache' => false,
                'debug' => true,
            ]
        );
        $this->twig->addExtension(new DebugExtension());

        $user = null;
        if (isset($_SESSION['user_id'])) {
            $userManager = new UserManager();
            $user = $userManager->selectOneById($_SESSION['user_id']);
        }
        $this->twig->addGlobal('user', $user);
    }

    protected function isAuthenticated()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            throw new Exception('Unauthorized access');
        }
    }
}
