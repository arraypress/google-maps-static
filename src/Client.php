<?php
/**
 * Google Maps Static API Client Class
 *
 * A comprehensive utility class for interacting with the Google Maps Static API.
 * This class provides methods for generating static map images with support for
 * various features including custom markers, paths, styling, and more.
 *
 * @package     ArrayPress\Google\MapsStatic
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\Google\MapsStatic;

use ArrayPress\Google\MapsStatic\Traits\Parameters;
use WP_Error;

/**
 * Class Client
 *
 * Main client class for generating static maps using the Google Maps Static API.
 *
 * @package ArrayPress\Google\MapsStatic
 */
class Client {
	use Parameters;

	/**
	 * Base URL for the Static Maps API
	 *
	 * @var string
	 */
	private const API_ENDPOINT = 'https://maps.googleapis.com/maps/api/staticmap';

	/**
	 * Initialize the Static Maps client
	 *
	 * @param string $api_key Google Maps API key
	 */
	public function __construct( string $api_key ) {
		$this->set_api_key( $api_key );
	}

	/**
	 * Generate static map URL for a location
	 *
	 * @param float|string $location Latitude,longitude or address
	 *
	 * @return string|WP_Error URL for the static map or WP_Error on failure
	 */
	public function location( $location ) {
		$params = array_merge(
			$this->get_all_params()['map'],
			[ 'center' => $location ]
		);

		return $this->generate_url( $params );
	}

	/**
	 * Generate static map URL with markers
	 *
	 * @param array $markers Array of marker locations
	 *
	 * @return string|WP_Error URL for the static map or WP_Error on failure
	 */
	public function markers( array $markers ) {
		$params        = $this->get_all_params()['map'];
		$marker_params = [];

		foreach ( $markers as $marker ) {
			$marker_string = '';
			$style         = $this->get_marker_style();

			if ( ! empty( $style ) ) {
				foreach ( $style as $key => $value ) {
					$marker_string .= "{$key}:{$value}|";
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
	 *
	 * @return string|WP_Error URL for the static map or WP_Error on failure
	 */
	public function path( array $path_points ) {
		$params      = $this->get_all_params()['map'];
		$path_string = '';

		$style = $this->get_path_style();
		if ( ! empty( $style ) ) {
			foreach ( $style as $key => $value ) {
				$path_string .= "{$key}:{$value}|";
			}
		}

		$path_string    .= implode( '|', $path_points );
		$params['path'] = $path_string;

		return $this->generate_url( $params );
	}

	/**
	 * Generate static map URL with custom styles
	 *
	 * @return string|WP_Error URL for the static map or WP_Error on failure
	 */
	public function styled() {
		$params = $this->get_all_params()['map'];
		$styles = $this->get_styles();

		foreach ( $styles as $index => $style ) {
			$style_string = $this->format_style( $style );
			if ( $style_string ) {
				$params["style[{$index}]"] = $style_string;
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

		// Download the image
		$temp_file = download_url( $url );
		if ( is_wp_error( $temp_file ) ) {
			return $temp_file;
		}

		// Verify it's an image
		$mime_type = wp_get_image_mime( $temp_file );
		if ( ! $mime_type ) {
			unlink( $temp_file );

			return new WP_Error( 'invalid_image', __( 'Invalid image file', 'arraypress' ) );
		}

		// Setup the file array
		$extension = explode( '/', $mime_type )[1] ?? 'png';
		$file      = [
			'name'     => $args['filename'] . '.' . $extension,
			'type'     => $mime_type,
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file )
		];

		// Prepare upload directory
		add_filter( 'upload_dir', function ( $dirs ) use ( $args ) {
			$dirs['subdir'] = '/' . $args['folder'];
			$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
			$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];

			return $dirs;
		} );

		// Add the image to media library
		$attachment_id = media_handle_sideload( $file, 0, $args['title'], [
			'post_content' => $args['description'],
			'post_excerpt' => $args['description'],
			'post_title'   => $args['title']
		] );

		// Clean up
		remove_filter( 'upload_dir', function () {
		} );
		@unlink( $temp_file );

		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		// Set alt text
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $args['alt'] );

		return $attachment_id;
	}

	/**
	 * Check if the API key is valid
	 *
	 * @return bool|WP_Error True if valid, WP_Error if invalid
	 */
	public function validate_api_key() {
		$test_url = $this->location( '0,0' );

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