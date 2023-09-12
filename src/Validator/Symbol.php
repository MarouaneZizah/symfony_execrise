<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Symbol extends Constraint
{
    public string $message = 'Expected valid symbol.';

    public string $mode = 'strict';
}