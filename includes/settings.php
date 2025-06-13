<?php
/**
 * Setting screen.
 *
 * @package tsep
 */

/**
 * Post types to have external link.
 *
 * @return string[]
 */
function tsep_post_types() {
	if ( defined( 'EXTERNAL_PERMALINK_POST_TYPES' ) ) {
		return array_values( array_filter( array_map( 'trim', explode( ',', EXTERNAL_PERMALINK_POST_TYPES ) ), function ( $post_type ) {
			return post_type_exists( $post_type );
		} ) );
	}
	return (array) get_option( 'tsep_post_types', [] );
}

/**
 * Is post type can have external link?
 *
 * @param string $post_type Post type.
 * @return bool
 */
function tsep_is_active( $post_type ) {
	return in_array( $post_type, tsep_post_types(), true );
}

/**
 * Get default link.
 *
 * @param bool $return_default If true, always return default.
 * @return string
 */
function tsep_link_text( $return_default = false ) {
	$default_label = __( 'For more details please refer to <a href="%link%"%rel%>here</a>.', 'tsep' );
	if ( $return_default ) {
		return $default_label;
	}
	return get_option( 'tsep_link_label', $default_label );
}

/**
 * Register settings.
 */
add_action( 'admin_init', function () {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}
	// Section.
	add_settings_section( 'tsep-setting', __( 'External Permalink', 'tsep' ), function () {
		printf(
			'<p id="tsep-setting" class="description">%s</p>',
			esc_html__( 'This section determine which post type can have external permalink.', 'tsep' )
		);
	}, 'writing' );
	// Add fields.
	add_settings_field( 'tsep_post_types', __( 'Post Types', 'tsep' ), function () {
		$post_types = array_values( array_filter( get_post_types( [ 'public' => true ], OBJECT ), function ( WP_Post_Type $post_type ) {
			return 'attachment' !== $post_type;
		} ) );
		foreach ( $post_types as $post_type ) {
			printf(
				'<label style="display: inline-block; margin: 0 10px 10px 0;"><input type="checkbox" name="tsep_post_types[]" value="%s" %s/>%s</label>',
				esc_attr( $post_type->name ),
				checked( tsep_is_active( $post_type->name ), true, false ),
				esc_html( $post_type->label )
			);
		}
		if ( defined( 'EXTERNAL_PERMALINK_POST_TYPES' ) ) :
			?>
				<p class="description">
					<?php
					// translators: %s is constant name.
					printf( esc_html__( 'Constant %s is defined, so post types will be programmatically set. You cannot change them here.', 'tsep' ), '<code>EXTERNAL_PERMALINK_POST_TYPES</code>' );
					?>
				</p>
			<?php
		endif;
	}, 'writing', 'tsep-setting' );
	// Register.
	register_setting( 'writing', 'tsep_post_types' );
	// Add fields.
	add_settings_field( 'tsep_render_type', __( 'Attributes', 'tsep' ), function () {
		$options = [
			'automatic' => __( 'Automatic', 'tsep' ),
			'manual'    => __( 'Manual', 'tsep' ),
		];
		?>
		<select name="tsep_render_type">
			<?php
			foreach ( $options as $value => $label ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( $value, get_option( 'tsep_render_type', '' ), false ),
					esc_html( $label )
				);
			}
			?>
		</select>
		<?php
		printf(
			'<p class="description">%s</p>',
			sprintf(
				// translators: %s is function.
				esc_html__( 'When using Manual mode, use %1$s inside an anchor element to generate href, rel and target attributes. Or use %2$s to populate the href attribute and %3$s to add the target and rel attributes separately.', 'tsep' ),
				'<code>tsep_anchor_attributes()</code>',
				'<code>the_permalink()</code>',
				'<code>tsep_target_attributes()</code>'
			)
		);
	}, 'writing', 'tsep-setting' );
	// Register.
	register_setting( 'writing', 'tsep_render_type' );
	// Add fields.
	add_settings_field( 'tsep_link_label', __( 'Single Page Content', 'tsep' ), function () {
		// translators: %s is lURL.
		$placeholder = __( 'e.g. ', 'tsep' ) . tsep_link_text( true );
		?>
		<input type="text" name="tsep_link_label" value="<?php echo esc_attr( get_option( 'tsep_link_label', '' ) ); ?>"
			class="widefat" placeholder="<?php echo esc_attr( $placeholder ); ?>"/>
		<?php
		printf(
			'<p class="description">%s</p>',
			esc_html__( 'On a single post page, this will be added to the post\'s content. %link% will be replaced with the external link, and %rel% with the target and rel attributes.', 'tsep' )
		);
	}, 'writing', 'tsep-setting' );
	// Register.
	register_setting( 'writing', 'tsep_link_label' );
} );

/**
 * Display notices if no settings.
 */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$post_types = array_filter( tsep_post_types(), function ( $post_type ) {
		return post_type_exists( $post_type );
	} );
	if ( ! empty( $post_types ) ) {
		return;
	}
	// No post type selected, so display notice.
	?>
	<div class="error">
		<p>
			<strong>Taro External Permalink</strong><br />
			<?php
			echo wp_kses_post( sprintf(
				// translators: %s is URL.
				__( 'No post type is selected. Please choose post types to have external permalink at <a href="%s">Setting Page</a>', 'tsep' ),
				esc_url( admin_url( 'options-writing.php#tsep-setting' ) )
			) );
			?>
		</p>
	</div>
	<?php
} );
