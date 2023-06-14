<?php
/**
 * WholesaleX Users
 *
 * @package WHOLESALEX
 * @since v.1.0.0
 */

namespace WHOLESALEX;

use WP_User;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * WholesaleX Users Class
 */
class WHOLESALEX_Users extends \WP_List_Table {

	/**
	 * WholesaleX User Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_submenu_page(
			'wholesalex-overview',
			__( 'Users', 'wholesalex' ),
			__( 'Users', 'wholesalex' ),
			'manage_options',
			'wholesalex-users',
			array( $this, 'users_list' )
		);
		parent::__construct(
			array(
				'singular' => __( 'user', 'wholesalex' ),
				'plural'   => __( 'users', 'wholesalex' ),
				'ajax'     => false,
			)
		);
		add_action( 'admin_head', array( $this, 'admin_header' ) );
		add_action( 'admin_init', array( $this, 'user_status_change_action' ) );
		add_filter( 'user_row_actions', array( $this, 'user_status_action_button' ), 10, 2 );
		add_filter( 'wholesalex_user_row_actions', array( $this, 'user_status_action_button_wholesalex_users' ), 10, 2 );
        add_action( 'pre_get_users', array( $this, 'filter_user_section' ) );

	}

	/**
	 * WholesaleX User Status Action Button
	 *
	 * @param array   $actions Array of Action.
	 * @param WP_User $user_object WordPress User Object.
	 * @since 1.0.0
	 */
	public function user_status_action_button( $actions, $user_object ) {
		$nonce = wp_create_nonce( 'wholesalex_user_status_change' );
		if ( $user_object->ID ) {
			$new_actions = array(
				'pending'  => '<a href="' . admin_url( 'users.php?&action=set-pending&userid=' . absint( $user_object->ID ) . '&_wpnonce=' . $nonce ) . '">' . __( 'Waiting Approval', 'wholesalex' ) . '</a>',
				'active'   => '<a href="' . admin_url( 'users.php?&action=set-active&userid=' . absint( $user_object->ID ) . '&_wpnonce=' . $nonce ) . '">' . __( 'Active', 'wholesalex' ) . '</a>',
				'inactive' => '<a href="' . admin_url( 'users.php?&action=set-inactive&userid=' . absint( $user_object->ID ) . '&_wpnonce=' . $nonce ) . '">' . __( 'Inactive', 'wholesalex' ) . '</a>',
				'reject'   => '<a href="' . admin_url( 'users.php?&action=set-reject&userid=' . absint( $user_object->ID ) . '&_wpnonce=' . $nonce ) . '">' . __( 'Rejected', 'wholesalex' ) . '</a>',
			);
			$status      = get_user_meta( $user_object->ID, '__wholesalex_status', true );

			if ( $status ) {
				unset( $new_actions[ $status ] );
			}
			return array_merge( $actions, $new_actions );
		}

		return $actions;
	}

	/**
	 * WholesaleX User Status Action Button
	 *
	 * @param array   $actions Array of Action.
	 * @param WP_User $user_object WordPress User Object.
	 * @since 1.0.1
	 */
	public function user_status_action_button_wholesalex_users( $actions, $user_object ) {
		$nonce = wp_create_nonce( 'wholesalex_user_status_change' );

		$__id = '';
		if ( is_array( $user_object ) ) {
			$__id = $user_object['ID'];
		} elseif ( is_object( $user_object ) ) {
			$__id = $user_object->ID;
		}
		if ( $__id ) {
			$new_actions = array(
				'pending'  => '<a href="' . admin_url( 'admin.php?page=wholesalex-users&action=set-pending&userid=' . absint( $__id ) . '&_wpnonce=' . $nonce ) . '">' . __( 'Waiting Approval', 'wholesalex' ) . '</a>',
				'active'   => '<a href="' . admin_url( 'admin.php?page=wholesalex-users&action=set-active&userid=' . absint( $__id ) . '&_wpnonce=' . $nonce ) . '">' . __( 'Active', 'wholesalex' ) . '</a>',
				'inactive' => '<a href="' . admin_url( 'admin.php?page=wholesalex-users&action=set-inactive&userid=' . absint( $__id ) . '&_wpnonce=' . $nonce ) . '">' . __( 'Inactive', 'wholesalex' ) . '</a>',
				'reject'   => '<a href="' . admin_url( 'admin.php?page=wholesalex-users&action=set-reject&userid=' . absint( $__id ) . '&_wpnonce=' . $nonce ) . '">' . __( 'Rejected', 'wholesalex' ) . '</a>',
			);
			$status      = get_user_meta( $__id, '__wholesalex_status', true );

			if ( $status ) {
				unset( $new_actions[ $status ] );
			}
			return array_merge( $actions, $new_actions );
		}

		return $actions;
	}


