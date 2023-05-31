<?php
/**
 * REST Controller
 *
 * This class extend `WP_REST_Controller` in order to include /batch endpoint
 * for almost all endpoints in Camaleaun\Localize REST API.
 *
 * It's required to follow "Controller Classes" guide before extending this class:
 * <https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/>
 *
 * NOTE THAT ONLY CODE RELEVANT FOR MOST ENDPOINTS SHOULD BE INCLUDED INTO THIS CLASS.
 * If necessary extend this class and create new abstract classes like `Localize\RestApi\Version1\LocalizeController`.
 *
 * @class   Camaleaun\Localize\RestApi\RestController
 * @package WooCommerce\RestApi
 * @see     https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/
 */

namespace Camaleaun\Localize\RestApi\Controllers\Version1;

defined('ABSPATH') || exit;

/**
 * Abstract Rest Controller Class
 *
 * @package CamaleaunLocalize\RestApi
 * @extends WP_REST_Controller
 * @version 1.0.0
 */
abstract class RestController extends \WP_REST_Controller
{

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'camaleaun/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = '';
}
