# Developer test

REST API endpoint to retrieve current server datetime with remote IP and GPS coordinates of request.

## Requirements

* PHP 5.6+ (tested up to 8.2)
* WordPress 5.7+

## Installation

1. Download the WordPress installable ZIP archive [camaleaun-localize.1.0.0.zip](https://github.com/camaleaun/camaleaun-localize/releases/download/v1.0.0/camaleaun-localize.1.0.0.zip).
2. Log in to your WordPress dashboard.
3. Navigate to "Plugins" > "Add New."
4. Click on the "Upload Plugin" button.
5. Choose the `camaleaun-localize.1.0.0.zip` file you downloaded.
6. Click on the "Install Now" button.
7. Once the installation is complete, click on the "Activate" button to activate the plugin.

## Usage

The usage is based in WordPress REST API.

### Definition

Query endpoint to retrieve localize data.

> `GET` **/camaleaun/v1/localize**

#### Arguments

There are no arguments for this endpoint.

#### Properties

Atribute    | Type       | Description
------------|------------|-----------------------------------------------------------
`datetime`  | *datetime* | The current server datetime in the site's timezone.
`remote_ip` | *string*   | Remote IP from request.
`location`  | *string*   | GPS coordinates (Latitude/Longitude) from the `remote_ip`.

#### Response Format

The default response format is JSON.
Successful requests will return a `200 OK` HTTP status.

##### Information about response

* Datetime is returned in ISO 8601 format: `YYYY-MM-DDTHH:MM:SS+00:00`, e.g. 2023-01-24T11:18:46+01:00.
* GPS coordinates (Latitude/Longitude) is returned in Decimal Degrees (DD) format, e.g. 49.202442, 16.615052.

### Example cURL Request

> **Note:** The examples provided in this README assume that permalinks are enabled in your WordPress installation. If you are using [non-pretty permalinks](https://wordpress.org/support/article/using-permalinks/), you can pass the REST API route as a query string parameter: `http://example.com/?rest_route=/camaleaun/v1/localize`. Please make sure to adjust the examples according to your permalink settings.

```
$ curl https://example.com/wp-json/camaleaun/v1/localize
```

#### JSON response example

```
{
  "current_time": "2023-01-24T11:18:46+01:00",
  "remote_ip": "69.241.108.45",
  "location": "49.202442, 16.615052"
}
```

## Links

* Trellis: https://roots.io/docs/trellis/master/installation/
* Bedrock: https://roots.io/docs/bedrock/master/installation/
* Sage: https://roots.io/docs/sage/9.x/installation/
* WordPress REST API: https://developer.wordpress.org/rest-api/
