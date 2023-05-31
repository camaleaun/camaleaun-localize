<?php
/**
 * Camaleaun\Localize\Formatting setup
 *
 * @package CamaleaunLocalize
 * @since 1.0.0
 */

namespace Camaleaun\Localize;

defined('ABSPATH') || exit;

/**
 * Main Camaleaun\Localize\Formatting Class.
 *
 * @class Camaleaun\Localize\Formatting
 */
class Formatting
{

    /**
     * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
     * Non-scalar values are ignored.
     *
     * @param string|array $var Data to sanitize.
     * @return string|array
     */
    public static function clean($var)
    {
        if (is_array($var)) {
            return array_map(array(__CLASS__, __METHOD__), $var);
        } else {
            return is_scalar($var) ? sanitize_text_field($var) : $var;
        }
    }
}
