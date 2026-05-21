<?php

namespace App\Exceptions;

use Exception;

class AccountBannedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Votre compte a été banni. Contactez un administrateur.');
    }
}
