<?php
/**
 * Test editor functions.
 *
 * @package tsep
 */

class TestEditor extends WP_UnitTestCase {

	/**
	 * @var int
	 */
	private $post_id;

	public function setUp(): void {
		parent::setUp();
		update_option( 'tsep_post_types', [ 'post' ] );
		$this->post_id = self::factory()->post->create();
	}

	public function tearDown(): void {
		delete_option( 'tsep_post_types' );
		parent::tearDown();
	}

	/**
	 * Test tsep_get_url returns empty string when no meta is set.
	 */
	public function test_get_url_returns_empty_for_no_meta() {
		$this->assertSame( '', tsep_get_url( $this->post_id ) );
	}

	/**
	 * Test tsep_get_url returns URL when meta is set.
	 */
	public function test_get_url_returns_url_when_set() {
		update_post_meta( $this->post_id, '_external_permalink', 'https://example.com' );
		$this->assertSame( 'https://example.com', tsep_get_url( $this->post_id ) );
	}

	/**
	 * Test tsep_get_url returns empty for invalid post.
	 */
	public function test_get_url_returns_empty_for_invalid_post() {
		$this->assertSame( '', tsep_get_url( 999999 ) );
	}

	/**
	 * Test tsep_is_new_window returns false by default.
	 */
	public function test_is_new_window_default_false() {
		$this->assertFalse( tsep_is_new_window( $this->post_id ) );
	}

	/**
	 * Test tsep_is_new_window returns true when meta is set.
	 */
	public function test_is_new_window_true_when_set() {
		update_post_meta( $this->post_id, '_external_permalink_new', '1' );
		$this->assertTrue( tsep_is_new_window( $this->post_id ) );
	}

	/**
	 * Test display_post_states adds external link state.
	 */
	public function test_display_post_states_adds_external() {
		update_post_meta( $this->post_id, '_external_permalink', 'https://example.com' );
		$post   = get_post( $this->post_id );
		$states = apply_filters( 'display_post_states', [], $post );
		$this->assertArrayHasKey( 'external', $states );
		$this->assertSame( 'External Link', $states['external'] );
	}

	/**
	 * Test display_post_states does not add state for posts without external URL.
	 */
	public function test_display_post_states_skips_without_url() {
		$post   = get_post( $this->post_id );
		$states = apply_filters( 'display_post_states', [], $post );
		$this->assertArrayNotHasKey( 'external', $states );
	}
}
