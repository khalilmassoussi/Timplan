<?php

namespace TimSoft\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@TimSoftUtilisateur/Default/index.html.twig');
    }
}
