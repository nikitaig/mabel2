<?php
/**
 * Import Export Handler
 *
 * @package WholesaleX
 * @since 1.1.6
 */
namespace WHOLESALEX;

/**
 * WholesaleX Import Export Class
 */
class ImportExport {

	public function __construct() {
		add_action( 'wp_ajax_wholesalex_export_users_columns', array( $this, 'export_users_columns' ) );
		add_action( 'wp_ajax_wholesalex_export_users', array( $this, 'export_users' ) );
		add_action( 'wp_ajax_wholesalex_import_users', array( $this, 'import_users' ) );
		add_action( 'wp_ajax_wholesalex_process_import_users', array( $this, 'wholesalex_process_import_users' ) );
	}

	public static function get_instance() {
		$instance = new self();
		return $instance;
	}

	public static function exportable_user_columns() {
		return self::get_instance()->get_default_columns();
	}

	public function export_users_columns() {

		wp_send_json_success( $this->get_default_columns() );
	}

	/**
	 * Get Default User Columns List
	 *
	 * @since 1.1.6
	 */
	public function get_default_columns() {

		$columns = apply_filters(
			'wholesalex_user_export_default_columns',
			array(
				'user_id'                   => 'User ID',
				'username'                  => 'Username',
				'first_name'                => 'First Name',
				'last_name'                 => 'Last Name',
				'nickname'                  => 'Nickname',
				'display_name'              => 'Display Name',
				'email'                     => 'Email',
				'bio'                       => 'Biographical Info',
				'avatar'                    => 'Avatar',
				'password'                  => 'Password',
				'wholesalex_role'           => 'WholesaleX Role',
				'wholesalex_account_status' => 'WholesaleX Account Status',
				'billing_first_name'        => 'Billing: First Name',
				'billing_last_name'         => 'Billing: Last Name',
				'billing_company'           => 'Billing: Company',
				'billing_address_1'         => 'Billing: Address Line 1',
				'billing_city'              => 'Billing: City',
				'billing_post_code'         => 'Billing: Post Code/ZIP',
				'billing_country'           => 'Billing: Country/Region',
				'billing_state'             => 'Billing: State/County',
				'billing_phone'             => 'Billing: Phone',
				'shipping_first_name'       => 'Shipping: First Name',
				'shipping_last_name'        => 'Shipping: Last Name',
				'shipping_company'          => 'Shipping: Company',
				'shipping_address_1'        => 'Shipping: Address Line 1',
				'shipping_city'             => 'Shipping: City',
				'shipping_post_code'        => 'Shipping: Post Code/ZIP',
				'shipping_country'          => 'Shipping: Country/Region',
				'shipping_state'            => 'Shipping: State/County',
				'shipping_phone'            => 'Shipping: Phone',
			)
		);

		return $columns;
	}

