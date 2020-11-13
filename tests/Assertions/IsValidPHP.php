<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Assertions;

use Exception;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Constraint\Constraint;

final class IsValidPHP extends Constraint
{
    public function toString(): string
    {
        return 'is valid PHP';
    }

    protected function matches($other): bool
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);

        try {
            if (null === $parser->parse($other)) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
