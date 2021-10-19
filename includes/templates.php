<?php
/**
 * Add template hooks and functiosn.
 *
 * @package tsep
 */

/**
 * Get rel attributes.
 *
 * @param null|int|WP_Post $post Post object.
 * @param bool             $rel  If true, add rel attibutes.
 *
 * @return string
 */
function tsep_target_attributes( $post = null, $rel = true, $quote = '"' ) {
	$post = get_post( $post );
	if ( ! $post || ! tsep_is_active( $post->post_type ) ) {
		return '';
	}
	if ( ! tsep_is_new_window( $post ) ) {
		return '';
	}
	$attributes = [
		'target' => '_blank',
	];
	if ( $rel ) {
		$attributes['rel'] = 'noopner noreferrer';
	}
	$html = [];
	foreach ( $attributes as $attr => $value ) {
		$html[] = sprintf( '%1$s=%3$s%2$s%3$s', $attr, esc_attr( $value ), $quote );
	}
	return implode( ' ', $attributes );
}

/**
 * Save permlinks globally.
 *
 * @param string $save URL to save if set.
 * @return string[]
 */
function tsep_url_store( $save = '' ) {
	static $urls = [];
	if ( $save && ! in_array( $save, $urls, true ) ) {
		$urls[] = $save;
	}
	return $urls;
}

/**
 * Filter permalink.
 *
 * @param string  $permalink URL.
 * @param WP_Post $post      Post object.
 * @return string
 */
add_filter( 'post_link', function ( $permalink, $post ) {
	if ( ! is_admin() && tsep_is_active( $post->post_type ) ) {
		$url = tsep_get_url( $post );
		if ( $url ) {
			$permalink = $url;
			if ( tsep_is_new_window( $post ) ) {
				tsep_url_store( $permalink );
			}
		}
	}
	return $permalink;
}, 10, 2 );

/**
 * If permalink is changes, add rel.
 */
add_filter( 'the_permalink', function ( $link, $post ) {
	$post = get_post( $post );
	if ( ! $post || ! tsep_is_active( $post->post_type ) ) {
		return $link;
	}
	if ( ! tsep_is_new_window( $post ) ) {
		return $link;
	}
	// Depends on render type.
	$render_type = get_option( 'tsep_render_type', '' );
	switch ( $render_type ) {
		case 'single-quote':
			$quote = "'";
			break;
		case 'double-quote':
			$quote = '"';
			break;
		default:
			return $link;
	}
	$attr = rtrim( tsep_target_attributes( $post, true, $quote ), $quote );
	return $link . $quote . ' ' . $attr;
}, 10, 2);

//
// In automatic mode, Add helper script.
//
add_action( 'wp_footer', function() {
	if ( 'automatic' !== get_option( 'tsep_render_type' ) ) {
		// Only on automatic mode.
		return;
	}
	$urls = tsep_url_store();
	if ( empty( $urls) ) {
		// No URLs to be replaced.
		return;
	}
	// Load JavaScript helper.
	wp_enqueue_script( 'tsep-replace-rel', tsep_url() . '/dist/js/replace-rel.js', [], tsep_version(), true );
	$js = <<<'JS'
(function(){
	window.tsepUrls = %s;
})();
JS;
	$js = sprintf( $js, json_encode( $urls ) );
	wp_add_inline_script( 'tsep-replace-rel', $js, 'before' );
}, 9 );
