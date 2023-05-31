<?php
/**
 * Camaleaun\Localize\Deprecated setup
 *
 * @package CamaleaunLocalize
 * @since 1.0.0
 */

namespace Camaleaun\Localize;

defined('ABSPATH') || exit;

/**
 * Main Camaleaun\Localize\Deprecated Class.
 *
 * @class Camaleaun\Localize\Deprecated
 */
class Deprecated
{

    /**
     * Wrapper for _doing_it_wrong().
     *
     * @since 1.0
     * @param string $function Function used.
     * @param string $message Message to log.
     * @param string $version Version the message was added in.
     */
    public static function doingItWrong($function, $message, $version)
    {
        $message .= ' Backtrace: ' . wp_debug_backtrace_summary();

        if (wp_doing_ajax()) {
            do_action('doing_it_wrong_run', $function, $message, $version);
            error_log("{$function} was called incorrectly. {$message}. This message was added in version {$version}.");
        } else {
            _doing_it_wrong($function, $message, $version);
        }
    }
}
