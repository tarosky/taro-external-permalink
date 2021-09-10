<?php
/**
 * Editor hooks.
 *
 * @package tsep
 */

/**
 * Get external permalink.
 *
 * @param null|int|WP_Post $post Post object.
 * @return string
 */
function tsep_get_url( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	return (string) get_post_meta( $post->ID, '_external_permalink', true );
}

/**
 * Get external permalink.
 *
 * @param null|int|WP_Post $post Post object.
 * @return bool
 */
function tsep_is_new_window( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}
	return (bool) get_post_meta( $post->ID, '_external_permalink_new', true );
}

/**
 * Register meta box.
 */
add_action( 'add_meta_boxes', function( $post_type ) {
	if ( ! tsep_is_active( $post_type ) ) {
		return;
	}
	add_meta_box( 'tsep-meta-box', __( 'External Permalink', 'tsep' ), function( WP_Post $post ) {
		wp_enqueue_media();
		wp_enqueue_script( 'tsep-media-selector', tsep_url() . '/dist/js/media-selector.js', [ 'jquery' ], tsep_version(), true );
		wp_nonce_field( 'tsep_save_post', '_tsepnonce' );
		?>
		<p class="description">
			<?php esc_html_e( 'If external permalink is set, the url of this post will be replaced.', 'tsep' ); ?>
		</p>
		<p>
			<label>
				URL<br />
				<input class="widefat" type="url" name="external-permalink" value="<?php echo esc_url( tsep_get_url( $post ) ); ?>" placeholder="e.g. https://example.com" />
			</label>
		</p>
		<p>
			<button class="button is-small" id="tsep-media-chooser"><?php esc_html_e( 'Open media library', 'tsep' ); ?></button>
		</p>
		<p>
			<label>
				<input type="checkbox" value="1" name="external-permalink-new" <?php checked( tsep_is_new_window( $post ) ); ?> />
				<?php esc_html_e( 'Open in new window', 'tsep' ); ?>
			</label>
		</p>
		<?php
	}, $post_type, 'side', 'low' );
} );

/**
 * Save meta box.
 *
 * @param int     $post_id Post id.
 * @param WP_Post $post    Post object.
 */
add_action( 'save_post', function( $post_id, $post ) {
	if ( ! tsep_is_active( $post->post_type ) ) {
		return;
	}
	if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_tsepnonce' ), 'tsep_save_post' ) ) {
		return;
	}
	// Save meta data.
	update_post_meta( $post_id, '_external_permalink', filter_input( INPUT_POST, 'external-permalink' ) );
	update_post_meta( $post_id, '_external_permalink_new', (string) filter_input( INPUT_POST, 'external-permalink-new' ) );
}, 10, 2 );

/**
 * Add post states.
 *
 * @param string[] $states States.
 * @param WP_Post  $post   Post object.
 * @return string[]
 */
add_filter( 'display_post_states', function( $states, $post ) {
	if ( tsep_is_active( $post->post_type ) && tsep_get_url( $post ) ) {
		$states['external'] = __( 'External Link', 'tsep' );
	}
	return $states;
}, 10, 2 );