	public function export_users() {
		$exportable_columns = isset( $_POST['columns'] ) ? json_decode( wp_unslash( $_POST['columns'] ), true ) : array();

		$data  = array();
		$users = get_users();
		foreach ( $users as $user ) {
			$user_data       = array();
			$user_data['id'] = $user->ID;
			foreach ( $exportable_columns as $column ) {
				switch ( $column ) {
					case 'user_id':
						$user_data[ $column ] = $user->ID;
						break;
					case 'username':
						$user_data[ $column ] = $user->user_login;
						break;
					case 'first_name':
						$user_data[ $column ] = $user->first_name;
						break;
					case 'last_name':
						$user_data[ $column ] = $user->last_name;
						break;
					case 'nickname':
						$user_data[ $column ] = $user->nickname;
						break;
					case 'display_name':
						$user_data[ $column ] = $user->display_name;
						break;
					case 'email':
						$user_data[ $column ] = $user->user_email;
						break;
					case 'bio':
						$user_data[ $column ] = $user->description;
						break;
					case 'avatar':
						$user_data[ $column ] = get_user_meta( $user->ID, 'avatar', true );
						break;
					case 'password':
						$user_data[ $column ] = '';
						break;
					case 'wholesalex_role':
						$user_data[ $column ] = get_user_meta( $user->ID, '__wholesalex_role', true );
						if ( is_numeric( $user_data[ $column ] ) ) {
							$user_data[ $column ] = 'whx_old:' . $user_data[ $column ];
						}
						break;
					case 'wholesalex_account_status':
						$user_data[ $column ] = get_user_meta( $user->ID, '__wholesalex_status', true );
						break;
					case 'billing_first_name':
					case 'billing_last_name':
					case 'billing_company':
					case 'billing_address_1':
					case 'billing_city':
					case 'billing_post_code':
					case 'billing_country':
					case 'billing_state':
					case 'shipping_first_name':
					case 'shipping_last_name':
					case 'shipping_company':
					case 'shipping_address_1':
					case 'shipping_city':
					case 'shipping_post_code':
					case 'shipping_country':
					case 'shipping_state':
						
						$user_data[ $column ] = get_user_meta( $user->ID, $column, true );
						if ( ! $user_data[ $column ] ) {
							$user_data[ $column ] = ' ';
						}
						// replace comma with dash
						$user_data[$column] = str_replace(",", "-", $user_data[$column] );
						break;
				}
			}
			$user_data = apply_filters( 'wholesalex_user_export_column_data', $user_data );
			$data[]    = $user_data;
		}
		$csvData = '';

		// Add column headers to the CSV data
		$csvData .= implode( ',', $exportable_columns ) . "\n";

		// Add data rows to the CSV data
		foreach ( $data as $row ) {
			$rowData = array();
			foreach ( $exportable_columns as $column ) {
				$rowData[] = $row[ $column ];
			}
			$csvData .= implode( ',', $rowData ) . "\n";
		}

		wp_send_json_success( $csvData );

	}



	/**
	 * Save csv file
	 *
	 * @param [type] $file
	 * @return void
	 */
	public function save_csv_file( $file ) {
		$upload_dir = wp_upload_dir(); // WordPress upload directory

		// wholesalex custom import csv folder
		$target_dir = $upload_dir['basedir'] . '/wholesalex_import_data/';

		// Check if the target directory exists
		if ( file_exists( $target_dir ) && is_dir( $target_dir ) ) {
			$specific_file = $target_dir . 'wholesalex_users.csv';
			if ( file_exists( $specific_file ) ) {
				unlink( $specific_file );
				delete_option( '__wholesalex_customer_import_export_stats' );
			}
		} else {
			// Create the directory if it doesn't exist
			mkdir( $target_dir, 0755, true );
		}

		$file_name = 'wholesalex_users.csv'; // Specify the new file name

		// Upload the CSV file
		$target_file    = $target_dir . $file_name;
		$upload_success = move_uploaded_file( $file['tmp_name'], $target_file );

		if ( $upload_success ) {
			// count csv row and update in db
			$file_path                = $upload_dir['basedir'] . '/wholesalex_import_data/' . $file_name;
			$row_count                = $this->count_and_filter_csv( $file_path );
			$stats                    = get_option( '__wholesalex_customer_import_export_stats', array() );
			$stats['total']       = $row_count;
			$stats['process']         = 0;
			$stats['update_existing'] = isset( $_POST['update_existing'] ) ? sanitize_text_field( $_POST['update_existing'] ) : 'no';
			$stats['process_per_iteration'] = isset($_POST['process_per_iteration'])? sanitize_text_field($_POST['process_per_iteration']):10;
			if($stats['update_existing']) {
				$stats['find_user_by'] = isset($_POST['find_user_by'])? sanitize_text_field($_POST['find_user_by']):'username';
				// $stats['is_update_username'] = isset($_POST['is_update_username'])? sanitize_text_field($_POST['is_update_username']):'no';
			}
			$stats['log'] = '';
			update_option( '__wholesalex_customer_import_export_stats', $stats );
		}

		return $upload_success;
	}


