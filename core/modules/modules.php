<?php

	defined( 'ABSPATH' ) || exit;

	use Membership\Utils\Helper;
?>
<div class="section-wrap w-50">
	<h1 class="main-header"><?php esc_html_e( 'Setup Unlimited Membership to Increase your business revenue', 'create-members' ); ?></h1>
	<p><?php esc_html_e( 'Restrict content, manage member subscriptions, Offer discount to Capture and keep users engaged with your business.', 'create-members' ); ?></p>
</div>
<?php
extract( Helper::get_modules() );

$more_products = array(
	array(
		'icon'          => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 48 48" width="64px" height="64px"><path fill="#a64a7b" d="M43,11H5c-2.209,0-4,1.791-4,4v16c0,2.209,1.791,4,4,4h19l8,4l-2-4h13c2.209,0,4-1.791,4-4V15 C47,12.791,45.209,11,43,11z"/><path fill="#fff" d="M40.443 19c.041 0 .132.005.277.038.342.077.559.198.82.686C41.85 20.283 42 21.007 42 21.939c0 1.398-.317 2.639-.973 3.802C40.321 27 39.805 27 39.557 27c-.041 0-.132-.005-.277-.038-.342-.077-.559-.198-.809-.666C38.158 25.722 38 24.963 38 24.043c0-1.399.314-2.63.963-3.765C39.691 19 40.218 19 40.443 19M40.443 16c-1.67 0-3.026.931-4.087 2.793C35.452 20.375 35 22.125 35 24.043c0 1.434.278 2.662.835 3.686.626 1.173 1.548 1.88 2.783 2.16C38.948 29.963 39.261 30 39.557 30c1.687 0 3.043-.931 4.087-2.793C44.548 25.606 45 23.856 45 21.939c0-1.452-.278-2.662-.835-3.668-.626-1.173-1.548-1.88-2.783-2.16C41.052 16.037 40.739 16 40.443 16L40.443 16zM28.443 19c.041 0 .132.005.268.036.333.076.571.207.829.689C29.85 20.283 30 21.007 30 21.939c0 1.398-.317 2.639-.973 3.802C28.321 27 27.805 27 27.557 27c-.041 0-.132-.005-.277-.038-.342-.077-.559-.198-.809-.666C26.158 25.722 26 24.963 26 24.043c0-1.399.314-2.63.963-3.765C27.691 19 28.218 19 28.443 19M28.443 16c-1.67 0-3.026.931-4.087 2.793C23.452 20.375 23 22.125 23 24.043c0 1.434.278 2.662.835 3.686.626 1.173 1.548 1.88 2.783 2.16C26.948 29.963 27.261 30 27.557 30c1.687 0 3.043-.931 4.087-2.793C32.548 25.606 33 23.856 33 21.939c0-1.452-.278-2.662-.835-3.668-.626-1.173-1.565-1.88-2.783-2.16C29.052 16.037 28.739 16 28.443 16L28.443 16zM18.5 32c-.421 0-.832-.178-1.123-.505-2.196-2.479-3.545-5.735-4.34-8.343-1.144 2.42-2.688 5.515-4.251 8.119-.309.515-.894.792-1.491.715-.596-.083-1.085-.513-1.242-1.093-2.212-8.127-3.007-13.95-3.039-14.194-.11-.82.466-1.575 1.286-1.686.831-.108 1.576.465 1.687 1.286.007.049.571 4.177 2.033 10.199 2.218-4.208 4.078-8.535 4.102-8.59.267-.62.919-.989 1.58-.895.668.09 1.194.615 1.285 1.283.007.052.542 3.825 2.245 7.451.719-7.166 2.873-10.839 2.982-11.021.427-.711 1.35-.941 2.058-.515.711.426.941 1.348.515 2.058C22.762 16.313 20 21.115 20 30.5c0 .623-.386 1.182-.968 1.402C18.858 31.968 18.679 32 18.5 32z"/></svg>',
		'title'         => esc_html__( 'WooCommerce Membership', 'create-members' ),
		'desc'          => esc_html__( 'Offer discount to Capture and keep users engaged with your business.', 'create-members' ),
		'go_to_pro'     => 'https://woooplugin.com/ultimate-membership/',
		'go_to_pro_txt' => esc_html__( 'Go to Pro', 'create-members' ),
		'note'          => esc_html__( 'Note: Please Active WooCommerce to work properly', 'create-members' ),
		'id'            => 'woo_modules',
		'value'         => $woo_modules,
		'free'          => true,
	),
	array(
		'icon'          => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 48 48" width="64px" height="64px"><path fill="#fff" d="M24 4.050000000000001A19.95 19.95 0 1 0 24 43.95A19.95 19.95 0 1 0 24 4.050000000000001Z"/><path fill="#01579b" d="M8.001,24c0,6.336,3.68,11.806,9.018,14.4L9.385,17.488C8.498,19.479,8.001,21.676,8.001,24z M34.804,23.194c0-1.977-1.063-3.35-1.67-4.412c-0.813-1.329-1.576-2.437-1.576-3.752c0-1.465,1.471-2.84,3.041-2.84 c0.071,0,0.135,0.006,0.206,0.008C31.961,9.584,28.168,8,24.001,8c-5.389,0-10.153,2.666-13.052,6.749 c0.228,0.074,0.307,0.039,0.611,0.039c1.669,0,4.264-0.2,4.264-0.2c0.86-0.057,0.965,1.212,0.099,1.316c0,0-0.864,0.105-1.828,0.152 l5.931,17.778l3.5-10.501l-2.603-7.248c-0.861-0.046-1.679-0.152-1.679-0.152c-0.862-0.056-0.762-1.375,0.098-1.316 c0,0,2.648,0.2,4.217,0.2c1.675,0,4.264-0.2,4.264-0.2c0.861-0.057,0.965,1.212,0.104,1.316c0,0-0.87,0.105-1.832,0.152l5.891,17.61 l1.599-5.326C34.399,26.289,34.804,24.569,34.804,23.194z M24.281,25.396l-4.8,13.952c1.436,0.426,2.95,0.652,4.52,0.652 c1.861,0,3.649-0.324,5.316-0.907c-0.04-0.071-0.085-0.143-0.118-0.22L24.281,25.396z M38.043,16.318 c0.071,0.51,0.108,1.059,0.108,1.645c0,1.628-0.306,3.451-1.219,5.737l-4.885,14.135C36.805,35.063,40,29.902,40,24 C40,21.219,39.289,18.604,38.043,16.318z"/><path fill="#01579b" d="M4,24c0,11.024,8.97,20,19.999,20C35.03,44,44,35.024,44,24S35.03,4,24,4S4,12.976,4,24z M5.995,24 c0-9.924,8.074-17.999,18.004-17.999S42.005,14.076,42.005,24S33.929,42.001,24,42.001C14.072,42.001,5.995,33.924,5.995,24z"/></svg>',
		'title'         => esc_html__( 'WordPress Membership', 'create-members' ),
		'desc'          => esc_html__( 'Robust content restriction tools and a powerful WordPress membership site, all in one easy to manage plugin.', 'create-members' ),
		'go_to_pro'     => 'https://woooplugin.com/ultimate-membership/',
		'go_to_pro_txt' => esc_html__( 'Go to Pro', 'create-members' ),
		'id'            => 'wp_modules',
		'value'         => $wp_modules,
		'note'          => '',
		'free'          => false,
	),
);
?>
<form class="module-section" method="POST">
	<h1 class="module-header"><?php esc_html_e( 'Modules', 'create-members' ); ?></h1>
	<div class="module-wrap">
		<?php foreach ( $more_products as $key => $value ) { ?>
			<div class="card-block">
				<div class="mod-icon"><?php echo Helper::kses( $value['icon'] ); ?></div>
				<div class="mod-description">
					<div class="desc">
						<h1 class="mod-title"><?php echo esc_html( $value['title'] ); ?></h1>
						<p><?php echo esc_html( $value['desc'] ); ?></p>
						<p class="note"><?php echo esc_html( $value['note'] ); ?></p>
						<?php if ( ! $value['free'] && ! class_exists( 'CreateMembersPro' ) ) : ?>
						<div class="mod-link">
							<a href="<?php echo esc_url( $value['go_to_pro'] ); ?>" target="_blank"><?php echo esc_html( $value['go_to_pro_txt'] ); ?></a>  
						</div>
						<?php endif; ?>
					</div>
					<?php if ( class_exists( 'CreateMembersPro' ) ) : ?>
						<div class="action-block">
							<label class="module-switcher custom-switcher">
								<input type="checkbox" name="<?php echo esc_attr( $value['id'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								value="<?php echo esc_attr( $value['value'] ); ?>" class="switcher-ui-toggle" 
								<?php echo esc_attr( $value['value'] == 'yes' ? 'checked' : '' ); ?>
								/>
								<span class="slider round"></span>
							</label>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php if ( class_exists( 'CreateMembersPro' ) ) : ?>
	<input type="hidden" name="save_modules" value="um-modules"/>
	<button type="submit" class="button button-primary modules-btn mt-3"><?php esc_html_e( 'Save Changes', 'create-members' ); ?></button>
	<?php endif; ?>
</form>
