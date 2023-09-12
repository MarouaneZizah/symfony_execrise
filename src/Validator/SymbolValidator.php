<?php

declare(strict_types=1);

namespace App\Validator;

use App\Service\NasdaqClient;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SymbolValidator extends ConstraintValidator
{
    private NasdaqClient $nasdaqClient;

    public function __construct(NasdaqClient $nasdaqClient)
    {
        $this->nasdaqClient = $nasdaqClient;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Symbol) {
            throw new UnexpectedTypeException($constraint, Symbol::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $symbols = $this->nasdaqClient->getCachedCompanies();

        $symbolExist = array_filter($symbols, function ($item) use ($value) {
            return $item['symbol'] === $value;
        });

        if (empty($symbolExist)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
        }
    }
}