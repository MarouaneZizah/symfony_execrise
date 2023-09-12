<?php

declare(strict_types=1);

namespace App\Request;

use App\Validator\Symbol;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints as Assert;

class HistoricalQuotesRequest extends AbstractJsonRequest
{
    #[NotBlank()]
    #[Assert\Date]
    public readonly string $startDate;

    #[NotBlank()]
    #[Assert\Date]
    #[Assert\GreaterThan(propertyPath: 'startDate')]
    public readonly string $endDate;

    #[NotBlank()]
    #[Assert\Email]
    public readonly string $email;

    #[NotBlank()]
    #[Type('string')]
    #[Symbol()]
    public readonly string $symbol;
}