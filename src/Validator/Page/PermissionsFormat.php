<?php

namespace App\Validator\Page;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PermissionsFormat extends Constraint
{
    public string $message = 'tab.error.permissions.format';
}