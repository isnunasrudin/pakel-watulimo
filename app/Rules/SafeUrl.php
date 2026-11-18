<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class SafeUrl implements ValidationRule
{
    /**
     * @param  list<string>  $schemes
     */
    public function __construct(
        private array $schemes = ['http', 'https'],
        private bool $absolute = false,
        private ?string $message = null,
    ) {}

    /**
     * Validate that the value is a safe URL (or an allowed internal path).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_string($value) || Str::sanitizeUrl($value, $this->schemes) === null) {
            $fail($this->message ?? 'URL tidak valid.');

            return;
        }

        if ($this->absolute && filter_var($value, FILTER_VALIDATE_URL) === false) {
            $fail($this->message ?? 'URL harus berupa URL absolut yang valid.');
        }
    }
}