	/**
	 * Upload csv file
	 *
	 * @return void
	 */
	public function import_users() {
		$response = array(
			'log'             => '',
			'message'         => 'You must upload a valid csv file to import users',
			'insert_count'    => 0,
			'update_count'    => 0,
			'skipped_count'   => 0,
			'total'           => 0,
			'process'         => 0,
			'update_existing' => 'no',
		);
		if ( isset( $_FILES['file'] ) && ! empty( $_FILES['file'] ) ) {
			$file           = $_FILES['file'];
			$file_extension = pathinfo( $file['name'], PATHINFO_EXTENSION );
			if ( $file_extension !== 'csv' ) {
				return wp_send_json_success( $response );
			}

			// valid csv
			// upload this csv
			$upload_status = $this->save_csv_file( $file );

			if ( $upload_status ) {
				$import_stats = get_option( '__wholesalex_customer_import_export_stats', array() );
				// user data upload successful
				$response['total']   = isset( $import_stats['total'] ) ? $import_stats['total'] : 0;
				$response['process'] = isset( $import_stats['process'] ) ? $import_stats['process'] : 0;
				$response['message'] = '';
			}

			wp_send_json_success( $response );
		}

		wp_send_json_success( $response );

	}

	/**
	 * Filter empty row from csv and cound valid row
	 *
	 * @param [type] $file_path
	 * @return void
	 */
	public function count_and_filter_csv( $file_path ) {
		$row_count = -1;
		if ( ( $handle = fopen( $file_path, 'r+' ) ) !== false ) {
			$columns = fgetcsv( $handle );

			$mapped_column = array_flip( $columns );
			while ( ( $data = fgetcsv( $handle ) ) !== false ) {
				$email = $data[ $mapped_column['email'] ];

				if (!empty(array_filter($data)) || empty($email)) {
					$row_count++;
				}
			}
			fclose( $handle );
		}

		return $row_count;
	}


