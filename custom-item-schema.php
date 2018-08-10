<?php
/**
 * Plugin Name:     Custom Item Schema
 * Plugin URI:      https://alley.co/
 * Description:     Custom Schema.org structured data for posts and taxonomies.
 * Author:          Alley Interactive
 * Author URI:      https://alley.co/
 * Text Domain:     custom-item-schema
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Custom_Item_Schema
 */

namespace Custom_Item_Schema;
define( 'Custom_Item_Schema\PATH', __DIR__ );

/**
 * Setup the plugin.
 */
function setup() {
	// Load required files.
	require_once PATH . '/inc/class-fieldmanager-schema-editor.php';

	$post_types = get_post_types();
	foreach ( $post_types as $post_type ) {
		add_action( "fm_post_{$post_type}", __NAMESPACE__ . '\add_editor_meta_box' );
	}

	$taxonomies = get_taxonomies();
	foreach ( $taxonomies as $taxonomy ) {
		add_action( "fm_term_{$taxonomy}", __NAMESPACE__ . '\add_editor_meta_box' );
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\setup' );

/**
 * Add a schema-editing meta box.
 *
 * @param string $type The Fieldmanager type.
 */
function add_editor_meta_box( $type ) {
	list( $context ) = fm_calculate_context();

	$suggested_formatting_tags = [
		'#site_name#',
		'#site_description#',
	];

	// Hand-pick the formatting tags that make sense to suggest in this context.
	if ( 'post' === $context ) {
		$suggested_formatting_tags = array_merge(
			$suggested_formatting_tags,
			[
				'#title#',
				'#excerpt#',
				'#date_published#',
				'#date_modified#',
				'#author#',
				'#categories#',
				'#tags#',
				'#thumbnail_url#',
			]
		);
	}

	$options = [
		'name' => 'custom_item_schema',
	];

	if ( $suggested_formatting_tags ) {
		$options['description'] = sprintf(
			// Italics look funny with all these <code> tags.
			/* translators: %s: the available formatting tags. */
			'<p style="font-style: normal;">' . __( 'You can use WP SEO formatting tags here: %s', 'custom-item-schema' ) . '</p>',
			implode(
				' ',
				array_map(
					function ( $tag ) {
						return '<code>' . $tag . '</code>';
					},
					$suggested_formatting_tags
				)
			)
		);
		$options['escape']['description'] = 'wp_kses_post';
	}

	$fm = new \Custom_Item_Schema\Fieldmanager_Schema_Editor( '', $options );

	if ( 'post' === $context ) {
		$fm->add_meta_box( __( 'Schema Editor', 'custom-item-schema' ), [ $type ] );
	} elseif ( 'term' === $context ) {
		$fm->add_term_meta_box( __( 'Schema Editor', 'custom-item-schema' ), [ $type ] );
	}
}

/**
 * Retrieve enabled post types for the plugin.
 *
 * @return array
 */
function get_post_types() : array {
	$post_types = \get_post_types( [
		'public' => true,
	], 'names' );

	/**
	 * Retrieve the available post types to apply custom Schema to.
	 *
	 * @param array $post_types Post types to include, defaults to all public post types.
	 * @return array
	 */
	return (array) apply_filters( 'custom_item_schema_post_types', array_keys( $post_types ) );
}

/**
 * Retrieve enabled taxonomies for the plugin.
 *
 * @return array
 */
function get_taxonomies() : array {
	$taxonomies = \get_taxonomies( [
		'public' => true,
	] );

	/**
	 * Retrieve the available taxonomies to apply custom Schema to.
	 *
	 * @param array $taxonomies Taxonomies to include.
	 * @return array
	 */
	return (array) apply_filters( 'custom_item_schema_taxonomies', array_keys( $taxonomies ) );
}

/**
 * Print custom schema saved to the current post or term.
 */
function custom_item_schema() {
	$post_types = get_post_types();
	$taxonomies = get_taxonomies();

	if ( ! empty( $post_types ) && is_singular( $post_types ) ) {
		$schema = (string) get_post_meta( get_the_ID(), 'custom_item_schema', true );
	} elseif ( is_tag() || is_category() ) {
		$schema = (string) get_term_meta( get_queried_object_id(), 'custom_item_schema', true );
	}

	if ( ! empty( $schema ) ) {
		the_item_schema( $schema );
	}
}
add_action( 'wp_head', __NAMESPACE__ . '\custom_item_schema' );

/**
 * Template helper to print custom item schema.
 *
 * @param string $json The schema JSON.
 */
function the_item_schema( string $json ) {
	if ( function_exists( 'wp_seo' ) ) {
		$json = wp_seo()->format( $json );
	}

	$decoded = json_decode( $json );

	if ( JSON_ERROR_NONE !== json_last_error() ) {
		return;
	}

	printf(
		"\n" . '
<!-- Custom item schema. -->
<script type="application/ld+json">%s</script>
<!-- End custom item schema. -->
		'
		. "\n",
		wp_json_encode( $decoded )
	);
}
