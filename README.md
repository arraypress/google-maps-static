# Google Maps Static API for WordPress

A PHP library for integrating with the Google Maps Static API in WordPress, providing easy-to-use methods for generating
static map images with various features including markers, paths, styled maps, and custom locations. Features WordPress
integration and `WP_Error` support.

## Features

- 🗺️ **Multiple Map Types**: Support for roadmap, satellite, terrain, and hybrid views
- 📍 **Marker Integration**: Add custom markers with various styles and positions
- 🛣️ **Path Drawing**: Create routes and shapes with customizable paths
- 🎨 **Custom Styling**: Comprehensive map styling options
- ⚡ **WordPress Integration**: Native WP_Error support and URL escaping
- 🛡️ **Type Safety**: Full type hinting and strict types
- 📐 **Flexible Sizing**: Custom dimensions and scale options
- 🌍 **Global Support**: Works with locations worldwide, with language and region settings
- 🎯 **Multiple Formats**: Support for PNG, JPG, and GIF outputs
- 📱 **Responsive**: Support for different scale factors
- ✨ **Easy Implementation**: Simple, chainable API methods
- 💾 **Media Library Integration**: Save maps directly to WordPress media library
- 🎥 **Street View Support**: Configure heading and pitch for street view perspectives
- 🔄 **Method Chaining**: Fluent interface for setting options
- ⚙️ **Validation**: Built-in parameter validation for all setters

## Requirements

- PHP 7.4 or later
- WordPress 5.0 or later
- Google Maps Static API key

## Installation

Install via Composer:

```bash
composer require arraypress/google-maps-static
```

## Basic Usage

```php
use ArrayPress\Google\MapsStatic\Client;

// Initialize client with your API key
$client = new Client( 'your-google-api-key' );

// Configure options using method chaining
$client
	->set_size( 800, 600 )
	->set_zoom( 15 )
	->set_map_type( 'roadmap' )
	->set_format( 'png' )
	->set_scale( 2 )
	->set_language( 'en' )
	->set_region( 'US' );

// Generate a basic map for a location
$map_url = $client->location( 'Seattle, WA' );
$img_tag = $client->generate_image_tag( $map_url );

// Create a map with markers
$markers = [
	[
		'style'     => [ 'color' => 'red', 'label' => 'A' ],
		'locations' => [ 'Seattle, WA' ]
	]
];
$map_url = $client->markers( $markers );

// Generate a map with a custom path
$path_points = [ 'Seattle, WA', 'Portland, OR' ];
$map_url     = $client->path( $path_points );
```

## Configuration Methods

### Setting Options

```php
// Map dimensions and display
$client->set_size( 800, 600 );         // Set width and height
$client->set_zoom( 15 );               // Set zoom level (0-21)
$client->set_map_type( 'satellite' );  // roadmap, satellite, terrain, hybrid
$client->set_format( 'png' );          // png, jpg, gif
$client->set_scale( 2 );               // 1, 2, or 4

// Localization
$client->set_language( 'en' );         // Language code
$client->set_region( 'US' );           // Region code

// Street view settings
$client->set_heading( 90 );            // 0-360 degrees
$client->set_pitch( - 30 );            // -90 to 90 degrees

// API management
$client->set_api_key( 'new-key' );     // Update API key
$client->reset_options();              // Reset to defaults
```

### Getting Options

```php
// Map configuration
$dimensions = $client->get_size();      // Returns ['width' => int, 'height' => int]
$zoom       = $client->get_zoom();      // Current zoom level
$type       = $client->get_map_type();  // Current map type
$format     = $client->get_format();    // Current image format
$scale      = $client->get_scale();     // Current scale factor

// Localization settings
$language = $client->get_language();    // Current language code
$region   = $client->get_region();      // Current region code

// Street view settings
$heading = $client->get_heading();      // Current heading in degrees
$pitch   = $client->get_pitch();        // Current pitch in degrees

// API and options
$key     = $client->get_api_key();      // Current API key
$options = $client->get_options();      // All current options
```

## Extended Examples

### Saving Maps to Media Library

```php
// Configure and generate map URL
$map_url = $client
	->set_size( 800, 600 )
	->set_zoom( 14 )
	->set_map_type( 'roadmap' )
	->location( 'Seattle, WA' );

// Save to media library with custom settings
$attachment_id = $client->save_to_media_library( $map_url, [
	'title'       => 'Seattle Downtown Map',
	'filename'    => 'seattle-downtown',
	'description' => 'Static map of downtown Seattle',
	'alt'         => 'Map showing downtown Seattle area',
	'folder'      => 'google-maps/seattle'
] );

if ( ! is_wp_error( $attachment_id ) ) {
	// Get the attachment URL
	$image_url = wp_get_attachment_url( $attachment_id );

	// Use WordPress image functions
	echo wp_get_attachment_image( $attachment_id, 'full', false, [
		'class'   => 'my-map-image',
		'loading' => 'lazy'
	] );
}
```

