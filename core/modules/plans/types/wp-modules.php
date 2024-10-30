<?php

use Membership\Utils\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$hide_wp_block = $level_type == 'woo_modules' ? 'd-none' : '';

?>
<div class="wp_modules <?php echo esc_attr( $hide_wp_block ); ?>">
<h2 class="mt-2 mb-0"><?php esc_html_e( 'Content Settings Section:', 'create-members' ); ?></h2>
	<p class="block-desc mb-2">
	<?php
	echo esc_html__( 'Protect access to posts, pages, and content sections. Read ', 'create-members' )
	. '<a href="' . esc_url( '' ) . '" target="_blank">' . esc_html__( 'Documentation', 'create-members' ) . '</a>'
	. ' ' . esc_html__( 'to protect content', 'create-members' )
	?>
	</p>
	<?php

		$args = array(
			'label'           => esc_html__( 'Show Pages', 'create-members' ),
			'id'              => 'wp_hide_pages',
			'type'            => 'random',
			'selected'        => $wp_hide_pages,
			'select_type'     => 'multiple',
			'condition_class' => '',
			'options'         => Helper::get_content_pages(),
			'disable'         => Helper::is_pro_active() ? false : true,
		);
		membership_select_field( $args );

		$args = array(
			'label'           => esc_html__( 'Post Categories', 'create-members' ),
			'id'              => 'post_categories',
			'type'            => 'random',
			'selected'        => $post_categories,
			'select_type'     => 'multiple',
			'condition_class' => '',
			'options'         => Helper::get_categories( 'category' ),
			'disable'         => Helper::is_pro_active() ? false : true,
		);
		membership_select_field( $args );

		$args = array(
			'label'           => esc_html__( 'Single Post', 'create-members' ),
			'id'              => 'single_post',
			'type'            => 'random',
			'selected'        => $single_post,
			'select_type'     => 'multiple',
			'condition_class' => '',
			'options'         => Helper::get_all_post( 'post' ),
			'disable'         => Helper::is_pro_active() ? false : true,
		);
		membership_select_field( $args );

		?>
</div>