	public function wholesalex_process_import_users() {
		add_filter( 'send_password_change_email', '__return_false' );
		add_filter( 'woocommerce_email_change_notification', '__return_false' );

		$stats = get_option('__wholesalex_customer_import_export_stats',array());

		$max_process = isset($stats['process_per_iteration'])? $stats['process_per_iteration']:10;
		// Check if a previous end position is stored
		$startFrom        = isset($stats['previous_position'])? $stats['previous_position']:1;
		$current_position = isset($stats['current_position'])? $stats['current_position']:1;

		$is_update = isset( $_POST['update_existing'] ) ? 'yes' === $_POST['update_existing'] : false;

		$response   = array(
			'log'             => isset($stats['log'])?$stats['log']:'' ,
			'message'         => '',
			'insert_count'    => isset($stats['insert_count'])?$stats['insert_count']:0 ,
			'update_count'    => isset($stats['update_count'])?$stats['update_count']:0 ,
			'skipped_count'   => isset($stats['skipped_count'])?$stats['skipped_count']:0 ,
			'total'           => isset($stats['total'])?$stats['total']:0 ,
			'process'         => isset($stats['process'])?$stats['process']:0,
		);
		$upload_dir = wp_upload_dir(); // WordPress upload directory

		// wholesalex custom import csv folder
		$file_path = $upload_dir['basedir'] . '/wholesalex_import_data/wholesalex_users.csv';
		if ( ( $handle = fopen( $file_path, 'r' ) ) !== false ) {

			// Get the length of the first row
			
			$columns = fgetcsv( $handle );

			$mapped_column = array_flip( $columns );
			$log           = '';
			$row_count     = isset($stats['row_count'])?$stats['row_count']:1;

			if(1==$startFrom) {
				// Set the file pointer to the last processed position
				fseek( $handle,ftell( $handle ));

			} else {
				fseek( $handle, $startFrom );
			}

			while ( ( $data = fgetcsv( $handle ) ) !== false ) {
				$current_position = ftell( $handle );
				$username         = $data[ $mapped_column['username'] ];
				$email            = $data[ $mapped_column['email'] ];
				$password         = $data[ $mapped_column['password'] ];

				$log .= "Row $row_count: ";
				$row_count++;

				if ( empty( $email ) ) {
					$response['skipped_count']++;
					$log .= "Email is mandatory! , Skipped\n";
					continue;
				}

				$first_name   = $data[ $mapped_column['first_name'] ];
				$last_name    = $data[ $mapped_column['last_name'] ];
				$display_name = $data[ $mapped_column['display_name'] ];

				if ( ! is_email( $email ) ) {
					$errors[] = array( "$email is not a Valid Email!" );
					$log     .= "$email is not a Valid Email! , Skipped\n";
					$response['skipped_count']++;
					$response['process']++;
					continue;
				}

				$is_username_exist = username_exists( $username );
				$is_email_exist    = email_exists( $email );


				if ( $is_update ) {
					$flag  = false;
					$find_user_by = isset($stats['find_user_by'])?$stats['find_user_by']:'username';
					switch ($find_user_by) {
						case 'username':
							$user = get_user_by('login',$username);
							if ( $user && $is_username_exist ) {
								$log .= "$username found!. ";
							} else {
								$log .= "$username Not found!. ";
								$response['process']++;
								$response['skipped_count']++;
								$flag = true;
								break;
							}
							break;
							case 'email':								
								$user = get_user_by('email',$email);
								if ( $user && $is_email_exist ) {
									$log .= "$email found!. ";
								} else {
									$log .= "$email Not found!. ";
									$response['process']++;
									$response['skipped_count']++;
									$flag = true;
									break;
								}
							
							break;
						
						default:
						$flag  = true;
							break;
					}
					if($flag) {
						continue;
					}
					// update existing users
					
				} else {
					if($is_email_exist){
						$log .= "Skipped\n";
						$response['skipped_count']++;
						$response['process']++;
						continue;
					}
					$user_id = wc_create_new_customer( $email, $username, $password );

					if ( is_wp_error( $user_id ) ) {
						$__error_messages = $user_id->get_error_messages();
						$log.= 'User Created Failed. Errors: ';
						foreach ( $__error_messages as $error_message ) {
							$errors[] = $error_message;
							$log.= $error_message . ',';
						}

						$log .= "Skipped\n";
						$response['skipped_count']++;
						$response['process']++;
						continue;
					} else {
						$log .= "User Created. User ID $user_id ";
						$response['insert_count']++;
						$response['process']++;
					}
					$created[] = $user_id;
					$user      = get_userdata( $user_id );
				}

				if ( ! is_object( $user ) ) {
					$log .= "User Object Not Found.\n";
					$response['skipped_count']++;
					$response['process']++;
					continue;
				}

				$user_data       = array();
				$user_data['ID'] = $user->ID;

				if ( !email_exists($email) && $email != $user->user_email ) {
					$user_data['user_email'] = $email;
					$log .= "$username Email Updated. ";
				}

				if ( ! empty( $password ) && $is_update ) {
					$user_data['user_pass'] = $password;
					// wp_set_password( $password, $user->ID );
					$log .= 'Password Updated.';
				}

				if ( ! empty( $first_name ) ) {
					$user_data['first_name'] = $first_name;
					// update_user_meta( $user->ID, 'first_name', $first_name );
					$log .= 'First Name Updated.';
				}
				if ( ! empty( $last_name ) ) {
					$user_data['last_name'] = $last_name;
					// update_user_meta( $user->ID, 'last_name', $last_name );
					$log .= 'Last Name Updated.';
				}
				if ( ! empty( $display_name ) ) {
					// wp_update_user(
					// array(
					// 'ID'           => $user->ID,
					// 'display_name' => $display_name,
					// )
					// );
					$user_data['display_name'] = $display_name;
					$log                      .= 'Display Name Updated.';

				}
				if ( ! empty( $data[ $mapped_column['nickname'] ] ) ) {
					$user_data['user_nicename'] = $data[ $mapped_column['nickname'] ];
					// update_user_meta( $user->ID, 'nickname', $data[ $mapped_column['nickname'] ] );
					$log .= 'Nickname Updated.';

				}
				if ( ! empty( $data[ $mapped_column['bio'] ] ) ) {
					$user_data['description'] = $data[ $mapped_column['bio'] ];
					// update_user_meta( $user->ID, 'description', $data[ $mapped_column['bio'] ] );
					$log .= 'Bio Updated.';
				}
				if ( ! empty( $data[ $mapped_column['avatar'] ] ) ) {
					$user_data['avatar'] = $data[ $mapped_column['avatar'] ];
					// update_user_meta( $user->ID, 'avatar', $data[ $mapped_column['avatar'] ] );
					$log .= 'Avatar Updated.';

				}

				if ( sizeof( $user_data ) > 1 ) {
					wp_update_user( $user_data );
					if($is_update) {
						$response['process']++;
						$response['update_count']++;
					}
				}
				if (! empty( $data[ $mapped_column['billing_first_name'] ] ) && $data[ $mapped_column['billing_first_name'] ] != get_user_meta( $user->ID,'billing_first_name',true) ) {
					update_user_meta( $user->ID, 'billing_first_name', $data[ $mapped_column['billing_first_name'] ] );
					$log .= 'Billing First Name Updated.';
				}
				if ( ! empty( $data[ $mapped_column['billing_last_name'] ] ) && $data[ $mapped_column['billing_last_name'] ] != get_user_meta( $user->ID,'billing_last_name',true)  ) {
					update_user_meta( $user->ID, 'billing_last_name', $data[ $mapped_column['billing_last_name'] ] );
					$log .= 'Billing Last Name Updated.';
				}
				if ( ! empty( $data[ $mapped_column['billing_company'] ] ) && $data[ $mapped_column['billing_company'] ] != get_user_meta( $user->ID,'billing_company',true)   ) {
					update_user_meta( $user->ID, 'billing_company', $data[ $mapped_column['billing_company'] ] );
					$log .= 'Billing Company Updated.';
				}
				if ( ! empty( $data[ $mapped_column['billing_address_1'] ] ) && $data[ $mapped_column['billing_address_1'] ] != get_user_meta( $user->ID,'billing_address_1',true)  ) {
					update_user_meta( $user->ID, 'billing_address_1', $data[ $mapped_column['billing_address_1'] ] );
					$log .= 'Billing Address 1 Updated.';
				}
				if ( ! empty( $data[ $mapped_column['billing_city'] ] ) && $data[ $mapped_column['billing_city'] ] != get_user_meta( $user->ID,'billing_city',true)   ) {
					update_user_meta( $user->ID, 'billing_city', $data[ $mapped_column['billing_city'] ] );
					$log .= 'Billing City Updated.';
				}
				if ( ! empty( $data[ $mapped_column['billing_post_code'] ] ) && $data[ $mapped_column['billing_post_code'] ] != get_user_meta( $user->ID,'billing_post_code',true) ) {
					update_user_meta( $user->ID, 'billing_post_code', $data[ $mapped_column['billing_post_code'] ] );
					$log .= 'Billing Postcode Updated.';

				}
				if ( ! empty( $data[ $mapped_column['billing_country'] ] ) && $data[ $mapped_column['billing_country'] ] != get_user_meta( $user->ID,'billing_country',true) ) {
					update_user_meta( $user->ID, 'billing_country', $data[ $mapped_column['billing_country'] ] );
					$log .= 'Billing Country Updated.';

				}
				if ( ! empty( $data[ $mapped_column['billing_state'] ] ) && $data[ $mapped_column['billing_state'] ] != get_user_meta( $user->ID,'billing_state',true)  ) {
					update_user_meta( $user->ID, 'billing_state', $data[ $mapped_column['billing_state'] ] );
					$log .= 'Billing State Updated.';

				}
				if ( ! empty( $data[ $mapped_column['billing_phone'] ] ) && $data[ $mapped_column['billing_phone'] ] != get_user_meta( $user->ID,'billing_phone',true)   ) {
					update_user_meta( $user->ID, 'billing_phone', $data[ $mapped_column['billing_phone'] ] );
					$log .= 'Billing Phone Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_first_name'] ] )  && $data[ $mapped_column['shipping_first_name'] ] != get_user_meta( $user->ID,'shipping_first_name',true)    ) {
					update_user_meta( $user->ID, 'shipping_first_name', $data[ $mapped_column['shipping_first_name'] ] );
					$log .= 'Shipping First Name Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_last_name'] ] ) && $data[ $mapped_column['shipping_last_name'] ] != get_user_meta( $user->ID,'shipping_last_name',true) ) {
					update_user_meta( $user->ID, 'shipping_last_name', $data[ $mapped_column['shipping_last_name'] ] );
					$log .= 'Shipping Last Name Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_company'] ] ) && $data[ $mapped_column['shipping_company'] ] != get_user_meta( $user->ID,'shipping_company',true) ) {
					update_user_meta( $user->ID, 'shipping_company', $data[ $mapped_column['shipping_company'] ] );
					$log .= 'Shipping Company Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_address_1'] ] )  && $data[ $mapped_column['shipping_address_1'] ] != get_user_meta( $user->ID,'shipping_address_1',true) ) {
					update_user_meta( $user->ID, 'shipping_address_1', $data[ $mapped_column['shipping_address_1'] ] );
					$log .= 'Shipping Address 1 Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_city'] ] )  && $data[ $mapped_column['shipping_city'] ] != get_user_meta( $user->ID,'shipping_city',true) ) {
					update_user_meta( $user->ID, 'shipping_city', $data[ $mapped_column['shipping_city'] ] );
					$log .= 'Shipping City Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_post_code'] ] ) && $data[ $mapped_column['shipping_post_code'] ] != get_user_meta( $user->ID,'shipping_post_code',true)  ) {
					update_user_meta( $user->ID, 'shipping_post_code', $data[ $mapped_column['shipping_post_code'] ] );
					$log .= 'Shipping PostCode Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_country'] ] ) && $data[ $mapped_column['shipping_country'] ] != get_user_meta( $user->ID,'shipping_country',true) ) {
					update_user_meta( $user->ID, 'shipping_country', $data[ $mapped_column['shipping_country'] ] );
					$log .= 'Shipping Country Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_state'] ] ) && $data[ $mapped_column['shipping_state'] ] != get_user_meta( $user->ID,'shipping_state',true)  ) {
					update_user_meta( $user->ID, 'shipping_state', $data[ $mapped_column['shipping_state'] ] );
					$log .= 'Shipping State Updated.';

				}
				if ( ! empty( $data[ $mapped_column['shipping_phone'] ] ) && $data[ $mapped_column['shipping_phone'] ] != get_user_meta( $user->ID,'shipping_phone',true)   ) {
					update_user_meta( $user->ID, 'shipping_phone', $data[ $mapped_column['shipping_phone'] ] );
					$log .= 'Shipping Phone Updated.';
				}
				if ( ! empty( $data[ $mapped_column['wholesalex_role'] ] )  ) {
					$temp = explode( ':', $data[ $mapped_column['wholesalex_role'] ] );

					if ( 'whx_old' == $temp[0] ) {
						$data[ $mapped_column['wholesalex_role'] ] = $temp[1];
					}
				}

				if ( ! empty( $data[ $mapped_column['wholesalex_role'] ] ) && get_user_meta( $user->ID, '__wholesalex_role', true ) !== $data[ $mapped_column['wholesalex_role'] ] ) {
					wholesalex()->change_role( $user->ID, $data[ $mapped_column['wholesalex_role'] ] );
					$log .= 'WholesaleX Role Updated.';
				}
				if ( ! empty( $data[ $mapped_column['wholesalex_account_status'] ] ) && get_user_meta( $user->ID, '__wholesalex_status', true ) !== $data[ $mapped_column['wholesalex_account_status'] ] ) {
					update_user_meta( $user->ID, '__wholesalex_status', $data[ $mapped_column['wholesalex_account_status'] ] );
					$log .= 'WholesaleX Account Status Updated.';
				}

				$log = apply_filters( 'wholesalex_customer_import_export_log', $log, $is_update );

				do_action( 'wholesalex_import_userdata', $user->ID, $data, $mapped_column, $is_update );

				if ( ($row_count-1) >= $max_process ) {
					break;
				}
			}

			$stats['insert_count']      = $response['insert_count'];
			$stats['update_count']      = $response['update_count'];
			$stats['previous_position'] = $current_position;
			$stats['skipped_count']     = $response['skipped_count'];
			$stats['process']           = $response['process'];
			$stats['log'].=$log;

			$response['total'] = $stats['total'];

			update_option( '__wholesalex_customer_import_export_stats', $stats );

			$response['log'] = $stats['log'];

			fclose( $handle );
		}

		wp_send_json_success( $response );

	}
}
