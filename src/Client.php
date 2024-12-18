<?php
/**
 * Google Maps Static API Client Class
 *
 * @package     ArrayPress\Google\MapsStatic
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\Google\MapsStatic;

use WP_Error;

/**
 * Class Client
 *
 * A comprehensive utility class for interacting with the Google Maps Static API.
 */
class Client {

	/**
	 * API key for Google Maps
	 *
	 * @var string
	 */
	private string $api_key;

	/**
	 * Base URL for the Static Maps API
	 *
	 * @var string
	 */
	private const API_ENDPOINT = 'https://maps.googleapis.com/maps/api/staticmap';

	/**
	 * Default map options
	 *
	 * @var array
	 */
	private array $default_options = [
		'size'     => '600x300',
		'zoom'     => 14,
		'scale'    => 1,
		'format'   => 'png',
		'maptype'  => 'roadmap',
		'language' => '',
		'region'   => '',
		'heading'  => 0,
		'pitch'    => 0
	];

	/**
	 * Allowed image formats
	 *
	 * @var array
	 */
	private array $allowed_formats = [ 'png', 'png8', 'png32', 'gif', 'jpg', 'jpg-baseline' ];

	/**
	 * Allowed map types
	 *
	 * @var array
	 */
	private array $allowed_map_types = [ 'roadmap', 'satellite', 'terrain', 'hybrid' ];

