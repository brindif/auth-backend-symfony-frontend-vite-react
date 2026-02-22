<?php

namespace App\Validator\Page;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TabsExists extends Constraint
{
  public string $message = 'schema.error.tabs.unknow';
}