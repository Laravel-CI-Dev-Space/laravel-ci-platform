<?php

namespace App\Exceptions;

use Exception;

class AccountSuspendedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Votre compte a été suspendu. Contactez un administrateur.');
    }
}
