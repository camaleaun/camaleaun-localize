<?php
/**
 * Geolocation class
 *
 * Handles geolocation and updating the geolocation database.
 *
 * @package CamaleaunLocalize\Classes
 * @version 1.0.0
 */

namespace Camaleaun\Localize;

use Camaleaun\Localize\Formatting;
use \Camaleaun\GeolocationCoordinates as Coordinates;

defined('ABSPATH') || exit;

/**
 * Camaleaun\Geolocation Class.
 */
class Geolocation
{

    /**
     * API endpoints for looking up user IP address.
     *
     * @var array
     */
    private static $ipLookupApis = array(
        'ipify' => 'http://api.ipify.org/',
        'ipecho' => 'http://ipecho.net/plain',
        'ident' => 'http://ident.me',
        'tnedi' => 'http://tnedi.me',
    );

    /**
     * API endpoints for geolocating an IP address
     *
     * @var array
     */
    private static $geoipApis = array(
        'ip-api.com' => 'http://ip-api.com/json/%s',
    );

    /**
     * Get current user IP Address.
     *
     * @return string
     */
    public static function getIpAddress()
    {
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REAL_IP']));
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
            // Make sure we always only send through the first IP in the list which should always be the client IP.
            return (string) rest_is_ip_address(
                trim(current(preg_split('/,/', sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR'])))))
            );
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }
        return '';
    }

    /**
     * Get user IP Address using an external service.
     * This can be used as a fallback for users on localhost where
     * getIpAddress() will be a local IP and non-geolocatable.
     *
     * @return string
     */
    public static function getExternalIpAddress()
    {
        $externalIpAddress = '0.0.0.0';

        if ('' !== self::getIpAddress()) {
            $transientName = 'external_ip_address_'.self::getIpAddress();
            $externalIpAddress = get_transient($transientName);
        }

        if (false === $externalIpAddress) {
            $externalIpAddress = '0.0.0.0';
            $ipLookupServices = apply_filters('camaleaun_localize_geolocation_ip_lookup_apis', self::$ipLookupApis);
            $ipLookupServicesKeys = array_keys($ipLookupServices);
            shuffle($ipLookupServicesKeys);

            foreach ($ipLookupServicesKeys as $service_name) {
                $serviceEndpoint = $ipLookupServices[ $service_name ];
                $response = wp_safe_remote_get(
                    $serviceEndpoint,
                    array(
                        'timeout' => 2,
                        'user-agent' => 'CamaleaunLocalize/'.CamaleaunLocalize()->version,
                    )
                );

                if (! is_wp_error($response) && rest_is_ip_address($response['body'])) {
                    $externalIpAddress = apply_filters(
                        'camaleaun_localize_geolocation_ip_lookup_api_response',
                        Formatting::clean($response['body']),
                        $service_name
                    );
                    break;
                }
            }

            set_transient($transientName, $externalIpAddress, DAY_IN_SECONDS);
        }

        return $externalIpAddress;
    }

    /**
     * Geolocate coordinates an IP address.
     *
     * @param  string $ipAddress   IP Address.
     * @param  bool   $fallback    If true, fallbacks to alternative IP detection (can be slower).
     * @return Coordinates
     */
    public static function geolocateCoordinatesIp($ipAddress = '', $fallback = false)
    {
        // Filter to allow custom geolocation of the IP address.
        $coordinates = apply_filters('camaleaun_localize_geolocate_coordinates_ip', false, $ipAddress, $fallback);

        if (false !== $coordinates) {
            return $coordinates;
        }

        if (empty($ipAddress)) {
            $ipAddress = self::getIpAddress();
        }

        $coordinates = self::geolocateCoordinatesViaApi($ipAddress);

        // It's possible that we're in a local environment, in which case the geolocation needs to be done from the
        // external address.
        if ('' === $coordinates && $fallback) {
            $externalIpAddress = self::getExternalIpAddress();

            // Only bother with this if the external IP differs.
            if ('0.0.0.0' !== $externalIpAddress && $externalIpAddress !== $ipAddress) {
                return self::geolocateIp($externalIpAddress, false, $apiFallback);
            }
        }

        return new Coordinates($coordinates);
    }

    /**
     * Use APIs to Geolocate the user.
     *
     * Geolocation APIs can be added through the use of the camaleaun_localize_geolocation_geoip_apis filter.
     * Provide a name=>value pair for service-slug=>endpoint.
     *
     * If APIs are defined, one will be chosen at random to fulfil the request. After completing, the result
     * will be cached in a transient.
     *
     * @param  string $ipAddress IP address.
     * @return string
     */
    private static function geolocateCoordinatesViaApi($ipAddress)
    {
        $coordinates = get_transient('geoip_coordinates_'.$ipAddress);

        if (false === $coordinates) {
            $geoip_services = apply_filters('camaleaun_localize_geolocation_geoip_apis', self::$geoipApis);

            if (empty($geoip_services)) {
                return '';
            }

            $geoip_services_keys = array_keys($geoip_services);

            shuffle($geoip_services_keys);

            foreach ($geoip_services_keys as $service_name) {
                $serviceEndpoint = $geoip_services[ $service_name ];
                $response = wp_safe_remote_get(
                    sprintf($serviceEndpoint, $ipAddress),
                    array(
                        'timeout' => 2,
                        'user-agent' => 'CamaleaunLocalize/'.CamaleaunLocalize()->version,
                    )
                );

                if (! is_wp_error($response) && $response['body']) {
                    switch ($service_name) {
                        case 'ip-api.com':
                            $data = json_decode($response['body']);
                            $coordinates = '';
                            if (isset($data->lat) && isset($data->lon)) {
                                $coordinates = sprintf('%s, %s', $data->lat, $data->lon);
                            }
                            break;
                        default:
                            $coordinates = apply_filters(
                                'camaleaun_localize_geolocation_geoip_response_'.$service_name,
                                '',
                                $response['body']
                            );
                            break;
                    }

                    $coordinates = sanitize_text_field($coordinates);

                    if ($coordinates) {
                        break;
                    }
                }
            }

            set_transient('geoip_coordinates_'.$ipAddress, $coordinates, DAY_IN_SECONDS);
        }

        return $coordinates;
    }
}
