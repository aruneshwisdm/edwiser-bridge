<?php
/**
 * Partial: Page - right section.
 *
 * @package    Edwiser Bridge
 */

$documentation = '';

$extensions_details = apply_filters(
	'eb_setting_help_data',
	array(
		'woo-int'         => array(
			'name'   => 'Woocommerce Integration',
			'path'   => 'woocommerce-integration/bridge-woocommerce.php',
			'doc'    => 'https://edwiser.org/documentation/edwiser-bridge-woocommerce-integration/',
			'rating' => 'https://edwiser.org/bridge/extensions/woocommerce-integration/documentation/',
		),
		'sso'             => array(
			'name'   => 'Single Sign On',
			'path'   => 'edwiser-bridge-sso/sso.php',
			'doc'    => 'https://edwiser.org/documentation/single-sign-on/',
			'rating' => 'https://edwiser.org/bridge/extensions/woocommerce-integration/documentation/',
		),
		'bulk-purchase'   => array(
			'name'   => 'Bulk Purchase',
			'path'   => 'edwiser-multiple-users-course-purchase/edwiser-multiple-users-course-purchase.php',
			'doc'    => 'https://edwiser.org/documentation/bulk-purchase/',
			'rating' => 'https://edwiser.org/bridge/extensions/woocommerce-integration/documentation/',
		),
		'selective-synch' => array(
			'name'   => 'Selective Synchronization',
			'path'   => 'selective-synchronization/selective-synchronization.php',
			'doc'    => 'https://edwiser.org/documentation/selective-synchronization/',
			'rating' => 'https://edwiser.org/bridge/extensions/woocommerce-integration/documentation/',
		),
	)
);


foreach ( $extensions_details as $key => $value ) {
	if ( is_plugin_active( $value['path'] ) ) {
		$documentation .= '<li>
							<a href="' . $value['doc'] . '" target="_blank"> ' . esc_attr( $value['name'] ) . '</a>
						</li>';
	}
}



?>

<div>
	<div class="eb_settings_pop_btn_wrap">
		<div class="eb_settings_help_btn_wrap">
			<button class='eb_open_btn'> <?php echo esc_html__( 'Get Help', 'eb-textdomain' ); ?></button>
		</div>
		<div class="eb_settings_rate_btn_wrap">
			<a class="eb_open_btn" target="_blank" href="https://wordpress.org/support/plugin/edwiser-bridge/reviews/">
				<?php echo esc_html__( 'Rate Us', 'eb-textdomain' ); ?>
			</a>
		</div>
	</div>

	<div class="eb_setting_pop_up_wrap">
		<div class='eb-setting-right-sidebar'>

			<div class="eb_setting_help_pop_up">
				<div>
					<span class="closebtn">×</span>
				</div>
				<!-- <h3 class="eb-setting-sidebar-h3"> Help </h3> -->
				<div class="eb-setting-help-accordion">
					<h4 class='eb_setting_help_h4'><?php echo esc_html__( 'Documentation', 'eb-teh3xtdomain' ); ?></h4>
					<div>
						<ol>

							<li>
								<a href="https://edwiser.org/bridge/documentation/" target="_blank"> <?php echo esc_html__( 'Edwiser Bridge', 'eb-textdomain' ); ?></a>
							</li>


						<?php echo esc_attr( $documentation ); ?>

						</ol>
					</div>

					<h4 class='eb_setting_help_h4'><?php echo esc_html__( 'FAQs', 'eb-textdomain' ); ?></h4>
					<div>
						<a href="https://edwiser.helpscoutdocs.com/collection/85-edwiser-bridge-plugin" target="_blank"> <?php echo esc_html__( 'Click here ', 'eb-textdomain' ); ?>  </a>
						<?php echo esc_html__( 'to check frequently asked questions.', 'eb-textdomain' ); ?>
					</div>

					<h4 class='eb_setting_help_h4'><?php echo esc_html__( 'Contact Us', 'eb-textdomain' ); ?></h4>
					<div>
						<a href="https://edwiser.org/bridge/" target="_blank"> <?php echo esc_html__( 'Click here ', 'eb-textdomain' ); ?>  </a> <?php echo esc_html__( 'to chat with us', 'eb-textdomain' ); ?>
					</div>
				</div>
			</div>


			<!-- <div class="eb_setting_rate_pop_up">
				<div>
					<span class="closebtn">×</span>
				</div>
				<h4 class="eb_setting_help_h4"><?php echo esc_html__( 'Rate And Review', 'eb-textdomain' ); ?></h4>
				<div>
					<a href="https://wordpress.org/support/plugin/edwiser-bridge/reviews/"> <?php echo esc_html__( 'Click here', 'eb-textdomain' ); ?> </a>
					<?php echo esc_html__( ' to rate us', 'eb-textdomain' ); ?>
				</div>
			</div> -->


			<!-- <div>
				<h3 class="eb-setting-sidebar-h3"> Other Products </h3>
				<div>
					<div> LINK 1 </div>
					<div> LINK 2 </div>
					<div> LINK 3 </div>
				</div>
			</div> -->

		</div>
	</div>
</div>
