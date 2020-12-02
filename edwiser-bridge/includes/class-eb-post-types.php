<?php
/**
 * The post type registration functionality of the plugin.
 *
 * @link       https://edwiser.org
 * @since      1.0.0
 * @package    Edwiser Bridge
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace app\wisdmlabs\edwiserBridge;

/**
 * Eb post type.
 */
class Eb_Post_Types {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register EDW taxonomies.
	 */
	public function register_taxonomies() {
		if ( taxonomy_exists( 'eb_course_cat' ) ) {
			return;
		}

		do_action( 'eb_register_taxonomy' );

		register_taxonomy(
			'eb_course_cat',
			apply_filters( 'eb_taxonomy_objects_eb_course_cat', array( 'eb_course' ) ),
			apply_filters(
				'eb_taxonomy_args_eb_course_cat',
				array(
					'hierarchical' => true,
					'label'        => __( 'Course Categories', 'eb-textdomain' ),
					'labels'       => array(
						'name'              => __( 'Course Categories', 'eb-textdomain' ),
						'singular_name'     => __( 'Course Category', 'eb-textdomain' ),
						'menu_name'         => _x( 'Course Categories', 'Admin menu name', 'eb-textdomain' ),
						'search_items'      => __( 'Search Course Categories', 'eb-textdomain' ),
						'all_items'         => __( 'All Course Categories', 'eb-textdomain' ),
						'parent_item'       => __( 'Parent Course Categories', 'eb-textdomain' ),
						'parent_item_colon' => __( 'Parent Course Category:', 'eb-textdomain' ),
						'edit_item'         => __( 'Edit Course Category', 'eb-textdomain' ),
						'update_item'       => __( 'Update Course Category', 'eb-textdomain' ),
						'add_new_item'      => __( 'Add New Course Category', 'eb-textdomain' ),
						'new_item_name'     => __( 'New Course Category Name', 'eb-textdomain' ),
					),
					'show_ui'      => true,
					'query_var'    => true,
					'hierarchical' => true,
					'rewrite'      => array( 'slug' => 'eb_category' ),
				)
			)
		);
		do_action( 'eb_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public function register_post_types() {
		do_action( 'eb_register_post_type' );

		if ( ! post_type_exists( 'eb_course' ) ) {
			register_post_type(
				'eb_course',
				apply_filters(
					'eb_register_post_type_courses',
					array(
						'labels'            => array(
							'name'               => __( 'Edwiser Bridge', 'eb-textdomain' ),
							'singular_name'      => __( 'Edwiser Bridge', 'eb-textdomain' ),
							'menu_name'          => _x( 'Edwiser Bridge', 'Admin menu name', 'eb-textdomain' ),
							'all_items'          => __( 'Courses', 'Admin menu name', 'eb-textdomain' ),
							'add_new'            => __( 'Add Course', 'eb-textdomain' ),
							'add_new_item'       => __( 'Add New Course', 'eb-textdomain' ),
							'edit'               => __( 'Edit', 'eb-textdomain' ),
							'edit_item'          => __( 'Edit Course', 'eb-textdomain' ),
							'new_item'           => __( 'New Course', 'eb-textdomain' ),
							'view'               => __( 'View Course', 'eb-textdomain' ),
							'view_item'          => __( 'View Course', 'eb-textdomain' ),
							'search_items'       => __( 'Search Courses', 'eb-textdomain' ),
							'not_found'          => __( 'No Courses found', 'eb-textdomain' ),
							'not_found_in_trash' => __( 'No Courses found in trash', 'eb-textdomain' ),
						),
						'description'       => __(
							'This is where you can add new courses to your Moodle LMS.',
							'eb-textdomain'
						),
						'public'            => true,
						'capability_type'   => 'post',
						'capabilities'      => array(
							'create_posts' => false,
						),
						'map_meta_cap'      => true,
						'show_ui'           => true,
						'show_in_menu'      => true,
						'menu_icon'         => 'dashicons-book-alt',
						'menu_position'     => 54,
						'hierarchical'      => false, // Hierarchical causes memory issues - WP loads all records!
						'rewrite'           => array( 'slug' => 'eb_courses' ),
						'query_var'         => true,
						'supports'          => array( 'title', 'editor', 'thumbnail', 'comments' ),
						'has_archive'       => true,
						'show_in_nav_menus' => true,
						'taxonomies'        => array( 'eb_course_cat' ),
					)
				)
			);
		}

		if ( ! post_type_exists( 'eb_order' ) ) {
			register_post_type(
				'eb_order',
				apply_filters(
					'eb_register_post_type_order',
					array(
						'labels'            => array(
							'name'               => __( 'Orders', 'eb-textdomain' ),
							'singular_name'      => __( 'Order', 'eb-textdomain' ),
							'menu_name'          => _x( 'Orders', 'Admin menu name', 'eb-textdomain' ),
							'add_new'            => __( 'Add Order', 'eb-textdomain' ),
							'add_new_item'       => __( 'Add New Order', 'eb-textdomain' ),
							'edit'               => __( 'Edit', 'eb-textdomain' ),
							'edit_item'          => __( 'Edit Order', 'eb-textdomain' ),
							'new_item'           => __( 'New Order', 'eb-textdomain' ),
							'view'               => __( 'View Order', 'eb-textdomain' ),
							'view_item'          => __( 'View Order', 'eb-textdomain' ),
							'search_items'       => __( 'Search Orders', 'eb-textdomain' ),
							'not_found'          => __( 'No orders found', 'eb-textdomain' ),
							'not_found_in_trash' => __( 'No orders found in trash', 'eb-textdomain' ),
						),
						'description'       => __( 'This is where you can see course orders.', 'eb-textdomain' ),
						'public'            => false,
						'capability_type'   => 'post',
						'capabilities'      => array(
							'create_posts' => true,
						),
						'map_meta_cap'      => true,
						'show_ui'           => true,
						'show_in_menu'      => false,
						'menu_position'     => 80,
						'hierarchical'      => false, // Hierarchical causes memory issues - WP loads all records!
						'rewrite'           => array( 'slug' => 'orders' ),
						'query_var'         => true,
						'supports'          => array( 'title' ),
						'has_archive'       => false,
						'show_in_nav_menus' => false,
					)
				)
			);
		}

		do_action( 'eb_after_register_post_type' );
	}

	/**
	 * Register core post type meta boxes.
	 *
	 * @since         1.0.0
	 */
	public function register_meta_boxes() {
		// register metabox for course post type options.
		add_meta_box(
			'eb_course_options',
			__( 'Course Options', 'eb-textdomain' ),
			array( $this, 'post_options_callback' ),
			'eb_course',
			'advanced',
			'default',
			array( 'post_type' => 'eb_course' )
		);

		// register metabox for recommended course section on single course page.
		add_meta_box(
			'eb_recommended_course_options',
			__( 'Recommended Course Settings', 'eb-textdomain' ),
			array( $this, 'post_options_callback' ),
			'eb_course',
			'advanced',
			'default',
			array( 'post_type' => 'eb_course' )
		);

		// register metabox for moodle Order post type options.
		add_meta_box(
			'eb_order_options',
			__( 'Order Details', 'eb-textdomain' ),
			array( $this, 'post_options_callback' ),
			'eb_order',
			'advanced',
			'default',
			array( 'post_type' => 'eb_order' )
		);
	}

	/**
	 * Callback for metabox fields.
	 *
	 * @since         1.0.0
	 *
	 * @param object $post current $post object.
	 * @param array  $args arguments supplied to the callback function.
	 */
	public function post_options_callback( $post, $args ) {
		$post;
		// get fields for a specific post type.

		if ( 'eb_recommended_course_options' === $args['id'] ) {
			$fields = $this->populate_metabox_fields( $args['id'] );
		} else {
			$fields = $this->populate_metabox_fields( $args['args']['post_type'] );
		}

		$css_class = '';
		echo '<div>';
		if ( 'eb_order' === $args['args']['post_type'] ) {
			$css_class = 'eb-wdm-order-meta';
			echo '<strong>';
			echo esc_html__( 'Order ', 'eb-textdomain' ) . esc_html( printf( '#%s Details', get_the_id() ) );
			echo '</strong>';
			echo "<div id='" . esc_html( $args['args']['post_type'] ) . "'_options' class='post-options " . esc_html( $css_class ) . "'>";
		} else {
			echo "<div id='" . esc_html( $args['args']['post_type'] ) . "'_options' class='post-options'>";
		}

		// render fields using our render_metabox_fields() function.
		foreach ( $fields as $key => $values ) {
			$field_args = array(
				'field_id'  => $key,
				'field'     => $values,
				'post_type' => $args['args']['post_type'],
			);

			$this->render_metabox_fields( $field_args );
		}
		// display content before order options, only if post type is moodle order.
		if ( 'eb_order' === $args['args']['post_type'] ) {
			$order_meta = new Eb_Order_Meta( $this->plugin_name, $this->version );
			$order_meta->get_order_details( get_the_id() );
		}
		echo '</div>';
		do_action( 'eb_post_add_meta', $args );
		echo '</div>';
	}

	/**
	 * Method to populate metabox fields for core post types.
	 *
	 * @since     1.0.0
	 *
	 * @param string $post_type returns array of fields for specific post type.
	 *
	 * @return array $args_array returns complete fields array.
	 */
	private function populate_metabox_fields( $post_type ) {
		global $post;

		$post_id = get_the_id();

		$deletion_status = self::get_post_options( $post_id, 'mdl_course_deleted', $post_type );

		$args_array = array(
			'eb_course'                     => array(
				'moodle_course_id'         => array(
					'label'       => esc_html__( 'Moodle Course ID', 'eb-textdomain' ),
					'description' => '',
					'type'        => 'text',
					'placeholder' => '',
					'attr'        => 'text',
					'default'     => '0',
					'note'        => isset( $deletion_status ) && ! empty( $deletion_status ) ? '<span style="color:red;">' . __( 'This course is deleted on Moodle', 'eb-textdomain' ) . '</span>' : '',
				),
				'course_price_type'        => array(
					'label'       => esc_html__( 'Course Price Type', 'eb-textdomain' ),
					'description' => esc_html__( 'Is it free to join or one time purchase?', 'eb-textdomain' ),
					'type'        => 'select',
					'options'     => array(
						'free'   => esc_html__( 'Free', 'eb-textdomain' ),
						'paid'   => esc_html__( 'Paid', 'eb-textdomain' ),
						'closed' => esc_html__( 'Closed', 'eb-textdomain' ),
					),
					'default'     => array( 'free' ),
				),
				'course_price'             => array(
					'label'       => esc_html__( 'Course Price', 'eb-textdomain' ),
					'description' => esc_html__( 'Course price in currency as defined in settings.', 'eb-textdomain' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Enter course price', 'eb-textdomain' ),
					'default'     => '',
				),
				'course_closed_url'        => array(
					'label'       => esc_html__( 'Optional URL', 'eb-textdomain' ),
					'description' => esc_html__( 'Optional url to redirect user on click of take this course button.', 'eb-textdomain' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Optional URL', 'eb-textdomain' ),
					'default'     => '',
				),
				'course_expirey'           => array(
					'label'       => esc_html__( 'Expire Access', 'eb-textdomain' ),
					'description' => esc_html__( 'Leave this field unchecked if access never expires.', 'eb-textdomain' ),
					'default'     => 'no',
					'type'        => 'checkbox',
					'autoload'    => false,
				),
				'course_expiry_action'     => array(
					'label'       => esc_html__( 'On Course Expiration', 'eb-textdomain' ),
					'description' => esc_html__( 'Select an action to perform on course access expiration.', 'eb-textdomain' ),
					'type'        => 'select',
					'options'     => array(
						'unenroll'   => esc_html__( 'Unenroll', 'eb-textdomain' ),
						'suspend'    => esc_html__( 'Suspend', 'eb-textdomain' ),
						'do-nothing' => esc_html__( 'Do nothing', 'eb-textdomain' ),
					),
					'default'     => array( 'unenroll' ),
				),
				'num_days_course_access'   => array(
					'label'       => esc_html__( 'Expire Access After (days)', 'eb-textdomain' ),
					'description' => esc_html__( 'Number of days the course is accessible', 'eb-textdomain' ),
					'type'        => 'text',
					'default'     => '',
				),
				'course_short_description' => array(
					'label'       => esc_html__( 'Short Description', 'eb-textdomain' ),
					'description' => esc_html__( 'Short description of course.', 'eb-textdomain' ),
					'type'        => 'textarea',
					'placeholder' => '',
					'default'     => '',
				),
			),
			'eb_recommended_course_options' => array(
				'enable_recmnd_courses'               => array(
					'label'       => esc_html__( 'Show Recommended Courses', 'eb-textdomain' ),
					'description' => esc_html__( 'Show recommended courses on single course page.', 'eb-textdomain' ),
					'default'     => 'no',
					'type'        => 'checkbox',
					'autoload'    => false,
				),
				'show_default_recmnd_course'          => array(
					'label'       => esc_html__( 'Show Category Wise Recommended Courses', 'eb-textdomain' ),
					'description' => esc_html__( 'Show category wise selected recommended courses on single course page.', 'eb-textdomain' ),
					'default'     => 'no',
					'type'        => 'checkbox',
					'autoload'    => false,
				),
				'enable_recmnd_courses_single_course' => array(
					'label'       => esc_html__( 'Select Courses', 'eb-textdomain' ),
					'description' => esc_html__( 'Select courses to show in custom courses in recommended course section.', 'eb-textdomain' ),
					'type'        => 'select_multi',
					'options'     => isset( $post->ID ) ? get_all_eb_sourses( $post->ID ) : array(),
					'default'     => array( 'pending' ),
				),
			),
			'eb_order'                      => array(
				'order_status' => array(
					'label'       => esc_html__( 'Order Status', 'eb-textdomain' ),
					'description' => esc_html__( 'Status of Order', 'eb-textdomain' ),
					'type'        => 'select',
					'options'     => array(
						'pending'   => esc_html__( 'Pending', 'eb-textdomain' ),
						'completed' => esc_html__( 'Completed', 'eb-textdomain' ),
						'failed'    => esc_html__( 'Failed', 'eb-textdomain' ),
						'refunded'  => esc_html__( 'Refunded', 'eb-textdomain' ),
					),
					'default'     => array( 'pending' ),
				),
			),
		);

		$args_array = apply_filters( 'eb_post_options', $args_array );

		if ( ! empty( $post_type ) ) {
			if ( isset( $args_array[ $post_type ] ) ) {
				return $args_array[ $post_type ];
			} else {
				return $args_array;
			}
		}
	}

	/**
	 * Generate HTML for displaying metabox fields.
	 *
	 * @since               1.0.0
	 *
	 * @param array $args Field data.
	 */
	public function render_metabox_fields( $args ) {
		$post_id     = get_the_id();
		$field_id    = $args['field_id'];
		$field       = $args['field'];
		$post_type   = $args['post_type'];
		$html        = '';
		$option_name = $post_type . '_options[' . $field_id . ']';
		$option = self::get_post_options( $post_id, $field_id, $post_type );

		$data = '';
		if ( $option ) {
			$data = $option;
		} elseif ( isset( $field['default'] ) ) {
			$data = $field['default'];
		}

		if ( ! isset( $field['placeholder'] ) ) {
			$field['placeholder'] = '';
		}
		$label = '';
		if ( isset( $field['label'] ) ) {
			$label = $field['label'];
		}
		$attr = '';
		if ( isset( $field['attr'] ) ) {
			$attr = $field['attr'];
		}

		$html .= "<div id='{$post_type}_{$field_id}' class='field-input-box'>";
		$html .= "<span class='eb-option-label'>
					<label class='field-label'>{$label}</label>
				  </span>";

		$html .= "<span class='eb-option-input'>
				  <div class='eb-option-div'>";

		switch ( $field['type'] ) {
			case 'title':
				$html .= '<h2 id="' . esc_attr( $field_id ) . '" />' . $field['label'] . '</h2>';
				break;
			case 'label':
				$html .= '<span id="' . esc_attr( $field_id ) . '" /><b>' . $data . '</b></span>' . "\n";
				break;
			case 'text':
			case 'password':
			case 'number':
			case 'date':
				$html .= '<input id="' . esc_attr( $field_id );
				$html .= '" type="' . $field['type'];
				$html .= '" name="' . esc_attr( $option_name );
				$html .= '" placeholder="' . esc_attr( $field['placeholder'] );
				$html .= '" value="' . $data . '" ' . $attr . '/>' . "\n";
				break;
			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field_id );
				$html .= '" type="text" name="' . esc_attr( $option_name );
				$html .= '" placeholder="' . esc_attr( $field['placeholder'] );
				$html .= '" value=""/>' . "\n";
				break;
			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field_id );
				$html .= '" rows="5" cols="50" name="' . esc_attr( $option_name );
				$html .= '" placeholder="' . esc_attr( $field['placeholder'] ) . '">';
				$html .= $data . '</textarea>' . "\n";
				break;
			case 'checkbox':
				$checked = '';
				if ( $option && 'yes' === $option ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field_id );
				$html .= '" type="' . $field['type'];
				$html .= '" name="' . esc_attr( $option_name );
				$html .= '" ' . $checked . '/>' . "\n";
				break;
			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field_id . '_' . $k );
					$html .= ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k );
					$html .= '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;
			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field_id . '_' . $k );
					$html .= '"><input type="radio" ' . checked( $checked, true, false );
					$html .= ' name="' . esc_attr( $option_name );
					$html .= '" value="' . esc_attr( $k );
					$html .= '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;
			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field_id ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k === $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false );
					$html .= ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;
			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field_id );
				$html .= '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false );
					$html .= ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
				break;
		}

		switch ( $field['type'] ) {
			case 'textarea':
			case 'select_multi':
				$html .= '<em><p class="description-label ' . esc_attr( $field_id );
				$html .= '">' . $field['description'] . '</p></em>';
				break;
			default:
				$html .= '<span class="description-label ' . esc_attr( $field_id );
				$html .= '"><img class="help-tip" src="' . EB_PLUGIN_URL . 'images/question.png" data-tip="';
				$html .= $field['description'] . '" /></span>';

				// Adding note after input.
				$html .= isset( $field['note'] ) ? $field['note'] : '';

				break;
		}

		$html .= '</div></span></div>';
		echo esc_html( $html );
	}

	/**
	 * Hanlder to save post data on post save
	 * At first we are cleaning & formatting the data then saving in post meta.
	 *
	 * @since           1.0.0
	 *
	 * @param int $post_id id of current post.
	 *
	 * @return bool returns true
	 */
	public function handle_post_options_save( $post_id ) {
		$fields = array();

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// Options to update will be stored here.
		$update_post_options = array();
		// get current post type.
		$post_type = get_post_type( $post_id );

		if ( ! in_array( $post_type, array( 'eb_course', 'eb_order' ) ) ) {
			return;
		} else {
			if ( 'eb_course' == $post_type ) {
				$fields = $this->populate_metabox_fields( $post_type );
				$fields = array_merge( $this->populate_metabox_fields( 'eb_recommended_course_options' ), $fields );
			}
			$post_options = array();
			if ( isset( $_POST[ $post_type . '_options' ] ) ) {
				$post_options = sanitize_text_field( wp_unslash( $_POST[ $post_type . '_options' ] ) );
			}
			if ( ! empty( $post_options ) ) {
				foreach ( $fields as $key => $values ) {
					$option_name  = $key;
					$option_value = null;
					if ( isset( $post_options[ $key ] ) ) {
						$option_value = wp_unslash( $post_options[ $key ] );
					}
					// format the values.
					switch ( sanitize_title( $values['type'] ) ) {
						case 'checkbox':
							if ( is_null( $option_value ) ) {
								$option_value = 'no';
							} else {
								$option_value = 'yes';
							}
							break;
						case 'textarea':
							$option_value = wp_kses_post( trim( $option_value ) );
							break;
						case 'text':
						case 'text_secret':
						case 'number':
						case 'select':
						case 'password':
						case 'radio':
							$option_value = wp_clean( $option_value );
							break;
						case 'select_multi':
						case 'checkbox_multi':
							$option_value = array_filter( array_map( 'wpClean', (array) $option_value ) );
							break;
						default:
							// $option_value = isset( $post_options[ $key ] ) ?
							// wp_unslash( $post_options[ $key ] ) : null;
							break;
					}

					if ( ! is_null( $option_value ) ) {
						$update_post_options[ $option_name ] = $option_value;
					}
				}

				if ( is_array( $update_post_options ) ) {
					/*
					 * merge previous values in array with new values retrieved
					 * replace old values with new values and save as option
					 *
					 * To keep custom buyer data saved in same order meta key, so that it is not erased on post save.
					 */
					$previous = get_post_meta( $post_id, $post_type . '_options', true );
					$merged   = array_merge( $previous, $update_post_options );
					update_post_meta( $post_id, $post_type . '_options', $merged );
				}
			}
		}

		return true;
	}

	/**
	 * Update post updated messages for all CPTs added by our plugin.
	 *
	 * @since  1.0.0
	 *
	 * @param [type] $messages messages.
	 */
	public function custom_post_type_update_messages( $messages ) {
		global $post;

		$post_ID   = $post->ID;
		$post_type = get_post_type( $post_ID );

		$obj      = get_post_type_object( $post_type );
		$singular = $obj->labels->singular_name;

		$messages[ $post_type ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( '%1$s', esc_attr( $singular ) ) .
				esc_html__( ' updated.', 'eb-textdomain' ) . '<a href="' . sprintf( '%2$s" target="_blank">', esc_url( get_permalink( $post_ID ) ) ) . esc_html__( ' View', 'eb-textdomain' ) .
				sprintf(
					'%3$s</a>',
					strtolower( $singular )
				),
			2  => esc_html__( 'Custom field updated.', 'eb-textdomain' ),
			3  => esc_html__( 'Custom field deleted.', 'eb-textdomain' ),
			4  => sprintf( '%s ', esc_attr( $singular ) ) . esc_html__( 'updated.', 'eb-textdomain' ),
			5  => isset( $_GET['revision'] ) ? sprintf(
				'%s ',
				wp_post_revision_title( (int) $_GET['revision'], false )
			) . esc_html__( 'restored to revision from ', 'eb-textdomain' ) . sprintf(
				'%s ',
				esc_attr( $singular )
			) : false,
			6  => sprintf(
				'%1$s published. <a href="%2$s">View %3$s</a>',
				$singular,
				esc_url( get_permalink( $post_ID ) ),
				strtolower( $singular )
			),
			7  => sprintf( '%s ', esc_attr( $singular ) ) . esc_html__( 'saved.', 'eb-textdomain' ),
			8  => sprintf(
				'%1$s submitted. <a href="%2$s" target="_blank">Preview %3$s</a>',
				$singular,
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ),
				strtolower( $singular )
			),
			9  => sprintf(
				'%1$s' . esc_html__( 'scheduled for: <strong> ', 'eb-textdomain' ) . '%2$s' . esc_html__( '</strong>. <a href="', 'eb-textdomain' ) . '%3$s' . esc_html__( '" target="_blank">Preview ', 'eb-textdomain' ) . '%4$s</a>',
				$singular,
				date_i18n(
					esc_html__( 'M j, Y @ G:i' ),
					strtotime( $post->post_date )
				),
				esc_url(
					get_permalink( $post_ID )
				),
				strtolower( $singular )
			),
			10 => sprintf(
				'%1$s' . esc_html__( ' draft updated. <a href="', 'eb-textdomain' ) . '%2$s' . esc_html__( '" target="_blank">Preview ', 'eb-textdomain' ) . '%3$s </a>',
				$singular,
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ),
				strtolower( $singular )
			),
		);

		return $messages;
	}

	/**
	 * Get post option.
	 *
	 * @param text $post_id post id.
	 * @param text $key key id.
	 * @param text $post_type post_type id.
	 * @param text $default default id.
	 */
	public static function get_post_options( $post_id, $key, $post_type, $default = false ) {
		if ( empty( $key ) ) {
			return $default;
		}

		$post_options = get_post_meta( $post_id, $post_type . '_options', true );

		if ( is_array( $key ) ) {
			foreach ( $key as $k ) {
				$value[ $k ] = isset( $post_options[ $k ] ) ? $post_options[ $k ] : $default;
			}
		} else {
			$value = isset( $post_options[ $key ] ) ? $post_options[ $key ] : $default;
		}

		return $value;
	}
}
