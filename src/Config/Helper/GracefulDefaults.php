<?php

namespace EFrane\PharBuilder\Config\Helper;

use EFrane\PharBuilder\Exception\ConfigurationException;

trait GracefulDefaults
{
    /**
     * Gracefully default if a non-required option is missing.
     *
     * @param array<string,mixed> $options
     * @param mixed $default
     *
     * @return mixed
     */
    public function default(array $options, string $optionName, $default)
    {
        return $options[$optionName] ?? $default;
    }

    /**
     * Gracefully fail with a helpful error message if a required option is not configured.
     *
     * @param array<string,mixed> $options
     * @param string $qualifier if the option is in a section, it may be given here
     *
     * @return mixed
     */
    public function required(array $options, string $optionName, string $qualifier = '')
    {
        if (!array_key_exists($optionName, $options)) {
            $qualifiedOptionName = ('' !== $qualifier) ? $qualifier.'.'.$optionName : $optionName;
            throw ConfigurationException::missingConfigurationValue($qualifiedOptionName);
        }

        return $options[$optionName];
    }
}