	/**
	 * WholesaleX User Status Change
	 *
	 * @return void
	 * @since 1.0.0
	 * @since 1.0.1 User Role Not Changed Issue Fixed.
	 */
	public function user_status_change_action() {
		$action  = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		$user_id = isset( $_GET['userid'] ) ? sanitize_text_field( $_GET['userid'] ) : '';
		if ( isset( $_REQUEST['_wpnonce'] ) && $action && $user_id && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'wholesalex_user_status_change' ) ) {
			if ( 'set-active' === $action || 'set-pending' === $action || 'set-inactive' === $action || 'set-reject' === $action ) {
				$new_action = str_replace( 'set-', '', $action );
				$old_status = get_user_meta( $user_id, '__wholesalex_status', true );
				update_user_meta( $user_id, '__wholesalex_status', $new_action );
				$__user_role = get_user_meta( $user_id, '__wholesalex_role', true );
				if ( empty( $__user_role ) ) {
					$__registration_role = get_user_meta( $user_id, '__wholesalex_registration_role', true );
					if ( ! empty( $__registration_role ) ) {
						wholesalex()->change_role( $user_id, $__registration_role );
					}
				}
				do_action( 'wholesalex_set_status_' . $new_action, $user_id, $old_status );
			} elseif ( 'delete' === $action ) {
				wp_delete_user( $user_id );
			}
		}
	}

	/**
	 * Get WholesaleX User Data
	 *
	 * @param String $search Search Key.
	 * @return array $data User Data.
	 * @since 1.0.0
	 * @since 1.0.1 Not assigned role and registration role added.
	 */
	public function get_user_data( $search ) {
		$data = array();
		$args = array(
			'meta_query' => array(
				'relation' => 'AND',
			),
		);
		//phpcs:ignore
		if ( isset( $_POST['new_role'] ) && $_POST['new_role'] ) {
			$args['meta_query'][] = array(
				'key'     => '__wholesalex_status',
				'value'   => sanitize_text_field( $_POST['new_role'] ), //phpcs:ignore
				'compare' => '=',
			);
		} else {
			$args['meta_query'][] = array(
				'key'     => '__wholesalex_status',
				'value'   => '',
				'compare' => '!=',
			);
		}

		$wp_user_query = new \WP_User_Query( $args );
		$authors       = $wp_user_query->get_results();

		if ( ! empty( $authors ) ) {
			foreach ( $authors as $author ) {
				$__role_id   = get_the_author_meta( '__wholesalex_role', $author->ID );
				$__role_name = __( 'Not Assigned To Any Role!', 'wholesalex' );
				if ( empty( $__role_id ) ) {
					$__role_id = get_the_author_meta( '__wholesalex_registration_role', $author->ID );
				}
				if ( ! empty( $__role_id ) ) {
					$__role_name = wholesalex()->get_role_name_by_role_id( $__role_id );
				}
				$temp         = array();
				$temp['ID']   = $author->ID;
				$temp['user'] = '<a href="' . get_admin_url( null, 'user-edit.php?user_id=' . $author->ID ) . '">' . $author->user_login . '</a>';
				$temp['name'] = get_the_author_meta( 'first_name', $author->ID ) . ' ' . get_the_author_meta( 'last_name', $author->ID );
				if ( empty( trim( $temp['name'] ) ) ) {
					$temp['name'] = get_the_author_meta( 'display_name', $author->ID );
				}
				$temp['email']             = $author->user_email;
				$temp['registration_date'] = $author->user_registered;
				$temp['role']              = $__role_name;
				$__wholesalex_status       = get_the_author_meta( '__wholesalex_status', $author->ID );
				switch ( $__wholesalex_status ) {
					case 'pending':
						$temp['status'] = __( 'Waiting Approval', 'wholesalex' );
						break;
					case 'active':
						$temp['status'] = __( 'Active', 'wholesalex' );
						break;
					case 'inactive':
						$temp['status'] = __( 'Inactive', 'wholesalex' );
						break;
					case 'reject':
						$temp['status'] = __( 'Rejected', 'wholesalex' );
						break;
					default:
						$temp['status'] = __( 'No Status!', 'wholesalex' );
				}

				if ( $search ) {
					if ( preg_match( "/{$search}/i", $temp['user'] ) ||
					preg_match( "/{$search}/i", $temp['name'] ) ||
					preg_match( "/{$search}/i", $temp['email'] ) ) {
						$data[] = $temp;
					}
				} else {
					$data[] = $temp;
				}
			}
		}
		return $data;
	}

	/**
	 * Admin Header
	 *
	 * @return void
	 */
	public function admin_header() {
		$get_data = wholesalex()->sanitize( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification
		$page     = ( isset( $get_data['page'] ) ) ? $get_data['page'] : false;
		if ( 'wholesalex-users' !== $page ) {
			return;
		}
		?>
		<style type="text/css">
			body { background-color: #f0f0f1; }
			.wp-list-table .column-cb { width: 2%; }
			/* .wp-list-table .column-user { width: 16%; }
			.wp-list-table .column-name { width: 20%; }
			.wp-list-table .column-email { width: 20%; }
			.wp-list-table .column-date { width: 16%; }
			.wp-list-table .column-role { width: 16%; }
			.wp-list-table .column-status { width: 10%; } */
		</style>
		<?php
	}

	/**
	 * WholesaleX No User Found
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No WholesaleX Users found!', 'wholesalex' );
	}

	/**
	 * WholesaleX User Colum Handler
	 *
	 * @param array  $item .
	 * @param String $column_name Name of Column.
	 * @since 1.0.0
	 */
	public function column_default( $item, $column_name ) {
		$column_value = '';
		switch ( $column_name ) {
			case 'user':
			case 'name':
			case 'email':
			case 'role':
			case 'registration_date':
			case 'status':
				$column_value = $item[ $column_name ];
				break;
			default:
				$column_value = $column_name;
		}
		$column_value = apply_filters( 'wholesalex_users_column_value', $column_value, $item, $column_name );
		return $column_value;
	}

	/**
	 * Get Sortable Columns
	 *
	 * @return array $sortable_columns
	 * @since 1.0.0
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'user'              => array( 'user', false ),
			'email'             => array( 'email', false ),
			'registration_date' => array( 'registration_date', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Get Columns
	 *
	 * @return array $columns
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = apply_filters(
			'wholesalex_users_columns',
			array(
				'cb'                => '<input type="checkbox" />',
				'user'              => __( 'Username', 'wholesalex' ),
				'name'              => __( 'Full Name', 'wholesalex' ),
				'email'             => __( 'Email', 'wholesalex' ),
				'registration_date' => __( 'Date', 'wholesalex' ),
				'role'              => __( 'Role', 'wholesalex' ),
				'status'            => __( 'Status', 'wholesalex' ),
			)
		);
		return $columns;
	}

	/**
	 * Usort Reorder
	 *
	 * @param array $a .
	 * @param array $b .
	 * @return array $result
	 */
	public function usort_reorder( $a, $b ) {
		$get_data = wholesalex()->sanitize( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification

		$orderby = ( ! empty( $get_data['orderby'] ) ) ? $get_data['orderby'] : 'user';
		$order   = ( ! empty( $get_data['order'] ) ) ? $get_data['order'] : 'asc';
		$result  = strcmp( $a[ $orderby ], $b[ $orderby ] );
		return ( 'asc' === $order ) ? $result : -$result;
	}

	/**
	 * Get Bulk Action
	 *
	 * @return array $actions
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete'          => __( 'Delete', 'wholesalex' ),
			'bulk-status-pending'  => __( 'Change Status to Waiting Approval', 'wholesalex' ),
			'bulk-status-active'   => __( 'Change Status to Active', 'wholesalex' ),
			'bulk-status-inactive' => __( 'Change Status to Inactive', 'wholesalex' ),
			'bulk-status-reject'   => __( 'Change Status to Reject', 'wholesalex' ),
		);
		return $actions;
	}

	/**
	 * Render Checkbox For Each Row
	 *
	 * @param array $item Item.
	 * @since 1.0.0
	 */
	public function column_cb( $item ) {
		if ( is_array( $item ) ) {
			return sprintf(
				'<input type="checkbox" name="userid[]" value="%s" />',
				$item['ID']
			);
		} elseif ( is_object( $item ) ) {
			return sprintf(
				'<input type="checkbox" name="userid[]" value="%s" />',
				$item->ID
			);
		}
	}

	/**
	 *  Prepare Items to feed WP_Table_List.
	 *
	 * @param string $search Search Key.
	 * @return void
	 */
	public function prepare_items( $search = '' ) {
		$columns = $this->get_columns();
		$hidden  = array();
		$this->process_bulk_action();
		$sortable              = $this->get_sortable_columns();
		$user_data             = $this->get_user_data( $search );
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $user_data, array( &$this, 'usort_reorder' ) );

		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = count( $user_data );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$this->items = array_slice( $user_data, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * User Status Change Handler
	 *
	 * @param string $which Top or Bottom.
	 * @since 1.0.0
	 */
	public function extra_tablenav( $which ) {
		$id        = 'bottom' === $which ? 'new_role2' : 'new_role';
		$button_id = 'bottom' === $which ? 'changeit2' : 'changeit';
		// phpcs:ignore
		$status    = isset( $_POST['new_role'] ) ? sanitize_text_field($_POST['new_role']) : '';
		?>
		<div class="alignleft actions">
			<?php if ( current_user_can( 'promote_users' ) && $this->has_items() ) : ?>
			<select name="<?php echo esc_html( $id ); ?>" id="<?php echo esc_html( $id ); ?>">
				<option value=""><?php esc_html_e( '- Select Status -', 'wholesalex' ); ?></option>
				<option value="pending" <?php selected( $status, 'pending' ); ?>><?php esc_html_e( 'Waiting Approval', 'wholesalex' ); ?></option>
				<option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'wholesalex' ); ?></option>
				<option value="inactive" <?php selected( $status, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'wholesalex' ); ?></option>
				<option value="reject" <?php selected( $status, 'reject' ); ?>><?php esc_html_e( 'Reject', 'wholesalex' ); ?></option>
			</select>
				<?php
				submit_button( __( 'Filter', 'wholesalex' ), 'button', $button_id, false );
			endif;
			?>
            
		</div>
		<?php
	}

	/**
	 * Process Bulk Actions
	 *
	 * @return void
	 */
	public function process_bulk_action() {
		if ( isset( $_POST['action'] ) && isset( $_POST['wholesalex_user_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['wholesalex_user_nonce'] ), 'wholesalex_users' ) ) {

			$get_data     = wholesalex()->sanitize( $_GET );
			$request_data = wholesalex()->sanitize( $_REQUEST );
			$post_data    = wholesalex()->sanitize( $_POST );

			$user_ids = isset( $post_data['userid'] ) ? $post_data['userid'] : array();
			if ( 'bulk-delete' === $post_data['action'] && isset( $post_data['userid'] ) ) {
				foreach ( $user_ids as $val ) {
					wp_delete_user( $val );
				}
			} elseif ( isset( $post_data['userid'] ) && isset( $post_data['action'] ) ) {
				foreach ( $user_ids as $val ) {
					switch ( $post_data['action'] ) {
						case 'bulk-status-pending':
							update_user_meta( $val, '__wholesalex_status', 'pending' );
							do_action( 'wholesalex_set_status_pending', $val );
							break;
						case 'bulk-status-active':
							update_user_meta( $val, '__wholesalex_status', 'active' );

							$__registration_role = get_user_meta( $val, '__wholesalex_registration_role', true );
							if ( ! empty( $__registration_role ) ) {
								wholesalex()->change_role( $val, $__registration_role );
							}

							do_action( 'wholesalex_set_status_active', $val );
							break;
						case 'bulk-status-reject':
							update_user_meta( $val, '__wholesalex_status', 'reject' );
							do_action( 'wholesalex_set_status_reject', $val );
							break;
						case 'bulk-status-inactive':
							update_user_meta( $val, '__wholesalex_status', 'inactive' );
							do_action( 'wholesalex_set_status_inactive', $val );
							break;
					}
				}
			}
		}
		$user_id = $get_data['userid'] ?? '';
		if ( isset( $request_data['_wpnonce'] ) && $user_id && wp_verify_nonce( $request_data['_wpnonce'], 'wholesalex_user_status' ) ) {
			$action = $this->current_action();
			if ( 'delete' === $action ) {
				wp_delete_user( $user_id );
			} elseif ( 'set-active' === $action || 'set-pending' === $action || 'set-inactive' === $action || 'set-reject' === $action ) {
				$old_status = get_user_meta( $user_id, '__wholesalex_status', true );
				update_user_meta( $user_id, '__wholesalex_status', str_replace( 'set-', '', $action ) );

				if ( 'set-active' === $action ) {
					$__user_role = get_user_meta( $user_id, '__wholesalex_role', true );
					if ( ! empty( $__user_role ) ) {
						$__registration_role = get_user_meta( $user_id, '__wholesalex_registration_role', true );
						if ( ! empty( $__registration_role ) ) {
							wholesalex()->change_role( $user_id, $__registration_role );
						}
					}
					do_action( 'wholesalex_set_status_active', $user_id, $old_status );

				} elseif ( 'set-pending' === $action ) {
					do_action( 'wholesalex_set_status_pending', $user_id, $old_status );

				} elseif ( 'set-inactive' === $action ) {
					do_action( 'wholesalex_set_status_inactive', $user_id, $old_status );

				} elseif ( 'set-reject' === $action ) {
					do_action( 'wholesalex_set_status_reject', $user_id, $old_status );

				}
			}
		}
	}

	/**
	 * Generate HTML for a single row on the wholesalex users in admin panel.
	 *
	 * @param array $user The current User Object.
	 * @since 1.0.1
	 */
	public function single_row_columns( $user ) {

		// Set up the hover actions for this user.
		$actions = array();

		// Check if the user for this row is editable.
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link.
			$edit_link = esc_url(
				add_query_arg(
					'wp_http_referer',
					rawurlencode( wp_unslash( esc_url_raw( $_SERVER['REQUEST_URI'] ) ) ),
					get_edit_user_link( $user['ID'] )
				)
			);

			if ( current_user_can( 'edit_user', $user['ID'] ) ) {
				$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit', 'wholesalex' ) . '</a>';

				$__user_id = $user['ID'];

				$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "admin.php?page=wholesalex-users&action=delete&amp;userid=$__user_id", 'wholesalex_user_status_change' ) . "'>" . __( 'Delete', 'wholesalex' ) . '</a>';
			}
		}

		$actions = apply_filters( 'wholesalex_user_row_actions', $actions, $user );

		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$classes = "$column_name column-$column_name";
			if ( $primary === $column_name ) {
				$classes .= ' has-row-actions column-primary';
			}

			if ( in_array( $column_name, $hidden, true ) ) {
				$classes .= ' hidden';
			}

			// Comments column uses HTML in the display name with screen reader text.
			// Strip tags to get closer to a user-friendly string.
			$data = 'data-colname="' . esc_attr( wp_strip_all_tags( $column_display_name ) ) . '"';

			$attributes = "class='$classes' $data";

			if ( 'cb' === $column_name ) {
				echo wp_kses(
					'<th scope="row" class="check-column">',
					array(
						'th' => array(
							'scope' => array(),
							'class' => array(),
						),
					)
				);
				echo wp_kses(
					$this->column_cb( $user ),
					array(
						'input' => array(
							'type'  => array(),
							'name'  => array(),
							'value' => array(),
						),
					)
				);
				echo wp_kses( '</th>', array( 'th' => array() ) );
			} elseif ( method_exists( $this, '_column_' . $column_name ) ) {
				echo wp_kses_post(
					call_user_func(
						array( $this, '_column_' . $column_name ),
						$user,
						$classes,
						$data,
						$primary
					)
				);
			} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo wp_kses_post( "<td $attributes>" );
				echo wp_kses_post( call_user_func( array( $this, 'column_' . $column_name ), $user ) );
				if ( $primary === $column_name ) {
					echo wp_kses_post( $this->row_actions( $actions ) );
				}
				echo wp_kses_post( '</td>' );
			} else {
				echo wp_kses_post( "<td $attributes>" );
				echo wp_kses_post( $this->column_default( $user, $column_name ) );
				if ( $primary === $column_name ) {
					echo wp_kses_post( $this->row_actions( $actions ) );
				}
				echo wp_kses_post( '</td>' );
			}
		}
	}

	/**
	 * WholesaleX User List
	 *
	 * @return void
	 */
	public function users_list() {
		/**
		 * Enqueue Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */
		wp_enqueue_script( 'wholesalex_header' );
		?>
		<div id="wholesalex_user_header"></div>
        
        
        <?php
        $action = isset($_GET['action'])?sanitize_text_field( $_GET['action'] ):'';

        

        switch ($action) {
            case 'import':
				if( current_user_can( 'administrator' )) {
					wp_enqueue_script('whx_user_import_export' );
					?>
					<div class="wholesalex-users">
						<div class="wholesalex-wrapper"> 
							<div class="wholesalex-editor__row wholesalex-editor__heading">
									<?php esc_html_e( 'WholesaleX User Import', 'wholesalex' ); ?>
							</div>
							<div id="wholesalex_user_import"></div>
						</div>
					</div>
					<?php
				}
                
                break;
            case 'export':
				if( current_user_can( 'administrator' )) {
					wp_enqueue_script('whx_user_import_export' );
					wp_localize_script( 'whx_user_import_export', 'whx_user_import_export', array('exportable_columns' => ImportExport::exportable_user_columns()) );
					
					?>
					<div class="wholesalex-users">
						<div class="wholesalex-wrapper"> 
							<div class="wholesalex-editor__row wholesalex-editor__heading">
									<?php esc_html_e( 'WholesaleX User Export', 'wholesalex' ); ?>
							</div>
							<div id="wholesalex_user_export"></div>
						</div>
					</div>
					<?php
				}
                
                break;
            default:
                $this->render_users();
                break;
        }
	}

    public function render_users() {
        if ( isset( $_POST['s'] ) && isset( $_POST['wholesalex_user_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['wholesalex_user_nonce'] ), 'wholesalex_users' ) ) {
			$this->prepare_items( sanitize_text_field( $_POST['s'] ) );
		} else {
			$this->prepare_items();
		}

        $url = 'admin.php?page=wholesalex-users';

        ?>
        <div class="wholesalex-users">
            <div class="wholesalex-wrapper">
                <div class="wholesalex-editor__row wholesalex-editor__heading">
                    <?php esc_html_e( 'WholesaleX Users', 'wholesalex' ); ?>
                    <span class="wholesalex_users_import_export">
                        <a href="<?php echo esc_url( add_query_arg(  array('page'=>'wholesalex-users','action'=>'import'), $url ) ); ?>"><button class="import-button">Import</button></a>
                        <a href="<?php echo esc_url( add_query_arg(  array('page'=>'wholesalex-users','action'=>'export'), $url ) ); ?>"><button class="export-button">Export</button></a>
                    </span>
                </div>
                <form method="post" class="wholesalex_users_table">
                    <input type="hidden" name="page" value="test_list_table">
                    <?php wp_nonce_field( 'wholesalex_users', 'wholesalex_user_nonce' ); ?>
                    <?php $this->views(); ?>
                    <?php $this->search_box( 'Search', 'search_id' ); ?>
                    <?php $this->display(); ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
	 * Return an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of roles and user count for that role for easy
	 * Filtersing of the user table.
	 *
	 * @since 1.1.9
	 *
	 * @global string $role
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 */
	protected function get_views() {
        $url = 'admin.php?page=wholesalex-users';
        $wholesalex_roles = wholesalex()->get_roles();
        $role = isset($_GET['role'])?sanitize_text_field( $_GET['role'] ):'';
		$role_links  = array();
		$all_text    = __( 'All' );

		$role_links['all'] = array(
			'url'     => $url,
			'label'   => $all_text,
			'current' => empty( $role ),
		);

		foreach ( wholesalex()->get_roles() as $this_role) {
			$role_links[ $this_role['id']] = array(
				'url'     => esc_url( add_query_arg( 'role', $this_role['id'], $url ) ),
				'label'   => $this_role['_role_title'],
				'current' =>  $this_role['id'] === $role,
			);
		}
		return $this->get_views_links( $role_links );
	}

    /**
	 * Filter Role Section
	 *
	 * @param WP_Query $query Query.
	 * @since 1.0.0
	 * @access public
	 */
	public function filter_user_section( $query ) {
		global $pagenow;
		$get_data = wholesalex()->sanitize( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification
		if ( is_admin() && 'admin.php' === $pagenow && 'wholesalex-users' === $get_data['page'] && isset($get_data['role']) && !empty($get_data['role']) ) {
			
            $selected_role = $get_data[ 'role' ]; // phpcs:ignore
            $meta_query    = array(
                array(
                    'key'     => '__wholesalex_role',
                    'value'   => $selected_role,
                    'compare' => '=',
                ),
            );
            $query->set( 'meta_key', '__wholesalex_role' );
            $query->set( 'meta_query', $meta_query );
		}
	}

}
