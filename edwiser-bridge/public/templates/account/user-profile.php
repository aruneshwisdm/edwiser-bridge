<?php
/**
 * User account.
 *
 * @link       https://edwiser.org
 * @since      1.0.2
 * @deprecated 1.2.0 Use shortcode eb_user_account
 * @package    Edwiser Bridge.
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

?>
<div class="eb-user-profile" >

<?php

if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'eb-update-user' ) ) {
	return false;
}

if ( isset( $_GET['eb_action'] ) && 'edit-profile' === sanitize_text_field( wp_unslash( $_GET['eb_action'] ) ) ) {
	$template_loader->wp_get_template(
		'account/edit-user-profile.php',
		array(
			'user_avatar'      => $user_avatar,
			'user'             => $user,
			'user_meta'        => $user_meta,
			'enrolled_courses' => $enrolled_courses,
			'template_loader'  => $template_loader,
		)
	);
} else {
	?>
	<section class="eb-user-info">
		<aside class="eb-user-picture">
			<?php echo esc_html( $user_avatar ); ?>
		</aside>
		<div class="eb-user-data">
			<?php echo '<div>' . @$user->first_name . ' ' . @$user->last_name . '</div>'; // @codingStandardsIgnoreLine. ?>
			<?php echo '<div>' . esc_html( $user->user_email ) . '</div>'; ?>
		</div>

		<div class="eb-edit-profile" >
			<a href="<?php echo esc_url( add_query_arg( 'eb_action', 'edit-profile', get_permalink() ) ); ?>" class="wdm-btn"><?php esc_html_e( 'Edit Profile', 'eb-textdomain' ); ?></a>
		</div>

	</section>
	<?php
}
?>

	<section class="eb-user-courses">
		<div class="course-heading" ><span><?php esc_html_e( 'S.No.', 'eb-textdomain' ); ?></span> <span><?php esc_html_e( 'Enrolled Courses', 'eb-textdomain' ); ?></span></div>
		<div class="eb-course-data">
<?php
if ( ! empty( $enrolled_courses ) ) {
	foreach ( $enrolled_courses as $key => $course ) {
		echo '<div class="eb-course-section course_' . esc_html( $course->ID ) . '">';
		echo '<div>' . esc_html( ( $key++ ) ) . '. </div>';
		echo '<div><a href="' . esc_html( get_the_permalink( $course->ID ) ) . '">' . esc_html( $course->post_title ) . '</a></div>';
		echo esc_html( app\wisdmlabs\edwiserBridge\Eb_Payment_Manager::access_course_button( $course->ID ) );
		echo '</div>';
	}
} else {
	?>
	<p class="eb-no-course">
		<?php
		/* Translator 1: course url */
		printf(
			esc_html__( 'Looks like you are not enrolled in any course, get your first course %$1s', 'eb-textdomain' ),
			'<a href="' . esc_url( site_url( '/courses' ) ) . '">' . esc_html__( 'here', 'eb-textdomain' ) . '</a>.'
		);
		?>
	</p>
	<?php
}
?>
		</div>
	</section>
</div>
