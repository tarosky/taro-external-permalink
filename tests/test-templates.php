<?php
/**
 * Test template functions.
 *
 * @package tsep
 */

class TestTemplates extends WP_UnitTestCase {

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
	 * Test tsep_target_attributes returns empty for non-new-window post.
	 */
	public function test_target_attributes_empty_without_new_window() {
		update_post_meta( $this->post_id, '_external_permalink', 'https://example.com' );
		$this->assertSame( '', tsep_target_attributes( $this->post_id ) );
	}

	/**
	 * Test tsep_target_attributes returns target and rel for new window.
	 */
	public function test_target_attributes_for_new_window() {
		update_post_meta( $this->post_id, '_external_permalink', 'https://example.com' );
		update_post_meta( $this->post_id, '_external_permalink_new', '1' );
		$result = tsep_target_attributes( $this->post_id );
		$this->assertStringContainsString( 'target="_blank"', $result );
		$this->assertStringContainsString( 'rel="noopener noreferrer"', $result );
	}

	/**
	 * Test tsep_target_attributes without rel.
	 */
	public function test_target_attributes_without_rel() {
		update_post_meta( $this->post_id, '_external_permalink', 'https://example.com' );
		update_post_meta( $this->post_id, '_external_permalink_new', '1' );
		$result = tsep_target_attributes( $this->post_id, false );
		$this->assertStringContainsString( 'target', $result );
		$this->assertStringNotContainsString( 'rel', $result );
	}

	/**
	 * Test tsep_target_attributes returns empty for inactive post type.
	 */
	public function test_target_attributes_empty_for_inactive_type() {
		$page_id = self::factory()->post->create( [ 'post_type' => 'page' ] );
		update_post_meta( $page_id, '_external_permalink_new', '1' );
		$this->assertSame( '', tsep_target_attributes( $page_id ) );
	}

	/**
	 * Test tsep_anchor_attributes returns href and target.
	 */
	public function test_anchor_attributes() {
		update_post_meta( $this->post_id, '_external_permalink', 'https://example.com' );
		update_post_meta( $this->post_id, '_external_permalink_new', '1' );
		$result = tsep_anchor_attributes( $this->post_id );
		$this->assertStringContainsString( 'href="https://example.com"', $result );
		$this->assertStringContainsString( 'target="_blank"', $result );
	}

	/**
	 * Test tsep_anchor_attributes returns empty for inactive post type.
	 */
	public function test_anchor_attributes_empty_for_inactive() {
		$page_id = self::factory()->post->create( [ 'post_type' => 'page' ] );
		$this->assertSame( '', tsep_anchor_attributes( $page_id ) );
	}

	/**
	 * Test tsep_url_store saves and retrieves URLs.
	 */
	public function test_url_store_saves_and_retrieves() {
		tsep_url_store( 'https://example.com', 'https://original.com' );
		$urls = tsep_url_store();
		$this->assertArrayHasKey( 'https://original.com', $urls );
		$this->assertSame( 'https://example.com', $urls['https://original.com'] );
	}

	/**
	 * Test tsep_post_link_filter returns external URL on frontend.
	 */
	public function test_post_link_filter_returns_external_url() {
		update_post_meta( $this->post_id, '_external_permalink', 'https://example.com' );
		set_current_screen( 'front' );
		$post   = get_post( $this->post_id );
		$result = tsep_post_link_filter( 'https://original.com', $post );
		// On the frontend, is_admin() is false so the filter replaces with external URL.
		$this->assertSame( 'https://example.com', $result );
	}

	/**
	 * Test tsep_post_link_filter preserves original URL when no external URL.
	 */
	public function test_post_link_filter_preserves_original() {
		$post   = get_post( $this->post_id );
		$result = tsep_post_link_filter( 'https://original.com', $post );
		$this->assertSame( 'https://original.com', $result );
	}
}
