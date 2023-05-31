<?php
/**
 * Camaleaun\Localize setup
 *
 * @package CamaleaunLocalize
 * @since 1.0.0
 */

namespace Camaleaun;

use Localize\Deprecated;

defined('ABSPATH') || exit;

/**
 * Main Camaleaun\Localize Class.
 *
 * @class Camaleaun\Localize
 */
final class Localize
{

    /**
     * CamaleaunLocation version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * The single instance of the class.
     *
     * @var Camaleaun\Localize
     * @since 1.0
     */
    protected static $instance = null;

    /**
     * Deprecated instance.
     *
     * @var Camaleaun\Deprecated
     */
    public $deprecated = null;

    /**
     * Main Camaleaun\Localize Instance.
     *
     * Ensures only one instance of Camaleaun\Localize is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @see CamaleaunLocalize()
     * @return Camaleaun\Localize - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0
     */
    public function __clone()
    {
        Deprecated::doingItWrong(__FUNCTION__, __('Cloning is forbidden.', 'camaleaun-localize'), '1.0');
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0
     */
    public function __wakeup()
    {
        Deprecated::doingItWrong(
            __FUNCTION__,
            __('Unserializing instances of this class is forbidden.', 'camaleaun-localize'),
            '1.0'
        );
    }

    /**
     * Camaleaun\Localize Constructor.
     */
    public function __construct()
    {
        $this->initHooks();
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0
     */
    private function initHooks()
    {
        add_action('init', array($this, 'loadRestApi'));
    }

    /**
     * Load REST API.
     */
    public function loadRestApi()
    {
        \Camaleaun\Localize\RestApi\Server::instance()->init();
    }
}