### Working with Markers

```php
$client
	->set_size( 800, 600 )
	->set_zoom( 14 )
	->set_map_type( 'roadmap' );

// Single marker
$markers = [
	[
		'style'     => [
			'color' => 'red',
			'size'  => 'mid',
			'label' => 'A'
		],
		'locations' => [ 'Seattle, WA' ]
	]
];

// Multiple markers with different styles
$markers = [
	[
		'style'     => [ 'color' => 'blue', 'label' => 'S' ],
		'locations' => [ 'Space Needle, Seattle' ]
	],
	[
		'style'     => [ 'color' => 'green', 'label' => 'P' ],
		'locations' => [ 'Pike Place Market, Seattle' ]
	]
];

$map_url = $client->markers( $markers );
```

### Creating Paths

```php
$client
	->set_size( 800, 400 )
	->set_map_type( 'roadmap' );

// Simple path
$path_points = [ 'Seattle, WA', 'Tacoma, WA', 'Olympia, WA' ];

// Path with styling
$map_url = $client->path( $path_points, [
	'path_style' => [
		'weight'   => '5',
		'color'    => 'blue',
		'geodesic' => 'true'
	]
] );
```

### Styled Maps

```php
// Custom map styling
$styles = [
	[
		'feature' => 'water',
		'element' => 'geometry',
		'rules'   => [
			'color' => '0x2c4d58'
		]
	],
	[
		'feature' => 'landscape',
		'rules'   => [
			'color' => '0xeaead9'
		]
	]
];

$map_url = $client
	->set_size( 800, 600 )
	->set_zoom( 12 )
	->styled( $styles, [ 'center' => 'Seattle, WA' ] );
```

### Street View Configuration

```php
// Configure street view parameters
$map_url = $client
	->set_heading( 180 )    // Face south
	->set_pitch( 20 )      // Look slightly upward
	->set_zoom( 1 )        // Close-up view
	->location( 'Space Needle, Seattle' );
```

## API Methods

### Main Methods
* `location( $location, $options = [] )`: Generate URL for a specific location
* `markers( $markers, $options = [] )`: Generate URL with custom markers
* `path( $path_points, $options = [] )`: Generate URL with a path
* `styled( $styles, $options = [] )`: Generate URL with custom styling
* `save_to_media_library( $url, $args = [] )`: Save static map to WordPress media library
* `generate_image_tag( $url, $attrs = [] )`: Generate complete img HTML tag
* `validate_api_key()`: Verify if the API key is valid

### Setter Methods
* `set_size( $width, $height)`: Set map dimensions
* `set_zoom( $level)`: Set zoom level (0-21)
* `set_map_type( $type)`: Set map type
* `set_format( $format)`: Set image format
* `set_scale( $scale)`: Set map scale
* `set_language( $language)`: Set map labels language
* `set_region( $region)`: Set map region bias
* `set_heading( $degrees)`: Set street view heading (0-360)
* `set_pitch( $degrees)`: Set street view pitch (-90 to 90)
* `set_api_key( $api_key)`: Set new API key

### Getter Methods
* `get_size()`: Get current dimensions
* `get_zoom()`: Get current zoom level
* `get_map_type()`: Get current map type
* `get_format()`: Get current image format
* `get_scale()`: Get current scale value
* `get_language()`: Get current language
* `get_region()`: Get current region
* `get_heading()`: Get current heading
* `get_pitch()`: Get current pitch
* `get_api_key()`: Get current API key
* `get_options()`: Get all current options

### Options Parameters

#### Common Options
* `size`: Map dimensions (required)
* `zoom`: Zoom level (0-21)
* `maptype`: Map type (roadmap, satellite, terrain, hybrid)
* `format`: Image format (png, jpg, gif)
* `scale`: Image scale (1, 2, 4)
* `language`: Map labels language (e.g., 'en', 'es', 'fr')
* `region`: Region bias (e.g., 'US', 'GB')
* `heading`: Street view heading in degrees (0-360)
* `pitch`: Street view pitch in degrees (-90 to 90)

#### Marker Options
* `color`: Marker color
* `size`: Marker size (tiny, mid, small)
* `label`: Single character label
* `icon`: Custom icon URL
* `scale`: Marker scale

#### Path Options
* `weight`: Line weight
* `color`: Line color
* `geodesic`: Follow earth's curvature
* `fillcolor`: Fill color for closed paths

## Use Cases

* **Business Listings**: Display store locations
* **Real Estate**: Show property locations
* **Event Maps**: Display venue locations
* **Travel Routes**: Visualize travel paths
* **Location Markers**: Highlight multiple points
* **Custom Territory**: Display service areas
* **Geographic Data**: Visualize data points
* **Styled Maps**: Brand-specific map designs
* **Direction Overview**: Show route overview
* **Location Context**: Add visual location context
* **Street Level Views**: Show building facades and street perspectives

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/google-maps-static)
- [Issue Tracker](https://github.com/arraypress/google-maps-static/issues)