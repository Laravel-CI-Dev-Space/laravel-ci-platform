<?php

namespace App\Exceptions;

use Exception;

class AccountEmailConflictException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            "Un compte existe déjà avec l'adresse email associée à ce compte GitHub. Contactez un administrateur pour lier vos comptes."
        );
    }
}
