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

## Extended Examples

### Saving Maps to Media Library

```php
// Generate a map URL
$map_url = $client->location( 'Seattle, WA', [
    'zoom' => 14,
    'size' => '800x600'
] );

// Save to media library with custom settings
$attachment_id = $client->save_to_media_library( $map_url, [
    'title'       => 'Seattle Downtown Map',
    'filename'    => 'seattle-downtown',
    'description' => 'Static map of downtown Seattle',
    'alt'         => 'Map showing downtown Seattle area',
    'folder'      => 'google-maps/seattle' // Custom subfolder within uploads
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

### Basic Location Map

```php
$client = new Client( 'your-api-key' );

// Basic location map
$map_url = $client->location( '47.6062,-122.3321' );

// Location with options
$map_url = $client->location( 'Pike Place Market, Seattle', [
    'zoom'     => 16,
    'size'     => '800x600',
    'maptype'  => 'roadmap',
    'language' => 'en',
    'region'   => 'US'
] );

// Generate image tag with custom attributes
$img_tag = $client->generate_image_tag( $map_url, [
    'class'  => 'my-map-image',
    'alt'    => 'Pike Place Market Map',
    'width'  => '800',
    'height' => '600'
] );
```

### Working with Markers

```php
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

$map_url = $client->markers( $markers, [
    'size' => '800x600',
    'zoom' => 14
] );
```

### Creating Paths

```php
// Simple path
$path_points = [ 'Seattle, WA', 'Tacoma, WA', 'Olympia, WA' ];

// Path with styling
$map_url = $client->path( $path_points, [
    'path_style' => [
        'weight'   => '5',
        'color'    => 'blue',
        'geodesic' => 'true'
    ],
    'size'       => '800x400'
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

$map_url = $client->styled( $styles, [
    'center' => 'Seattle, WA',
    'zoom'   => 12,
    'size'   => '800x600'
] );
```

### Street View Configuration

```php
// Configure street view parameters
$client->set_heading( 180 )  // Face south
      ->set_pitch( 20 )     // Look slightly upward
      ->set_zoom( 1 );      // Close-up view

$map_url = $client->location( 'Space Needle, Seattle' );
```

## API Methods

### Main Methods

* `location( $location, $options = [] )`: Generate URL for a specific location
* `save_to_media_library( $url, $args = [] )`: Save static map to WordPress media library
* `markers( $markers, $options = [] )`: Generate URL with custom markers
* `path( $path_points, $options = [] )`: Generate URL with a path
* `styled( $styles, $options = [] )`: Generate URL with custom styling
* `generate_image_tag( $url, $attrs = [] )`: Generate complete img HTML tag
* `validate_api_key()`: Verify if the API key is valid

### Configuration Methods

* `set_size( $width, $height )`: Set map dimensions
* `set_zoom( $zoom )`: Set zoom level (0-21)
* `set_map_type( $type )`: Set map type
* `set_format( $format )`: Set image format
* `set_scale( $scale )`: Set map scale
* `set_language( $language )`: Set map labels language
* `set_region( $region )`: Set map region bias
* `set_heading( $degrees )`: Set street view heading (0-360)
* `set_pitch( $degrees )`: Set street view pitch (-90 to 90)
* `reset_options()`: Reset all options to defaults

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