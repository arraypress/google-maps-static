<?php

declare( strict_types=1 );

namespace ArrayPress\Google\MapsStatic;

use InvalidArgumentException;

/**
 * Trait Parameters
 *
 * Manages parameters for the Google Maps Static API.
 *
 * @package ArrayPress\Google\MapsStatic
 */
trait Parameters {

	/**
	 * API key for Google Maps
	 *
	 * @var string
	 */
	private string $api_key;

	/**
	 * Valid map types
	 *
	 * @var array<string>
	 */
	private array $valid_map_types = [
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
	private array $valid_formats = [
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
	private array $valid_scales = [ 1, 2, 4 ];

	/**
	 * Map parameters
	 *
	 * @var array<string, mixed>
	 */
	private array $map_params = [
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
	 * Marker parameters
	 *
	 * @var array<string, mixed>
	 */
	private array $marker_params = [
		'size'   => '',
		'color'  => '',
		'label'  => '',
		'scale'  => '',
		'anchor' => '',
		'icon'   => ''
	];

	/**
	 * Path parameters
	 *
	 * @var array<string, mixed>
	 */
	private array $path_params = [
		'weight'    => '',
		'color'     => '',
		'fillcolor' => '',
		'geodesic'  => false,
		'points'    => []
	];

	/**
	 * Style parameters
	 *
	 * @var array<array>
	 */
	private array $style_params = [];

	/** API Key ******************************************************************/

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
	 * Get API key
	 *
	 * @return string
	 */
	public function get_api_key(): string {
		return $this->api_key;
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
		$this->map_params['size'] = "{$width}x{$height}";

		return $this;
	}

	/**
	 * Get current map dimensions
	 *
	 * @return array{width: int, height: int}
	 */
	public function get_size(): array {
		list( $width, $height ) = explode( 'x', $this->map_params['size'] );

		return [
			'width'  => (int) $width,
			'height' => (int) $height
		];
	}

	/**
	 * Set map zoom level
	 *
	 * @param int $level Zoom level (0-21)
	 *
	 * @return self
	 * @throws InvalidArgumentException If zoom level is invalid
	 */
	public function set_zoom( int $level ): self {
		if ( $level < 0 || $level > 21 ) {
			throw new InvalidArgumentException( "Invalid zoom level. Must be between 0 and 21." );
		}
		$this->map_params['zoom'] = $level;

		return $this;
	}

	/**
	 * Get current zoom level
	 *
	 * @return int
	 */
	public function get_zoom(): int {
		return $this->map_params['zoom'];
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
		if ( ! in_array( $type, $this->valid_map_types ) ) {
			throw new InvalidArgumentException( "Invalid map type. Must be one of: " . implode( ', ', $this->valid_map_types ) );
		}
		$this->map_params['maptype'] = $type;

		return $this;
	}

	/**
	 * Get current map type
	 *
	 * @return string
	 */
	public function get_map_type(): string {
		return $this->map_params['maptype'];
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
		if ( ! in_array( $format, $this->valid_formats ) ) {
			throw new InvalidArgumentException( "Invalid format. Must be one of: " . implode( ', ', $this->valid_formats ) );
		}
		$this->map_params['format'] = $format;

		return $this;
	}

	/**
	 * Get current image format
	 *
	 * @return string
	 */
	public function get_format(): string {
		return $this->map_params['format'];
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
		if ( ! in_array( $scale, $this->valid_scales ) ) {
			throw new InvalidArgumentException( "Invalid scale. Must be one of: " . implode( ', ', $this->valid_scales ) );
		}
		$this->map_params['scale'] = $scale;

		return $this;
	}

	/**
	 * Get current map scale
	 *
	 * @return int
	 */
	public function get_scale(): int {
		return $this->map_params['scale'];
	}

	/**
	 * Set language for map labels
	 *
	 * @param string $language Language code (e.g., 'en', 'es', 'fr')
	 *
	 * @return self
	 */
	public function set_language( string $language ): self {
		$this->map_params['language'] = $language;

		return $this;
	}

	/**
	 * Get current language setting
	 *
	 * @return string
	 */
	public function get_language(): string {
		return $this->map_params['language'];
	}

	/**
	 * Set region bias
	 *
	 * @param string $region Region code (e.g., 'US', 'GB')
	 *
	 * @return self
	 */
	public function set_region( string $region ): self {
		$this->map_params['region'] = $region;

		return $this;
	}

	/**
	 * Get current region setting
	 *
	 * @return string
	 */
	public function get_region(): string {
		return $this->map_params['region'];
	}

	/**
	 * Set heading for street view
	 *
	 * @param float $degrees Heading in degrees (0-360)
	 *
	 * @return self
	 * @throws InvalidArgumentException If heading is invalid
	 */
	public function set_heading( float $degrees ): self {
		if ( $degrees < 0 || $degrees > 360 ) {
			throw new InvalidArgumentException( "Invalid heading. Must be between 0 and 360 degrees." );
		}
		$this->map_params['heading'] = $degrees;

		return $this;
	}

	/**
	 * Get current heading
	 *
	 * @return float
	 */
	public function get_heading(): float {
		return $this->map_params['heading'];
	}

	/**
	 * Set pitch for street view
	 *
	 * @param float $degrees Pitch in degrees (-90 to 90)
	 *
	 * @return self
	 * @throws InvalidArgumentException If pitch is invalid
	 */
	public function set_pitch( float $degrees ): self {
		if ( $degrees < - 90 || $degrees > 90 ) {
			throw new InvalidArgumentException( "Invalid pitch. Must be between -90 and 90 degrees." );
		}
		$this->map_params['pitch'] = $degrees;

		return $this;
	}

	/**
	 * Get current pitch
	 *
	 * @return float
	 */
	public function get_pitch(): float {
		return $this->map_params['pitch'];
	}

	/**
	 * Set marker style parameters
	 *
	 * @param array $style Marker style options
	 *
	 * @return self
	 */
	public function set_marker_style( array $style ): self {
		$valid_styles = [ 'size', 'color', 'label', 'scale', 'anchor', 'icon' ];
		foreach ( $valid_styles as $key ) {
			if ( isset( $style[ $key ] ) ) {
				$this->marker_params[ $key ] = $style[ $key ];
			}
		}

		return $this;
	}

	/**
	 * Get current marker style parameters
	 *
	 * @return array
	 */
	public function get_marker_style(): array {
		return array_filter( $this->marker_params );
	}

	/**
	 * Set path style parameters
	 *
	 * @param array $style Path style options
	 *
	 * @return self
	 */
	public function set_path_style( array $style ): self {
		$valid_styles = [ 'weight', 'color', 'fillcolor', 'geodesic' ];
		foreach ( $valid_styles as $key ) {
			if ( isset( $style[ $key ] ) ) {
				$this->path_params[ $key ] = $style[ $key ];
			}
		}

		return $this;
	}

	/**
	 * Get current path style parameters
	 *
	 * @return array
	 */
	public function get_path_style(): array {
		return array_filter( $this->path_params );
	}

	/**
	 * Add path points
	 *
	 * @param array $points Array of coordinates or addresses
	 *
	 * @return self
	 */
	public function add_path_points( array $points ): self {
		$this->path_params['points'] = array_merge( $this->path_params['points'], $points );

		return $this;
	}

	/**
	 * Get current path points
	 *
	 * @return array
	 */
	public function get_path_points(): array {
		return $this->path_params['points'];
	}

	/**
	 * Add map style
	 *
	 * @param array $style Style configuration
	 *
	 * @return self
	 */
	public function add_style( array $style ): self {
		$this->style_params[] = $style;

		return $this;
	}

	/**
	 * Get all map styles
	 *
	 * @return array
	 */
	public function get_styles(): array {
		return $this->style_params;
	}

	/**
	 * Reset map parameters to defaults
	 *
	 * @return self
	 */
	public function reset_map_params(): self {
		$this->map_params = [
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
	 * Reset marker parameters
	 *
	 * @return self
	 */
	public function reset_marker_params(): self {
		$this->marker_params = [
			'size'   => '',
			'color'  => '',
			'label'  => '',
			'scale'  => '',
			'anchor' => '',
			'icon'   => ''
		];

		return $this;
	}

	/**
	 * Reset path parameters
	 *
	 * @return self
	 */
	public function reset_path_params(): self {
		$this->path_params = [
			'weight'    => '',
			'color'     => '',
			'fillcolor' => '',
			'geodesic'  => false,
			'points'    => []
		];

		return $this;
	}

	/**
	 * Reset style parameters
	 *
	 * @return self
	 */
	public function reset_style_params(): self {
		$this->style_params = [];

		return $this;
	}

	/**
	 * Reset all parameters
	 *
	 * @return self
	 */
	public function reset_all_params(): self {
		$this->reset_map_params();
		$this->reset_marker_params();
		$this->reset_path_params();
		$this->reset_style_params();

		return $this;
	}

	/**
	 * Get all parameters
	 *
	 * @return array
	 */
	public function get_all_params(): array {
		return [
			'map'    => $this->map_params,
			'marker' => $this->marker_params,
			'path'   => $this->path_params,
			'style'  => $this->style_params
		];
	}

}