	/**
	 * Initialize the Static Maps client
	 *
	 * @param string $api_key API key for Google Maps
	 */
	public function __construct( string $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * Set map dimensions
	 *
	 * @param int $width  Map width in pixels
	 * @param int $height Map height in pixels
	 *
	 * @return self
	 */
	public function set_size( int $width, int $height ): self {
		$this->default_options['size'] = "{$width}x{$height}";

		return $this;
	}

	/**
	 * Set map zoom level
	 *
	 * @param int $zoom Zoom level (0-21)
	 *                  0: World view
	 *                  5: Continent/Region
	 *                  10: City
	 *                  15: Streets
	 *                  20: Buildings
	 *
	 * @return self
	 */
	public function set_zoom( int $zoom ): self {
		$this->default_options['zoom'] = max( 0, min( 21, $zoom ) );

		return $this;
	}

	/**
	 * Set map type
	 *
	 * @param string $type Map type (roadmap, satellite, terrain, hybrid)
	 *
	 * @return self
	 */
	public function set_map_type( string $type ): self {
		if ( in_array( $type, $this->allowed_map_types ) ) {
			$this->default_options['maptype'] = $type;
		}

		return $this;
	}

	/**
	 * Set image format
	 *
	 * @param string $format Image format (png, png8, png32, gif, jpg, jpg-baseline)
	 *
	 * @return self
	 */
	public function set_format( string $format ): self {
		if ( in_array( $format, $this->allowed_formats ) ) {
			$this->default_options['format'] = $format;
		}

		return $this;
	}

	/**
	 * Set map scale
	 *
	 * @param int $scale Map scale (1, 2, 4)
	 *
	 * @return self
	 */
	public function set_scale( int $scale ): self {
		if ( in_array( $scale, [ 1, 2, 4 ] ) ) {
			$this->default_options['scale'] = $scale;
		}

		return $this;
	}

	/**
	 * Set the language for map labels
	 *
	 * @param string $language Language code (e.g., 'en', 'es', 'fr')
	 *                         See: https://developers.google.com/maps/faq#languagesupport
	 *
	 * @return self
	 */
	public function set_language( string $language ): self {
		$this->default_options['language'] = $language;

		return $this;
	}

	/**
	 * Set the region bias for the map
	 *
	 * @param string $region Region code (e.g., 'US', 'GB')
	 *                       See: https://developers.google.com/maps/coverage
	 *
	 * @return self
	 */
	public function set_region( string $region ): self {
		$this->default_options['region'] = $region;

		return $this;
	}

	/**
	 * Set the street view camera heading
	 *
	 * @param float $degrees Heading in degrees (0-360)
	 *                       0: North
	 *                       90: East
	 *                       180: South
	 *                       270: West
	 *
	 * @return self
	 */
	public function set_heading( float $degrees ): self {
		$this->default_options['heading'] = max( 0, min( 360, $degrees ) );

		return $this;
	}

	/**
	 * Set the street view camera pitch
	 *
	 * @param float $degrees Pitch in degrees (-90 to 90)
	 *                       -90: Straight down
	 *                       0: Horizontal
	 *                       90: Straight up
	 *
	 * @return self
	 */
	public function set_pitch( float $degrees ): self {
		$this->default_options['pitch'] = max( - 90, min( 90, $degrees ) );

		return $this;
	}

	/**
	 * Reset all options to their default values
	 *
	 * @return self
	 */
	public function reset_options(): self {
		$this->default_options = [
			'size'     => '600x300',
			'zoom'     => 14,
			'scale'    => 1,
			'format'   => 'png',
			'maptype'  => 'roadmap',
			'language' => '',
			'region'   => '',
			'heading'  => 0,
			'pitch'    => 0
		];

		return $this;
	}

	/**
	 * Generate static map URL for a location
	 *
	 * @param float|string $location Latitude,longitude or address
	 * @param array        $options  Additional options for the map
	 *
	 * @return string|WP_Error     URL for the static map or WP_Error on failure
	 */
	public function location( $location, array $options = [] ) {
		$params = array_merge(
			$this->default_options,
			$options,
			[ 'center' => $location ]
		);

		return $this->generate_url( $params );
	}

	/**
	 * Generate static map URL with markers
	 *
	 * @param array $markers Array of marker configurations
	 * @param array $options Additional options for the map
	 *
	 * @return string|WP_Error URL for the static map or WP_Error on failure
	 */
	public function markers( array $markers, array $options = [] ) {
		$params        = array_merge( $this->default_options, $options );
		$marker_params = [];

		foreach ( $markers as $marker ) {
			$marker_string = '';

			if ( isset( $marker['style'] ) ) {
				$valid_styles = [
					'size',
					'color',
					'label',
					'scale',
					'anchor',
					'icon'
				];

				foreach ( $valid_styles as $style ) {
					if ( isset( $marker['style'][ $style ] ) ) {
						$marker_string .= "{$style}:{$marker['style'][$style]}|";
					}
				}
			}

			if ( isset( $marker['locations'] ) ) {
				$locations     = is_array( $marker['locations'] ) ? $marker['locations'] : [ $marker['locations'] ];
				$marker_string .= implode( '|', $locations );
				if ( $marker_string ) {
					$marker_params[] = $marker_string;
				}
			}
		}

		if ( ! empty( $marker_params ) ) {
			$params['markers'] = $marker_params;
		}

		return $this->generate_url( $params );
	}

	/**
	 * Generate static map URL with a path
	 *
	 * @param array $path_points Array of path points
	 * @param array $options     Additional options for the map
	 *
	 * @return string|WP_Error   URL for the static map or WP_Error on failure
	 */
	public function path( array $path_points, array $options = [] ) {
		$params = array_merge( $this->default_options, $options );

		$path_string = '';
		if ( isset( $options['path_style'] ) ) {
			foreach ( $options['path_style'] as $key => $value ) {
				$path_string .= "$key:$value|";
			}
		}

		$path_string    .= implode( '|', $path_points );
		$params['path'] = $path_string;

		return $this->generate_url( $params );
	}

	/**
	 * Generate static map URL with custom styles
	 *
	 * @param array $styles  Map style array
	 * @param array $options Additional map options
	 *
	 * @return string|WP_Error URL for the static map
	 */
	public function styled( array $styles, array $options = [] ) {
		$params = array_merge( $this->default_options, $options );

		foreach ( $styles as $index => $style ) {
			$style_string = $this->format_style( $style );
			if ( $style_string ) {
				$params["style[$index]"] = $style_string;
			}
		}

		return $this->generate_url( $params );
	}

	/**
	 * Generate an HTML img tag for the static map
	 *
	 * @param string $url   The static map URL
	 * @param array  $attrs Additional img attributes
	 *
	 * @return string      Complete img HTML
	 */
	public function generate_image_tag( string $url, array $attrs = [] ): string {
		$default_attrs = [
			'alt'     => 'Google Map',
			'loading' => 'lazy'
		];

		$merged_attrs = array_merge( $default_attrs, $attrs );
		$attr_string  = '';

		foreach ( $merged_attrs as $key => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$attr_string .= " $key";
				}
			} else {
				$attr_string .= " $key=\"" . esc_attr( $value ) . "\"";
			}
		}

