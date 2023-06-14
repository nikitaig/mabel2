<?php
namespace WOPB;

defined('ABSPATH') || exit;

class Options_Contact{
    public function __construct() {
        add_submenu_page('wopb-settings', __('Contact', 'product-blocks'), __('Contact / Support', 'product-blocks'), 'manage_options', 'wopb-contact', array( self::class, 'create_admin_page'), 20);
    }

    /**
     * Settings page output
     */
    public static function create_admin_page() { ?>
        <style>
            .style-css{
                background-color: #f2f2f2;
                -webkit-font-smoothing: subpixel-antialiased;
            }
        </style>

        <div class="wopb-option-body">
            <?php require_once WOPB_PATH . 'classes/options/Heading.php'; ?>
            <?php require_once WOPB_PATH . 'classes/options/Footer.php'; ?>
        </div>

    <?php }
}