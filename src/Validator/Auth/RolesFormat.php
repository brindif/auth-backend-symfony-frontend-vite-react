<?php

namespace App\Validator\Auth;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class RolesFormat extends Constraint
{
    public string $message = 'tab.error.roles.format';
}