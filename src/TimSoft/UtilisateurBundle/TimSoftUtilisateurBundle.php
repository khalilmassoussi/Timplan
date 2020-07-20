<?php

namespace TimSoft\UtilisateurBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TimSoftUtilisateurBundle extends Bundle
{
     public function getParent()
    {
        return 'FOSUserBundle';
    }
}
