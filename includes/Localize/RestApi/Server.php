<?php
/**
 * Initialize this version of the REST API.
 *
 * @package CamaleaunLocalize\RestApi
 */

namespace Camaleaun\Localize\RestApi;

use Camaleaun\Localize\RestApi\Utilities\SingletonTrait;

defined('ABSPATH') || exit;

/**
 * Class responsible for loading the REST API and all REST API namespaces.
 */
class Server
{
    use SingletonTrait;

    /**
     * REST API namespaces and endpoints.
     *
     * @var array
     */
    protected $controllers = array();

    /**
     * Hook into WordPress ready to init the REST API as needed.
     */
    public function init()
    {
        add_action('rest_api_init', array( $this, 'registerRestRoutes' ), 10);
    }

    /**
     * Register REST API routes.
     */
    public function registerRestRoutes()
    {
        foreach ($this->getRestNamespaces() as $namespace => $controllers) {
            foreach ($controllers as $controller_name => $controller_class) {
                $this->controllers[ $namespace ][ $controller_name ] = new $controller_class();
                $this->controllers[ $namespace ][ $controller_name ]->registerRoutes();
            }
        }
    }

    /**
     * Get API namespaces - new namespaces should be registered here.
     *
     * @return array List of Namespaces and Main controller classes.
     */
    protected function getRestNamespaces()
    {
        return apply_filters(
            'camaleaun_localize_rest_api_get_rest_namespaces',
            array(
                'camaleaun-localize/v1' => $this->getV1Controllers(),
            )
        );
    }

    /**
     * List of controllers in the camaleaun-localize/v1 namespace.
     *
     * @return array
     */
    protected function getV1Controllers()
    {
        return array(
            'localize' => '\Camaleaun\Localize\RestApi\Controllers\Version1\LocalizeController',
        );
    }
}
