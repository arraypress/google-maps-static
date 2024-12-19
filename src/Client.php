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

use InvalidArgumentException;
use WP_Error;

/**
 * Class Client
 *
 * A comprehensive utility class for interacting with the Google Maps Static API.
 * This class provides methods for generating static map images with support for
 * various features including custom markers, paths, styling, and more.
 *
 * @package ArrayPress\Google\MapsStatic
 */
class Client {

	/**
	 * Valid map types
	 *
	 * @var array<string>
	 */
	private const VALID_MAP_TYPES = [
		'roadmap',
		'satellite',
		'terrain',
		'hybrid'
	];

	/**
	 * Valid image formats
	 *
	 * @var array<string>
	 */
	private const VALID_FORMATS = [
		'png',
		'png8',
		'png32',
		'gif',
		'jpg',
		'jpg-baseline'
	];

	/**
	 * Valid scale values
	 *
	 * @var array<int>
	 */
	private const VALID_SCALES = [ 1, 2, 4 ];

	/**
	 * Default options
	 *
	 * @var array<string, mixed>
	 */
	private const DEFAULT_OPTIONS = [
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
	 * Current options for map configuration
	 *
	 * @var array<string, mixed>
	 */
	private array $options;

	/**
	 * Initialize the Static Maps client
	 *
	 * @param string $api_key Google Maps API key
	 */
	public function __construct( string $api_key ) {
		$this->api_key = $api_key;
		$this->options = self::DEFAULT_OPTIONS;
	}

	/**
	 * Set map dimensions
	 *
	 * @param int $width  Map width in pixels (max 640 * scale)
	 * @param int $height Map height in pixels (max 640 * scale)
	 *
	 * @return self
	 * @throws InvalidArgumentException If dimensions are invalid
	 */
	public function set_size( int $width, int $height ): self {
		if ( $width <= 0 || $height <= 0 ) {
			throw new InvalidArgumentException( "Width and height must be positive integers." );
		}
		$this->options['size'] = "{$width}x{$height}";

		return $this;
	}

	/**
	 * Get current map dimensions
	 *
	 * @return array{width: int, height: int}
	 */
	public function get_size(): array {
		list( $width, $height ) = explode( 'x', $this->options['size'] );

		return [
			'width'  => (int) $width,
			'height' => (int) $height
		];
	}

	/**
	 * Set map zoom level
	 *
	 * @param int $level Zoom level (0-21)
	 *                   0: World view
	 *                   5: Continent/Region
	 *                   10: City
	 *                   15: Streets
	 *                   20: Buildings
	 *
	 * @return self
	 * @throws InvalidArgumentException If zoom level is invalid
	 */
	public function set_zoom( int $level ): self {
		if ( $level < 0 || $level > 21 ) {
			throw new InvalidArgumentException( "Invalid zoom level. Must be between 0 and 21." );
		}
		$this->options['zoom'] = $level;

		return $this;
	}

	/**
	 * Get current zoom level
	 *
	 * @return int Current zoom level
	 */
	public function get_zoom(): int {
		return $this->options['zoom'];
	}

	/**
	 * Set map type
	 *
	 * @param string $type Map type (roadmap, satellite, terrain, hybrid)
	 *
	 * @return self
	 * @throws InvalidArgumentException If map type is invalid
	 */
	public function set_map_type( string $type ): self {
		if ( ! in_array( $type, self::VALID_MAP_TYPES ) ) {
			throw new InvalidArgumentException( "Invalid map type. Must be one of: " . implode( ', ', self::VALID_MAP_TYPES ) );
		}
		$this->options['maptype'] = $type;

		return $this;
	}

	/**
	 * Get current map type
	 *
	 * @return string Current map type
	 */
	public function get_map_type(): string {
		return $this->options['maptype'];
	}

	/**
	 * Set image format
	 *
	 * @param string $format Image format
	 *
	 * @return self
	 * @throws InvalidArgumentException If format is invalid
	 */
	public function set_format( string $format ): self {
		if ( ! in_array( $format, self::VALID_FORMATS ) ) {
			throw new InvalidArgumentException( "Invalid format. Must be one of: " . implode( ', ', self::VALID_FORMATS ) );
		}
		$this->options['format'] = $format;

		return $this;
	}

	/**
	 * Get current image format
	 *
	 * @return string Current image format
	 */
	public function get_format(): string {
		return $this->options['format'];
	}

	/**
	 * Set map scale
	 *
	 * @param int $scale Map scale (1, 2, or 4)
	 *
	 * @return self
	 * @throws InvalidArgumentException If scale is invalid
	 */
	public function set_scale( int $scale ): self {
		if ( ! in_array( $scale, self::VALID_SCALES ) ) {
			throw new InvalidArgumentException( "Invalid scale. Must be one of: " . implode( ', ', self::VALID_SCALES ) );
		}
		$this->options['scale'] = $scale;

		return $this;
	}

	/**
	 * Get current map scale
	 *
	 * @return int Current scale value
	 */
	public function get_scale(): int {
		return $this->options['scale'];
	}

	/**
	 * Set language for map labels
	 *
	 * @param string $language Language code (e.g., 'en', 'es', 'fr')
	 *                         See: https://developers.google.com/maps/faq#languagesupport
	 *
	 * @return self
	 */
	public function set_language( string $language ): self {
		$this->options['language'] = $language;

		return $this;
	}

	/**
	 * Get current language setting
	 *
	 * @return string Current language code
	 */
	public function get_language(): string {
		return $this->options['language'];
	}

	/**
	 * Set region bias
	 *
	 * @param string $region Region code (e.g., 'US', 'GB')
	 *                       See: https://developers.google.com/maps/coverage
	 *
	 * @return self
	 */
	public function set_region( string $region ): self {
		$this->options['region'] = $region;

		return $this;
	}

	/**
	 * Get current region setting
	 *
	 * @return string Current region code
	 */
	public function get_region(): string {
		return $this->options['region'];
	}

	/**
	 * Set heading for street view
	 *
	 * @param float $degrees Heading in degrees (0-360)
	 *                       0: North
	 *                       90: East
	 *                       180: South
	 *                       270: West
	 *
	 * @return self
	 * @throws InvalidArgumentException If heading is invalid
	 */
	public function set_heading( float $degrees ): self {
		if ( $degrees < 0 || $degrees > 360 ) {
			throw new InvalidArgumentException( "Invalid heading. Must be between 0 and 360 degrees." );
		}
		$this->options['heading'] = $degrees;

		return $this;
	}

	/**
	 * Get current heading
	 *
	 * @return float Current heading in degrees
	 */
	public function get_heading(): float {
		return $this->options['heading'];
	}

	/**
	 * Set pitch for street view
	 *
	 * @param float $degrees Pitch in degrees (-90 to 90)
	 *                       -90: Straight down
	 *                       0: Horizontal
	 *                       90: Straight up
	 *
	 * @return self
	 * @throws InvalidArgumentException If pitch is invalid
	 */
	public function set_pitch( float $degrees ): self {
		if ( $degrees < - 90 || $degrees > 90 ) {
			throw new InvalidArgumentException( "Invalid pitch. Must be between -90 and 90 degrees." );
		}
		$this->options['pitch'] = $degrees;

		return $this;
	}

	/**
	 * Get current pitch
	 *
	 * @return float Current pitch in degrees
	 */
	public function get_pitch(): float {
		return $this->options['pitch'];
	}

	/**
	 * Get API key
	 *
	 * @return string Current API key
	 */
	public function get_api_key(): string {
		return $this->api_key;
	}

	/**
	 * Set API key
	 *
	 * @param string $api_key The API key to use
	 *
	 * @return self
	 */
	public function set_api_key( string $api_key ): self {
		$this->api_key = $api_key;

		return $this;
	}

	/**
	 * Get all current options
	 *
	 * @return array<string, mixed> Current options
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * Reset all options to their default values
	 *
	 * @return self
	 */
	public function reset_options(): self {
		$this->options = self::DEFAULT_OPTIONS;

		return $this;
	}

	/**
	 * Generate static map URL for a location
	 *
	 * @param float|string $location Latitude,longitude or address
	 * @param array        $options  Additional options for the map
	 *
	 * @return string|WP_Error URL for the static map or WP_Error on failure
	 */
	public function location( $location, array $options = [] ) {
		$params = array_merge(
			$this->options,
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
		$params        = array_merge( $this->options, $options );
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
	 * @return string|WP_Error URL for the static map or WP_Error on failure
	 */
	public function path( array $path_points, array $options = [] ) {
		$params = array_merge( $this->options, $options );

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
	 * @return string|WP_Error URL for the static map or WP_Error on failure
	 */
	public function styled( array $styles, array $options = [] ) {
		$params = array_merge( $this->options, $options );

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
	 * @return string Complete img HTML
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
			'<img src="%1$s"%2$s>',
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
	 * @return string|WP_Error URL for the static map or WP_Error on failure
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
					$query_params[] = $key . '=' . urlencode( (string) $item );
				}
			} else if ( $value !== '' ) {
				$query_params[] = $key . '=' . urlencode( (string) $value );
			}
		}

		return self::API_ENDPOINT . '?' . implode( '&', $query_params );
	}

	/**
	 * Format style array into string
	 *
	 * @param array $style Style configuration
	 *
	 * @return string Formatted style string
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