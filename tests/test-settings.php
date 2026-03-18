<?php
/**
 * Test setting functions.
 *
 * @package tsep
 */

class TestSettings extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		delete_option( 'tsep_post_types' );
		delete_option( 'tsep_link_label' );
	}

	public function tearDown(): void {
		delete_option( 'tsep_post_types' );
		delete_option( 'tsep_link_label' );
		parent::tearDown();
	}

	/**
	 * Test tsep_post_types returns empty array by default.
	 */
	public function test_post_types_returns_empty_by_default() {
		$this->assertSame( [], tsep_post_types() );
	}

	/**
	 * Test tsep_post_types returns saved post types.
	 */
	public function test_post_types_returns_saved_values() {
		update_option( 'tsep_post_types', [ 'post', 'page' ] );
		$this->assertSame( [ 'post', 'page' ], tsep_post_types() );
	}

	/**
	 * Test tsep_is_active returns true for active post type.
	 */
	public function test_is_active_returns_true() {
		update_option( 'tsep_post_types', [ 'post' ] );
		$this->assertTrue( tsep_is_active( 'post' ) );
	}

	/**
	 * Test tsep_is_active returns false for inactive post type.
	 */
	public function test_is_active_returns_false() {
		update_option( 'tsep_post_types', [ 'post' ] );
		$this->assertFalse( tsep_is_active( 'page' ) );
	}

	/**
	 * Test tsep_is_active returns false when no option set.
	 */
	public function test_is_active_returns_false_when_empty() {
		$this->assertFalse( tsep_is_active( 'post' ) );
	}

	/**
	 * Test tsep_link_text returns default label.
	 */
	public function test_link_text_returns_default() {
		$text = tsep_link_text();
		$this->assertStringContainsString( '%link%', $text );
		$this->assertStringContainsString( '%rel%', $text );
	}

	/**
	 * Test tsep_link_text returns custom label when option is set.
	 */
	public function test_link_text_returns_custom() {
		$custom = 'Visit <a href="%link%">external site</a>';
		update_option( 'tsep_link_label', $custom );
		$this->assertSame( $custom, tsep_link_text() );
	}

	/**
	 * Test tsep_link_text with return_default flag always returns default.
	 */
	public function test_link_text_return_default_flag() {
		update_option( 'tsep_link_label', 'Custom label' );
		$default = tsep_link_text( true );
		$this->assertStringContainsString( '%link%', $default );
	}
}
