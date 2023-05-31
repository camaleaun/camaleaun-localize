<?php
/**
 * Singleton class trait.
 *
 * @package CamaleaunLocalize\Utilities
 */

namespace Camaleaun\Localize\RestApi\Utilities;

use Camaleaun\Localize\Deprecated;

/**
 * Singleton trait.
 */
trait SingletonTrait
{
    /**
     * The single instance of the class.
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * Get class instance.
     *
     * @return object Instance.
     */
    final public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Prevent cloning.
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserializing.
     */
    final public function __wakeup()
    {
        Deprecated::doingItWrong(
            __FUNCTION__,
            __('Unserializing instances of this class is forbidden.', 'camaleaun-localize'),
            '1.0'
        );
        die();
    }
}
