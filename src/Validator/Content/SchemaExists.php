<?php

namespace App\Validator\Content;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SchemaExists extends Constraint
{
    public string $message = 'note.error.schema.unknow';
}