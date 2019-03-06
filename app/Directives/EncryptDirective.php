<?php

namespace App\Directives;

use Nuwave\Lighthouse\Support\Contracts\ArgTransformerDirective;

class EncryptDirective implements ArgTransformerDirective
{
    /**
     * Directive name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'encrypt';
    }

    /**
     * Encrypt the input with reversible encryption.
     *
     * @param  string  $argumentValue
     *
     * @return string
     */
    public function transform($argumentValue): string
    {
        return encrypt($argumentValue);
    }
}
