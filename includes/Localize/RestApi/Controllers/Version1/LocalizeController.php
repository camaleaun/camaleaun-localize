<?php
/**
 * REST API Camaleaun Localize
 *
 * Handles requests to the /localize endpoints.
 *
 * @package CamaleaunLocalize\RestApi
 * @since 1.0.0
 */

namespace Camaleaun\Localize\RestApi\Controllers\Version1;

use Camaleaun\Localize\RestApi\Controllers\Version1\RestController;
use Camaleaun\Localize\Geolocation;

defined('ABSPATH') || exit;

/**
 * System status tools controller.
 *
 * @package CamaleaunLocalize\RestApi
 */
class LocalizeController extends RestController
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
    protected $rest_base = 'localize';

    /**
     * Register the route for /localize
     */
    public function registerRoutes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array( $this, 'getItem' ),
                    'permission_callback' => '__return_true',
                ),
                'schema' => array( $this, 'get_public_item_schema' ),
            )
        );
    }

    /**
     * Get a system status info, by section.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function getItem($request)
    {
        $externalIpAddress = Geolocation::getExternalIpAddress();
        $location = Geolocation::geolocateCoordinatesIp($externalIpAddress);

        $localize = array(
            'current_time' => wp_date(DATE_ATOM),
            'remote_ip' => $externalIpAddress,
            'location' => $location->format('dd'),
        );

        $response = $this->prepareItemForResponse($localize, $request);

        return rest_ensure_response($response);
    }

    /**
     * Get the system status schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        $schema = array(
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'localize',
            'type' => 'object',
            'properties' => array(
                'datetime' => array(
                    'description' => __("The current server datetime in the site's timezone.", 'camaleaun-localize'),
                    'type' => 'datetime',
                    'context' => array('view'),
                    'readonly' => true,
                ),
                'remote_ip' => array(
                    'description' => __('Remote IP from request.', 'camaleaun-localize'),
                    'type' => 'string',
                    'context' => array('view'),
                    'readonly' => true,
                ),
                'location' => array(
                    'description' => __('GPS coordinates (Latitude/Longitude) from the remote IP.', 'camaleaun-localize'),
                    'type' => 'string',
                    'context' => array('view'),
                    'readonly' => true,
                ),
            ),
        );

        return $this->add_additional_fields_schema($schema);
    }

    /**
     * Prepare the system status response
     *
     * @param  array           $localize System status data.
     * @param  WP_REST_Request $request       Request object.
     * @return WP_REST_Response
     */
    public function prepareItemForResponse($localize, $request)
    {
        $data = $this->add_additional_fields_to_object($localize, $request);
        $data = $this->filter_response_by_context($data, 'view');

        $response = rest_ensure_response($data);

        /**
         * Filter the system status returned from the REST API.
         *
         * @param WP_REST_Response   $response The response object.
         * @param mixed              $localize System status
         * @param WP_REST_Request    $request  Request object.
         */
        return apply_filters('camaleaun_localize_rest_prepare_system_status', $response, $localize, $request);
    }
}
