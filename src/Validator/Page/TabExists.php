<?php

namespace App\Validator\Page;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TabExists extends Constraint
{
    public string $message = 'tab.error.parent.unknow';
}