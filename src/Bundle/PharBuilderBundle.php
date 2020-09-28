<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Bundle;


use EFrane\PharBuilder\DependencyInjection\PharBuilderExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PharBuilderBundle extends Bundle
{
    protected function getContainerExtensionClass()
    {
        return PharBuilderExtension::class;
    }
}
