<?php
/**
 * Class Test Schema
 *
 * @package Custom_Item_Schema
 */

namespace Custom_Item_Schema;

/**
 * Test Schema
 */
class Test_Schema extends \WP_UnitTestCase {
	/**
	 * Test valid schema.
	 */
	public function test_valid_schema() {
		$post_id = $this->factory->post->create();
		$schema  = '{"@context":"http:\/\/schema.org\/","@type":"WebSite"}';

		// Save the schema.
		update_post_meta( $post_id, 'custom_item_schema', $schema );
		$this->go_to( get_permalink( $post_id ) );

		// Ensure the schema displays properly.
		$this->assertTrue( is_singular() );
		$this->assertContains( $schema, $this->render_schema() );
	}

	/**
	 * Test invalid schema.
	 */
	public function test_invalid_schema() {
		$post_id = $this->factory->post->create();
		$schema  = '{"@context":"';

		// Save the schema.
		update_post_meta( $post_id, 'custom_item_schema', $schema );
		$this->go_to( get_permalink( $post_id ) );

		// Ensure the schema displays properly.
		$this->assertTrue( is_singular() );
		$this->assertEmpty( $this->render_schema() );
	}

	/**
	 * Test term schema.
	 */
	public function test_term_schema() {
		$term_id = $this->factory->term->create( [
			'taxonomy' => 'category',
		] );

		$schema = '{"@context":"http:\/\/schema.org\/","@type":"WebSite"}';

		// Save the schema.
		update_term_meta( $term_id, 'custom_item_schema', $schema );
		$this->go_to( get_term_link( $term_id ) );

		// Ensure the schema displays properly.
		$this->assertTrue( is_category() && is_archive() );
		$this->assertContains( $schema, $this->render_schema() );
	}

	/**
	 * Test to ensure that Schema is empty by default.
	 */
	public function test_empty_schema() {
		$post_id = $this->factory->post->create();
		$this->go_to( get_permalink( $post_id ) );

		// Ensure the schema displays properly.
		$this->assertTrue( is_singular() );
		$this->assertEmpty( $this->render_schema() );
	}

	/**
	 * Test to ensure the schema displays on the homepage.
	 */
	public function test_homepage_schema() {
		// Test the default schema.
		update_option( 'custom_item_schema', '' );
		$this->go_to( home_url() );
		$this->assertTrue( is_home() );
		$this->assertEmpty( $this->render_schema() );

		// Test the schema once set.
		$schema = '{"@context":"http:\/\/schema.org\/","@type":"WebSite"}';
		update_option( 'custom_item_schema', $schema );
		$this->assertContains( $schema, $this->render_schema() );
	}

	/**
	 * Retrieve the contents of the `wp_head` action.
	 *
	 * @return string
	 */
	protected function render_schema() {
		ob_start();
		custom_item_schema();
		return ob_get_clean();
	}
}
