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
// In automatic mode, replace all link.
//
if ( 'automatic' === get_option( 'tsep_render_type' ) ) {

	// Start output buffer.
	add_action( 'template_redirect', function() {
		ob_start();
	}, 9999 );

	// Replace all link tags.
	add_action( 'wp_footer', function() {
		$content = ob_get_contents();
		ob_end_clean();
		// Store
		foreach ( tsep_url_store() as $url ) {
			$content = preg_replace_callback( '#href=([\'"])([^\'"]+)([\'"])#u', function( $matches ) use ( $url ) {
				list( $href, $quote1, $link, $quote2 ) = $matches;
				if ( $link !== $url ) {
					return $href;
				} else {
					return sprintf( 'href=%1$s%2$s%1$s target=%3$s_blank%3$s rel=%3$snoopener noreferrers%3$s', $quote1, $link, $quote2 );
				}
			}, $content );
		}
		echo $content;
	}, 9999 );
}
