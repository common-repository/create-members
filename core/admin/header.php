<header class="menu">
	<a href="<?php echo esc_url( 'https://woooplugin.com/ultimate-membership/' ); ?>" target="_blank">
		<div class="logo">
			<img src = "<?php echo esc_url( CreateMembers::assets_url() . 'images/membership-icon.svg' ); ?>"
				alt="ultimate-membership" 
				width="45px"
			/>
			<span class='version'><?php echo esc_html( 'v ' . CreateMembers::get_version() ); ?></span>
		</div>
	</a>
	<?php
		$menus = array(
			array(
				'name'   => esc_html__( 'Settings', 'create-members' ),
				'url'    => admin_url() . 'admin.php?page=um-settings',
				'slug'   => 'um-settings',
				'target' => '_self',
			),
			array(
				'name'   => esc_html__( 'Subscriptions', 'create-members' ),
				'url'    => admin_url() . 'admin.php?page=um-plans',
				'slug'   => 'um-plans',
				'target' => '_self',
			),
			array(
				'name'   => esc_html__( 'Members', 'create-members' ),
				'url'    => admin_url() . 'admin.php?page=um-members',
				'slug'   => 'um-members',
				'target' => '_blank',
			),
			array(
				'name'   => esc_html__( 'Reports', 'create-members' ),
				'url'    => admin_url() . 'admin.php?page=um-reports',
				'slug'   => 'um-reports',
				'target' => '_blank',
			),
			array(
				'name'   => esc_html__( 'Support', 'create-members' ),
				'url'    => 'https://woooplugin.com/support/',
				'target' => '_blank',
			),
			array(
				'name'   => esc_html__( 'Feature Request', 'create-members' ),
				'url'    => 'https://app.loopedin.io/ultimate-membership#/roadmap',
				'target' => '_blank',
			),

		);

		if ( ! class_exists( 'CreateMembers' ) ) {
			$menus[] = array(
				'name'   => esc_html__( 'Upgrade to Pro', 'create-members' ),
				'url'    => 'https://woooplugin.com/ultimate-membership/',
				'target' => '_blank',
			);
		}
		?>
	<div class="navigation">
		<?php
			$current_page = ! empty( $_GET['page'] ) ? $_GET['page'] : '';
		foreach ( $menus as $key => $value ) {
			$active = ( ! empty( $value['slug'] ) && $value['slug'] == $current_page ) ? 'active' : '';
			$class  = $value === end( $menus ) ? 'upgrade_pro' : '';
			?>
				<li>
					<a class="<?php echo esc_attr( $class ) . ' ' . esc_attr( $active ); ?>" href="<?php echo esc_url( $value['url'] ); ?>"
						target="<?php echo esc_attr( $value['target'] ); ?>">
					<?php echo esc_html( $value['name'] ); ?>
					</a>
				</li>
				<?php
		}
		?>
	</div>
</header>