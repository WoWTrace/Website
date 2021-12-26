<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule;

class CsvValidation implements Rule, ImplicitRule
{
    private string $message = 'Invalid csv format!';

    public function __construct(private int $maxLines = 20000, private ?string $rowRegex = null)
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $value = trim((str_replace("\r\n", "\n", $value)));
        $lineCount = substr_count($value, "\n") + 1;

        if ($lineCount > $this->maxLines) {
            $this->message = sprintf('A maximum of %u files per request is allowed.', $this->maxLines);

            return false;
        }

        if ($this->rowRegex) {
            $matchCount = preg_match_all($this->rowRegex, $value);

            return $lineCount === $matchCount;
        }

        return true;
    }

    public function message(): string
    {
        return $this->message;
    }
}