		return sprintf(
			'<img src="%s"%s>',
			esc_url( $url ),
			$attr_string
		);
	}

	/**
	 * Save map image to WordPress media library
	 *
	 * @param string $url  The static map URL
	 * @param array  $args Additional arguments for the media item
	 *
	 * @return int|WP_Error Attachment ID on success, WP_Error on failure
	 */
	public function save_to_media_library( string $url, array $args = [] ) {
		$defaults = [
			'title'       => 'Google Static Map',
			'filename'    => 'google-map-' . time(),
			'description' => '',
			'alt'         => 'Google Static Map',
			'folder'      => 'google-maps',
		];

		$args = wp_parse_args( $args, $defaults );

		$temp_file = download_url( $url );

		if ( is_wp_error( $temp_file ) ) {
			return $temp_file;
		}

		$mime_type = wp_get_image_mime( $temp_file );

		if ( ! $mime_type ) {
			unlink( $temp_file );

			return new WP_Error( 'invalid_image', __( 'Invalid image file', 'arraypress' ) );
		}

		$extension   = explode( '/', $mime_type )[1] ?? 'png';
		$upload_dir  = wp_upload_dir();
		$maps_folder = trailingslashit( $upload_dir['basedir'] ) . $args['folder'];

		if ( ! file_exists( $maps_folder ) ) {
			wp_mkdir_p( $maps_folder );
		}

		$file = [
			'name'     => $args['filename'] . '.' . $extension,
			'type'     => $mime_type,
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file )
		];

		add_filter( 'upload_dir', function ( $dirs ) use ( $args ) {
			$dirs['subdir'] = '/' . $args['folder'];
			$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
			$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];

			return $dirs;
		} );

		$attachment_id = media_handle_sideload( $file, 0, $args['title'], [
			'post_content' => $args['description'],
			'post_excerpt' => $args['description'],
			'post_title'   => $args['title']
		] );

		remove_filter( 'upload_dir', function () {
		} );

		@unlink( $temp_file );

		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $args['alt'] );

		return $attachment_id;
	}

	/**
	 * Check if the API key is valid
	 *
	 * @return bool|WP_Error True if valid, WP_Error if invalid
	 */
	public function validate_api_key() {
		$test_url = $this->location( '0,0', [ 'size' => '1x1' ] );

		if ( is_wp_error( $test_url ) ) {
			return $test_url;
		}

		$response = wp_remote_get( $test_url );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );

		if ( $code === 200 ) {
			return true;
		}

		return new WP_Error(
			'invalid_api_key',
			__( 'The provided Google Maps API key is invalid', 'arraypress' )
		);
	}

	/**
	 * Generate the API URL
	 *
	 * @param array $params URL parameters
	 *
	 * @return string|WP_Error
	 */
	private function generate_url( array $params ) {
		if ( empty( $this->api_key ) ) {
			return new WP_Error(
				'missing_api_key',
				__( 'Google Maps API key is required', 'arraypress' )
			);
		}

		$params['key'] = $this->api_key;
		$query_params  = [];

		foreach ( $params as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $item ) {
					$query_params[] = $key . '=' . urlencode( $item );
				}
			} else if ( $value !== '' ) {
				$query_params[] = $key . '=' . urlencode( $value );
			}
		}

		return self::API_ENDPOINT . '?' . implode( '&', $query_params );
	}

	/**
	 * Format style array into string
	 *
	 * @param array $style Style configuration
	 *
	 * @return string     Formatted style string
	 */
	private function format_style( array $style ): string {
		$style_string = '';

		if ( isset( $style['feature'] ) ) {
			$style_string .= "feature:{$style['feature']}";
		}

		if ( isset( $style['element'] ) ) {
			$style_string .= "|element:{$style['element']}";
		}

		if ( isset( $style['rules'] ) && is_array( $style['rules'] ) ) {
			foreach ( $style['rules'] as $rule => $value ) {
				$style_string .= "|{$rule}:{$value}";
			}
		}

		return $style_string;
	}

}