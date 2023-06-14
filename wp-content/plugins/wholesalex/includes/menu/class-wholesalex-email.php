<?php
/**
 * WholesaleX Email Template
 *
 * @package WHOLESALEX
 * @since 1.0.0
 */

namespace WHOLESALEX;

/**
 * WholesaleX Email Class
 */
class WHOLESALEX_Email {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wholesalex_email_submenu_page' ) );
		add_action( 'wp_ajax_save_wholesalex_email_settings', array( $this, 'save_wholesalex_email_settings' ) );
		add_action( 'wp_ajax_save_wholesalex_email_template', array( $this, 'save_wholesalex_email_template' ) );
	}

	/**
	 * WholesaleX Email Add Submenu Page
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wholesalex_email_submenu_page() {
		add_submenu_page(
			'wholesalex-overview',
			__( 'Emails', 'wholesalex' ),
			__( 'Emails', 'wholesalex' ),
			'manage_options',
			'wholesalex-email',
			array( $this, 'wholesalex_email_submenu_page_callback' )
		);
	}

	/**
	 * WholesaleX Email Submenu Page Content
	 *
	 * @since 1.0.0
	 * @since 1.0.6 Default Email Template Save Issue Fixed.
	 */
	public function wholesalex_email_submenu_page_callback() {

		$__is_pro_active = wholesalex()->is_pro_active();

		$initial_emails = apply_filters(
			'wholesalex_email_templates',
			array(
				'new_wholesalex_user'             => array(
					'path'       => 'new_wholesalex_user',
					'title'      => __( 'WholesaleX: New User', 'wholesalex' ),
					'is_pro'     => false,
					'subject'    => 'Thank you for your registration',
					'email_type' => 'text/html',
					'content'    => 'Hi {first_name},

								Thank you for the registration request on {site_name}. Your registration will be manually reviewed. You will get another mail very soon once your request is approved or declined.
								Regards,
								
								{admin_name}
								{site_name}',
				),
				'wholesalex_registration_approve' => array(
					'path'       => 'wholesalex_registration_approve',
					'title'      => __( 'WholesaleX: Registration Approve', 'wholesalex' ),
					'is_pro'     => false,
					'subject'    => 'Registration Request Approved',
					'email_type' => 'text/html',
					'content'    => 'Hi {first_name},

								Thank you for the registration request on {site_name}. Your registration request has been approved. Now, you can log in to “Site Name” and place your first order.
								
								Regards,
								{admin_name}
								{site_name}',
				),
				'wholesalex_registration_decline' => array(
					'path'       => 'wholesalex_registration_decline',
					'title'      => __( 'WholesaleX: Registration Request Declined', 'wholesalex' ),
					'is_pro'     => true,
					'subject'    => 'Registration Request Declined',
					'email_type' => 'text/html',
					'content'    => 'Hi {first_name},

								Thank you for the registration request on {site_name}. Unfortunately, your registration request has been declined. 
								
								Regards,
								{admin_name}
								{site_name}',
				),
				'wholesalex_registration_pending' => array(
					'path'       => 'wholesalex_registration_pending',
					'title'      => __( 'WholesaleX: Registration Pending', 'wholesalex' ),
					'is_pro'     => true,
					'subject'    => 'Registration Request Pending',
					'email_type' => 'text/html',
					'content'    => 'Hi {first_name},

								Thank you for the registration request on {site_name}. The request status is pending. You will get a confirmation mail once the request is confirmed.
								
								Regards,
								{admin_name}
								{site_name}',
				),
				'wholesalex_email_verification'   => array(
					'path'       => 'wholesalex_email_verification',
					'title'      => __( 'WholesaleX: Email Verification', 'wholesalex' ),
					'is_pro'     => false,
					'subject'    => 'Confirmation Mail for Registration Request',
					'email_type' => 'text/html',
					'content'    => 'Hi {first_name},

								Thank you for the registration request on {site_name}. Please click on the confirmation link {confirmation_url}
								
								Your request will be manually reviewed. You will get a confirmation mail once your request is approved.
								
								Regards,
								{admin_name}
								{site_name}',
				),
			)
		);

		/**
		 * Save Deafult Email Templates
		 *
		 * @since 1.0.6
		 */
		$saved_emails = get_option( '__wholesalex_email_templates', false );
		if ( ! $saved_emails ) {
			update_option( '__wholesalex_email_templates', $initial_emails );
		} else {
			$flag = false;
			foreach ( $initial_emails as $key => $value ) {
				if ( ! isset( $saved_emails[ $key ] ) ) {
					$saved_emails[ $key ] = $value;
					$flag                 = true;
				} else {
					if ( isset( $saved_emails[ $key ]['email_type'] ) && 'html' === $saved_emails[ $key ]['email_type'] ) {
						$saved_emails[ $key ]['email_type'] = 'text/html';
						$flag                               = true;
					} elseif ( ! isset( $saved_emails[ $key ]['email_type'] ) ) {
						$saved_emails[ $key ]['email_type'] = 'text/html';
						$flag                               = true;
					}
				}
			}
			if ( $flag ) {
				update_option( '__wholesalex_email_templates', $saved_emails );
			}
		}

		/**
		 * Enqueue Header Script
		 *
		 * @since 1.1.0 Enqueue Script (Reconfigure Build File)
		 */

		wp_enqueue_script( 'wholesalex_header' );

		echo '<div id="wholesalex_email_header"></div>';
		echo '<div class="wholesalex-email">';

		$get_data = wholesalex()->sanitize( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $get_data['section'] ) ) {
			if ( isset( $get_data['_wpnonce'] ) && wp_verify_nonce( $get_data['_wpnonce'], $get_data['section'] ) ) {
				foreach ( $initial_emails as $email ) {
					if ( isset( $email['title'] ) && $get_data['section'] === $email['path'] ) {
						$this->wholesalex_email_template_generate( $get_data['section'], $email['title'], $email['subject'], $email['content'] );
					}
				}
			} else {
				die( 'Nonce Verification Failed!!!' );
			}
		} else {
			$wholesalex_email_templates = get_option( '__wholesalex_email_templates', array() );
			?>
			<div class="wholesalex-editor">
				<div class="wholesalex-editor__row wholesalex-editor__heading">
					<?php _e( 'WholesaleX Emails', 'wholesalex' ); ?>
				</div>
				<div class="wholesalex_email_list">
					<table class="form-table wc_emails widefat wholesalex-email">
						<thead>
							<tr>
								<th class="wc-email-settings-table-status" style="width:15%;"><?php esc_html_e( 'Status', 'wholesalex' ); ?></th>
								<th class="wc-email-settings-table-name"><?php esc_html_e( 'Email', 'wholesalex' ); ?></th>
								<th class="wc-email-settings-table-email_type" style="width:15%;"><?php esc_html_e( 'Content Type', 'wholesalex' ); ?></th>
							</tr>
						</thead>

						<tbody>
						<?php
						foreach ( $initial_emails as $email ) {
							$__is_pro = isset( $email['is_pro'] ) ? $email['is_pro'] : '';
							$plan     = isset( $email['plan'] ) ? $email['plan'] : '';
							if ( function_exists( 'wholesalex_pro' ) && version_compare( WHOLESALEX_PRO_VER, '1.0.6', '>=' ) ) {
								if ( method_exists( wholesalex_pro(), 'get_eligible_license_plans' ) ) {
									$eligible_plans  = wholesalex_pro()->get_eligible_license_plans();
									$__is_pro_active = $__is_pro && isset( $eligible_plans[ $plan ] );
								} else {
									$__is_pro_active = true;
								}
							}
							$get_addon_text = $plan ? 'Get ' . $plan : __( 'Get Pro', 'wholesalex' );
							$is_lock_email  = $__is_pro && ! $__is_pro_active;
							?>
								<tr>
									<td class="wc-email-settings-table-name">
									<div class="wsx-control-option">
												<?php if ( $is_lock_email ) { ?>
													<input type="checkbox" disabled id="<?php echo esc_attr( $email['path'] . '_email_status' ); ?>" class="wsx-email-enable" name="<?php echo esc_attr( $email['path'] . '_email_status' ); ?>"  <?php echo esc_attr( get_option( $email['path'] . '_email_status' ) ? '' : '' ); ?> />
												<?php } else { ?>
													<input type="checkbox"  id=<?php echo esc_attr( $email['path'] . '_email_status' ); ?> class="wsx-email-enable" name=<?php echo esc_attr( $email['path'] . '_email_status' ); ?>  <?php echo esc_attr( get_option( $email['path'] . '_email_status' ) ? 'checked' : '' ); ?> />
												<?php } ?>
												<?php if ( ! $is_lock_email ) { ?>
													<label for="<?php echo esc_attr( $email['path'] . '_email_status' ); ?>">
												</label>												
												<?php } else { ?>
													<label for="<?php echo esc_attr( $email['path'] . '_email_status' ); ?>" onclick="openWholesaleXGetProPopUp()">
														<span class="wholesalex-lock-icon dashicons dashicons-lock"></span>
												</label>
												<?php } ?>
											</div>
									</td>
									<td>
										<?php
										if ( $__is_pro && ! $__is_pro_active ) {
											echo '<p>' . esc_html( $email['title'] ) . '</p>';
										} else {
											?>
												<a href="<?php echo esc_attr( wp_nonce_url( 'admin.php?page=wholesalex-email&section=' . $email['path'], $email['path'] ) ); ?>"><?php echo esc_html( $email['title'] ); ?></a>
											<?php
										}
										?>
									</td>
									<td class="wc-email-settings-table-email_type">
									<?php echo esc_html( isset( $wholesalex_email_templates[ $email['path'] ]['email_type'] ) ? $wholesalex_email_templates[ $email['path'] ]['email_type'] : 'text' ); ?>
									</td>
								</tr>
								<?php
						}
						?>

						</tbody>

						<tfoot>
							<tr>
								<th class="wc-email-settings-table-status"> <?php esc_html_e( 'Status', 'wholesalex' ); ?></th>
								<th class="wc-email-settings-table-name"><?php esc_html_e( 'Email', 'wholesalex' ); ?></th>
								<th class="wc-email-settings-table-email_type"><?php esc_html_e( 'Content Type', 'wholesalex' ); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<?php wholesalex()->get_upgrade_pro_popup_html( __( 'Unlock Full Email Access', 'wholesalex' ), '', __( 'We are sorry, but unfortunately, only a limited number of emails are available on the free version. Please get the pro version to unlock all email templates.', 'wholesalex' ), 'https://getwholesalex.com/pricing/?utm_source=wholesalex-menu&utm_medium=email-get_pro&utm_campaign=wholesalex-DB' ); ?>
			</div>
			<?php
		}
		echo '</div>';
		$this->wholesalex_email_js();
		$this->wholesalex_email_css();
	}

	/**
	 * Generate Email Template
	 *
	 * @param string $_template_name Template Name.
	 * @param string $title Email Title.
	 * @param string $subject Email Subject.
	 * @param string $content Email Content.
	 * @return void
	 * @since 1.0.0
	 */
	public function wholesalex_email_template_generate( $_template_name, $title, $subject, $content ) {
		$wholesalex_email_templates = get_option( '__wholesalex_email_templates', array() );
		if ( ! $wholesalex_email_templates ) {
			$wholesalex_email_templates = array();
		}
		$blank_email = array(
			'admin_notification' => false,
			'subject'            => $subject,
			'content'            => $content,
			'email_type'         => 'text/html',
		);
		if ( ! isset( $wholesalex_email_templates[ $_template_name ] ) ) {
			$wholesalex_email_templates[ $_template_name ] = $blank_email;
		}
		$template = $wholesalex_email_templates[ $_template_name ];
		?>

		<form id="wholesalex_email_template_editor" method="post" class="wholesalex_email_template">
			<?php wp_nonce_field( 'save_wholesalex_email_template' ); ?>
			<input type="hidden" name="action" value="save_wholesalex_email_template" />
			<input type="hidden" name="template_name" value=<?php echo esc_attr( $_template_name ); ?>>

			<div class="wholesalex-editor__row wholesalex-editor__heading">
				<?php esc_html_e( 'Email Configuration', 'wholesalex' ); ?>
			</div>

			<div class="wholesalex-editor__label"><h2><?php echo esc_html( $title ); ?></h2></div>

			<?php if ( 'new_wholesalex_user' === $_template_name ) { ?>
				<table>
					<tbody>
						<tr>
							<th><?php esc_html_e( 'Admin Email Recipient', 'wholesalex' ); ?> </th>
							<td><input type="email" value=<?php echo esc_attr( get_option( 'admin_email' ) ); ?> /> </td>
						</tr>
					</tbody>
				</table>
			<?php } ?>

			<table>
				<tbody>					
					<?php if ( 'new_wholesalex_user' === $_template_name ) { ?>
						<tr>
							<th> <?php esc_html_e( 'Enable Admin Notification', 'wholesalex' ); ?> </th>
							<td><input type="checkbox" name="wholesalex_enable_admin_notification" <?php echo esc_attr( isset( $template['admin_notification'] ) ? 'checked' : '' ); ?>></td>
						</tr>
					<?php } ?>
					<tr>
						<th><?php esc_html_e( 'Subject', 'wholesalex' ); ?> </th>
						<td><input type="text" id="<?php echo esc_attr( 'subject' ); ?>" name="<?php echo esc_attr( 'subject' ); ?>" value="<?php echo esc_attr( $template['subject'] ); ?>"> </td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Email Content', 'wholesalex' ); ?> </th>
						<td>
							<?php
							wp_editor( $template['content'], 'content' );
							?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<div id="wholesalex_smart_tags_toogle_button"><?php esc_html_e( 'Smart Tag Used', 'wholesalex' ); ?></div>
							<div id="wholesalex_smart_tags" style="display: none;">
								<?php
									$smart_tags = apply_filters(
										'wholesalex_email_smart_tags',
										array(
											'date'         => __( 'Show Current Date inside the Email.', 'wholesalex' ),
											'username'     => __( 'Show the Username of the Register User.', 'wholesalex' ),
											'email'        => __( 'Show the Email Address of the User.', 'wholesalex' ),
											'display_name' => __( 'Show the Display Name of the User.', 'wholesalex' ),
											'first_name'   => __( 'Show the First Name of the User.', 'wholesalex' ),
											'last_name'    => __( 'Show the Last of the User.', 'wholesalex' ),
											'super_admin_name' => __( 'Show the Super Admin Display Name.', 'wholesalex' ),
											'site_name'    => __( 'Show the Site Name.', 'wholesalex' ),
											'super_admin_name' => __( 'Show the Super Admin Display Name.', 'wholesalex' ),
											'email_confirmation_link' => __( 'Show the email confirmation link.', 'wholesalex' ),
										),
										$_template_name
									);
								?>
								<ul>
									<?php
									if ( is_array( $smart_tags ) ) {
										foreach ( $smart_tags as $tag => $desc ) {
											?>
												<li><code>{<?php echo esc_html( $tag ); ?>}</code> : <?php echo esc_html( $desc ); ?></li>
											<?php
										}
									}
									?>
								</ul>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<?php esc_html_e( 'Email Type', 'wholesalex' ); ?>
						</th>
						<td>
							<select name="email_type" id="email_type">
								<option value="text/html" <?php echo 'text/html' === $template['email_type'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( 'HTML', 'wholesalex' ); ?></option>
								<option value="text/plain" <?php echo 'text/plain' === $template['email_type'] ? esc_attr( 'selected' ) : ''; ?>><?php esc_html_e( 'Plain Text', 'wholesalex' ); ?></option>
							</select>
						</td>

					</tr>
				</tbody>
			</table>
			<div id="message" class="updated inline" style="display: none;">
				<p><strong><?php esc_html_e( 'Email Template have been saved.', 'wholesalex' ); ?></strong></p>
			</div>
			<br/>
			<input class="button button-primary" onclick="saveEmailTemplate(event)" type="submit" value="Save Changes">
		</form>
		<?php
	}

	/**
	 * Save WholesaleX Email Template
	 *
	 * @since 1.0.0
	 */
	public function save_wholesalex_email_template() {
		if ( ! ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'save_wholesalex_email_template' ) ) ) {
			die( 'Nonce Verification Faild!' );
		}

		$wholesalex_email_templates = get_option( '__wholesalex_email_templates' );
		if ( ! $wholesalex_email_templates ) {
			$wholesalex_email_templates = array();
		}
		$updated_email = array(
			'admin_notification' => isset( $_POST['wholesalex_enable_admin_notification'] ) ? sanitize_text_field( $_POST['wholesalex_enable_admin_notification'] ) : '',
			'subject'            => isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '',
			'content'            => isset( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '',
			'email_type'         => isset( $_POST['email_type'] ) ? sanitize_title( $_POST['email_type'] ) : '',
		);
		if ( isset( $_POST['template_name'] ) ) {
			$wholesalex_email_templates[ sanitize_text_field( $_POST['template_name'] ) ] = $updated_email;
			update_option( '__wholesalex_email_templates', $wholesalex_email_templates );
		}

		wp_send_json_success( $wholesalex_email_templates );
	}

	/**
	 * CSS For WholesaleX Email
	 *
	 * @return void
	 */
	public function wholesalex_email_css() {
		?>
		<style>

			.wholesalex-email {
				padding-top: 80px;
				padding-left: 25px;
			}
			form#wholesalex_email_template_editor {
				padding-top: 25px;
			}

			#wp-content-editor-tools {
				padding: 0px;
				background-color: transparent;
			}

			th {
				font-size: var(--wholesalex-size-16);
				line-height: 22px;
				color: var(--wholesalex-heading-color);
				text-align: left;
				width: 18vw;
				font-weight: 500;
				vertical-align: top;
			}

			th,
			td {
				padding: 10px 0px;
			}

			table {
				width: 100%;
				padding-right: 7vw;
			}

			td {
				text-align: left;
			}

			input[type="email"] {
				width: 100%;
			}

			input#subject {
				width: 100%;
			}

			#wholesalex_smart_tags_toogle_button {
				font-size: var(--wholesalex-size-16);
				color: #1465a5;
				line-height: 22px;
				text-decoration: underline;
			}

			#wholesalex_smart_tags_toogle_button:hover {
				cursor: pointer;
			}

			#message {
				margin-left: 0;
			}
			.wholesalex_email_list {
				width: 70vw;
			}
			table.form-table.wc_emails.widefat.wholesalex-email th {
				padding-left: 10px;
			}
		</style>

		<?php
	}

	/**
	 * Email JS Codes
	 */
	public function wholesalex_email_js() {
		?>
		<script>
			function onCheckedHandler(e) {
				var checked = document.getElementById(e.target.id).checked;
				fetch(ajaxurl, {
						method: "POST",
						body: new URLSearchParams({
							action: "save_wholesalex_email_settings",
							nonce: wholesalex.nonce,
							id: e.target.id,
							value: checked
						}),
					})
					.then((res) => res.json())
					.then((res) => {
						if (res.success) {
						}
					})
					.catch((err) => {
					});
			};

			function SmartTagsHandler() {
				jQuery("#wholesalex_smart_tags_toogle_button").click(function() {
					jQuery("#wholesalex_smart_tags").toggle("fast");
				});
			}
			SmartTagsHandler();

			const saveEmailTemplate = (e) => {
				e.preventDefault();
				const formData = Object.fromEntries(new FormData(document.getElementById('wholesalex_email_template_editor')).entries());
				fetch(ajaxurl, {
						method: "POST",
						body: new URLSearchParams({
							...formData,
							'content': tinymce.activeEditor.getContent()
						}),
					})
					.then((res) => res.json())
					.then((res) => {
						if (res.success) {
							jQuery('#message').show();
						}
					})
					.catch((err) => {
					});
			}
		</script>
		<?php
	}

	/**
	 * Save WholesaleX Email Status
	 *
	 * @since 1.0.0
	 */
	public function save_wholesalex_email_settings() {
		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wholesalex-registration' ) ) ) {
			die( 'Nonce Verification Faild!' );
		}
		$__id    = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$__value = isset( $_POST['value'] ) ? sanitize_text_field( $_POST['value'] ) : '';
		if ( ! empty( $__id ) ) {
			update_option( $__id, ( 'true' === $__value ? true : false ) );
		}
		wp_send_json_success( __( 'Success.', 'wholesalex' ) );
	}
}
