<?php
namespace WOPB;

defined('ABSPATH') || exit;

class Option_Blocks{
    public function __construct() {
        $this->create_admin_page();
    }

    public static function create_admin_page() { ?>
        <div class="wopb-dashboard-container wopb-block-option">
            
            <div class="wopb-dashboard-body">
                <?php
                $option_data = wopb_function()->get_setting();
                $blocks_settings = wopb_function()->get_blocks_settings();

                foreach ($blocks_settings as $blocks) { ?>
                    <h6 class="wopb-sm-heading wopb-mb25"><?php echo esc_html($blocks['label']); ?></h6>
                    <div class="wopb-block-grid-content">
                        <?php foreach ($blocks['attr'] as $key => $val) { ?>
                                <div class="wopb-card wopb-p15">
                                    <div class="wopb-control-meta">
                                        <img src="<?php echo esc_url($val['icon']); ?>" alt="overview content">
                                        <div><?php echo esc_html($val['label']); ?></div>
                                    </div>
                                    <div class="wopb-control-option">
                                        <?php 
                                        if (isset($val['docs'])) { ?>
                                            <a href="<?php echo esc_url($val['docs']); ?>"  target="_blank" class="wopb-option-tooltip">
                                                <span><?php esc_html_e('Documentation', 'product-blocks'); ?></span>
                                                <div class="dashicons dashicons-book"></div>
                                            </a>
                                        <?php } ?>
                                        <?php 
                                        if (isset($val['live'])) { ?>
                                            <a href="<?php echo esc_url($val['live']); ?>" target="_blank" class="wopb-option-tooltip">
                                                <span><?php esc_html_e('Live View', 'product-blocks'); ?></span>
                                                <div class="dashicons dashicons-visibility"></div>
                                            </a>
                                        <?php } ?>
                                        <?php
                                            $output = $val['default'] ? 'checked' : '';
                                            if (isset($option_data[$key])) {
                                                $output = $option_data[$key] == 'yes' ? 'checked' : '';
                                            }
                                        ?>
                                        <input type="checkbox" data-type="blocks" class="wopb-addons-enable" id="<?php echo esc_attr($key); ?>" <?php echo esc_attr($output); ?>/>
                                        <label class="wopb-toggle-switch" for="<?php echo esc_attr($key); ?>">Toggle</label>
                                    </div>
                                </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                    
            </div>
        </div>
        <?php
    }
}