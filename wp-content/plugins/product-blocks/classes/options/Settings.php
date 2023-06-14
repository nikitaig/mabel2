<?php
namespace WOPB;

defined('ABSPATH') || exit;

class Options_Settings{
    public function __construct() {
        add_submenu_page('wopb-settings', __('Settings', 'product-blocks'), __('Settings', 'product-blocks'), 'manage_options', 'wopb-option-settings', array( $this, 'create_admin_page'), 15);

        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function register_settings() {
        register_setting( 'wopb_options', 'wopb_options', array( $this, 'sanitize' ) );
    }


    /**
     * Sanitization callback
     */
    public function sanitize( $options ) {
        if ($options) {
            $settings = self::get_option_settings_keys();
            foreach ($settings as $key) {
                $options[$key] = isset($options[$key]) ? $options[$key] : '';
            }
            $old_data = wopb_function()->get_setting();
            $options = array_merge($old_data, $options);
        }
        return $options;
    }


    /**
     * Settings Fields Key Return
     */
    public function get_option_settings_keys() {
        $attr = array();
        $data = self::get_option_settings();
        if (!empty($data)) {
            foreach ($data as $key => $inner_data) {
                if (isset($inner_data['attr'])) {
                    foreach ($inner_data['attr'] as $k => $val) {
                        $attr[] = $k;
                    }
                }
            }
        }
        return $attr;
    }

    /**
     * Settings Field Return
     */
    public static function get_option_settings(){
        return apply_filters('wopb_settings', array(
            'general' => array(
                'label' => __('General Settings', 'product-blocks'),
                'attr' => array(
//                    'general_heading' => array(
//                        'type'  => 'heading',
//                        'label' => __('General Settings', 'product-blocks'),
//                    ),
                    'css_save_as' => array(
                        'type' => 'select',
                        'label' => __('CSS Add Via', 'product-blocks'),
                        'options' => array(
                            'wp_head'   => __( 'Header - (Internal)','product-blocks' ),
                            'filesystem' => __( 'File System - (External)','product-blocks' ),
                        ),
                        'default' => 'wp_head',
                        'desc' => __('Select where you want to save CSS.', 'product-blocks')
                    ),
                    'container_width' => array(
                        'type' => 'number',
                        'label' => __('Container Width', 'product-blocks'),
                        'default' => '1140',
                        'desc' => __('Change Container Width of the Page Template(Gutenberg Post Blocks Template).', 'product-blocks')
                    ),
                    'editor_container' => array(
                        'type' => 'select',
                        'label' => __('Editor Container', 'product-blocks'),
                        'options' => array(
                            'theme_default' => __( 'Theme Default','product-blocks' ),
                            'full_width' => __( 'Full Width','product-blocks' )
                        ),
                        'default' => 'theme_default',
                        'desc' => __('Select Editor Container Width.', 'product-blocks')
                    ),
                    'hide_import_btn' => array(
                        'type' => 'switch',
                        'label' => __('Hide Import Button', 'product-blocks'),
                        'default' => '',
                        'desc' => __('Hide Import Layout Button from the Gutenberg Editor.', 'product-blocks')
                    ),
                    'disable_google_font' => array(
                        'type' => 'switchButton',
                        'label' => __('Disable All Google Fonts', 'product-blocks'),
                        'default' => '',
                        'desc' => __('Disable All Google Fonts From Frontend and Backend ProductX Blocks.', 'product-blocks')
                    ),
                )
            )
        ));
    }


    /**
     * Changelog Data
     */
    public static function get_changelog_data() {
        $resource_data = file_get_contents(WOPB_PATH.'/readme.txt', "r");
        $data = array();
        if ($resource_data) {
            $resource_data = explode('== Changelog ==', $resource_data);
            if (isset($resource_data[1])) {
                $resource_data = $resource_data[1];
                $resource_data = explode("\n", $resource_data);
                $inner = false;
                $count = -1;
                
                foreach ($resource_data as $element) {
                    if ($element){
                        if (substr_count($element, '=') > 1) {
                            $count++;
                            $temp = trim(str_replace('=', '', $element));
                            if (strpos($temp, '-') !== false) {
                                $temp = explode('-', $temp);
                                $data[$count]['date'] = trim($temp[1]);
                                $data[$count]['version'] = trim($temp[0]);
                            }
                        }
                        if (strpos($element, '* New:') !== false) {
                            $data[$count]['new'][] = trim(str_replace('* New:', '', $element));
                        }
                        if (strpos($element, '* Fix:') !== false) {
                            $data[$count]['fix'][] = trim(str_replace('* Fix:', '', $element));
                        }
                        if (strpos($element, '* Update:') !== false) {
                            $data[$count]['update'][] = trim(str_replace('* Update:', '', $element));
                        }
                    }
                }
            }
        }
        if (!empty($data)) {
            foreach ($data as $k => $inner_data) {
                echo '<div class="wopb-changelog-wrap">';
                foreach ($inner_data as $key => $changelog) {
                    if ($key == 'date') {
                        echo '<div class="wopb-changelog-date">'.esc_html__('Released on ', 'product-blocks').' '.esc_html($changelog).'</div>';
                    } elseif($key == 'version') {
                        echo '<div class="wopb-changelog-version">'.esc_html__('Version', 'product-blocks').' : '.esc_html($changelog).'</div>';
                    } else {
                        foreach ($changelog as $keyword => $val) {
                            echo '<div class="wopb-changelog-title"><span class="changelog-'.esc_attr($key).'">'.esc_html($key).'</span>'.esc_html($val).'</div>';
                        }
                    }
                }
                echo '</div>';
            }
        }
    }


    public static function get_settings_render( $data ) {
        
        $option_data = wopb_function()->get_setting();

        foreach ($data as $key => $value) {
            if ($value['type'] == 'hidden') {
                echo '<input type="hidden" name="wopb_options['.esc_attr($key).']" value="'.esc_attr($value['value']).'"/>';
            } else {
                if ($value['type'] == 'heading') {
                    echo '<h2 class="wopb-settings-heading">'.esc_html($value['label']).'</h2>';
                    if ( isset($value['desc']) ) {
                        echo '<div class="wopb-settings-subheading">'.$value['desc'].'</div>';
                    }
                }
                echo '<div class="wopb-settings-wrap">';
                if ($value['type'] != 'heading') {
                    if (isset($value['label'])) {
                        echo '<div class="wopb-settings-label">'.esc_html($value['label']).'</div>';
                    }
                }
                    echo '<div class="wopb-settings-field-wrap">';
                        switch ($value['type']) {

                            case 'radio':
                                echo '<div class="wopb-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    foreach ( $value['options'] as $id => $label ) {
                                        echo '<input type="radio" id="'.esc_attr($id).'" name="wopb_options['.esc_attr($key).']" value="'.esc_attr($id).'" '.( $val == $id ? 'checked':'').'>';
                                        echo '<label for="'.esc_attr($id).'">'.strip_tags( esc_html($label) ).'</label><br>';   
                                    }
                                    if (isset($value['pro'])) {
                                        $disable = '';
                                        if (!wopb_function()->isPro()) {
                                            $disable = 'disabled';
                                        }
                                        foreach ($value['pro'] as $id => $label) {
                                            echo '<input '.esc_attr($disable).' type="radio" id="'.esc_attr($id).'" name="wopb_options['.esc_attr($key).']" value="'.esc_attr($id).'" '.( $val == $id ? 'checked':'').'>';
                                            echo '<label for="'.esc_attr($id).'">'.strip_tags( esc_html($label) ).' '.($disable ? '<a href="'.esc_url(wopb_function()->get_premium_link()).'" target="_blank">['.esc_html__('PRO', 'product-blocks').']</a>' : '').'</label><br>';
                                        }
                                    }
                                    if (isset($value['desc'])) {
                                        echo '<p class="description">'.$value['desc'].'</p>';
                                    }
                                echo '</div>';
                                break;

                            case 'select':
                                echo '<div class="wopb-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    echo '<select name="wopb_options['.esc_attr($key).']">';
                                        foreach ( $value['options'] as $id => $label ) {
                                            echo '<option value="'.esc_attr($id).'" '.( $val == $id ? ' selected="selected"':'').'>';
                                            echo strip_tags( esc_html($label) );
                                            echo '</option>';
                                        }
                                        echo '</select>';
                                    if(isset($value['desc'])){
                                        echo '<p class="description">'.$value['desc'].'</p>';
                                    }
                                echo '</div>';
                                break;

                            case 'color':
                                echo '<div class="wopb-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    echo '<input name="wopb_options['.esc_attr($key).']" value="'.esc_attr($val).'" class="wopb-color-picker" />';
                                    if(isset($value['desc'])){
                                        echo '<p class="description">'.$value['desc'].'</p>';
                                    }
                                echo '</div>';
                                break;

                            case 'number':
                                echo '<div class="wopb-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    echo '<input type="number" name="wopb_options['.esc_attr($key).']" value="'.esc_attr($val).'"/>';
                                    if(isset($value['desc'])){
                                        echo '<p class="description">'.$value['desc'].'</p>';
                                    }
                                echo '</div>';
                                break;

                            case 'switch':
                            case 'switchButton':
                                echo '<div class="wopb-settings-field wopb-settings-field-inline">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    
                                    $disable = '';
                                    if (isset($value['pro'])) {
                                        if (!wopb_function()->isPro()) {
                                            $disable = 'disabled';
                                        }
                                    }
                                    if(isset($value['options'])){
                                        foreach ($value['options'] as $option_key => $option_value){
                                            echo '<div>';
                                                echo '<label class="wopb-multi-switch">';
                                                    echo '<input '.esc_attr($disable).' type="checkbox" value="'.esc_attr($option_key).'" name="wopb_options['.esc_attr($key).'][]" '.(($val && in_array($option_key,$val)) ? 'checked' : '').' /> '.esc_html($option_value);
                                                echo '</label>';
                                            echo '</div>';
                                        }
                                    }else{
                                        echo '<input '.esc_attr($disable).' type="checkbox" value="yes" name="wopb_options['.esc_attr($key).']" '.($val == 'yes' ? 'checked' : '').' />';
                                        if (isset($value['desc'])) {
                                        echo '<p class="switch-description">'.$value['desc'].' '.($disable ? '<a href="'.esc_url(wopb_function()->get_premium_link()).'" target="_blank">['.esc_html__('PRO', 'product-blocks').']</a>' : '').'</p>';
                                        }
                                    }
                                    if ($value['type'] == 'switchButton') {
                                        echo '<div><span id="wopb-regenerate-css" class="wopb-btn wopb-btn-warning '.($val=='yes'?'active':'').'"><span class="dashicons dashicons-admin-generic"></span><span class="wopb-text">';
                                        echo __('Re-Generate Font Files', 'product-blocks');
                                        echo '</span></span></div>';
                                    }
                                echo '</div>';
                                break;

                            case 'text':
                                echo '<div class="wopb-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    echo '<input type="text" name="wopb_options['.esc_attr($key).']" value="'.esc_attr($val).'"/>';
                                    if(isset($value['desc'])){
                                        echo '<p class="description">'.$value['desc'].'</p>';
                                    }
                                echo '</div>';
                                break;

                            default:
                                # code...
                                break;

                        }
                    echo '</div>';
                echo '</div>';
            }
        }
    }

    /**
     * Settings data only for addons
     * @param $data
     * @param $params
     */
    public static function get_settings_data( $data, $params = [] ) {
        $option_data = wopb_function()->get_setting();
        if(isset($params['repeat_item'])) {
            $option_data = $params['repeat_item'];
        }
        $html = '';
        foreach ($data as $key => $value) {
            if ($value['type'] == 'hidden') {
                echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value['value']).'" data-default="' . (isset($value['default']) ? esc_attr($value['default']) : '' ) . '"/>';
            } else {
                if ($value['type'] == 'heading') {
                    echo '<h2 class="wopb-settings-heading">'.esc_html($value['label']).'</h2>';
                    if ( isset($value['desc']) ) {
                        echo '<div class="wopb-settings-subheading">'.$value['desc'].'</div>';
                    }
                }
                echo '<div class="wopb-settings-wrap ' . ('wopb-'.$value['type'].'-wrap ') . (isset($value['class']) ? $value['class'] : '') . '">';
                if ($value['type'] != 'heading') {
                    if (isset($value['label']) && $value['type'] != 'toggle') {
                        echo '<strong class="wopb-title">'.esc_html($value['label']).'</strong>';
                    }
                }
                    echo '<div class="wopb-settings-field-wrap">';
                        switch ($value['type']) {

                            case 'radio':
                                echo '<div class="wopb-settings-field">';
                                $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                foreach ($value['options'] as $id => $label) {
                                    echo '<input type="radio" id="' . esc_attr($id) . '" name="' . esc_attr($key) . '" value="' . esc_attr($id) . '" ' . ($val == $id ? 'checked' : '') . '>';
                                    echo '<label for="' . esc_attr($id) . '">' . strip_tags(esc_html($label)) . '</label><br>';
                                }
                                if (isset($value['pro'])) {
                                    $disable = '';
                                    if (!wopb_function()->isPro()) {
                                        $disable = 'disabled';
                                    }
                                    foreach ($value['pro'] as $id => $label) {
                                        echo '<input ' . esc_attr($disable) . ' type="radio" id="' . esc_attr($id) . '" name="' . esc_attr($key) . '" value="' . esc_attr($id) . '" ' . ($val == $id ? 'checked' : '') . '>';
                                        echo '<label for="' . esc_attr($id) . '">' . strip_tags(esc_html($label)) . ' ' . ($disable ? '<a href="' . esc_url(wopb_function()->get_premium_link()) . '" target="_blank">[' . esc_html__('PRO', 'product-blocks') . ']</a>' : '') . '</label><br>';
                                    }
                                }
                                if (isset($value['desc'])) {
                                    echo '<p class="description">' . $value['desc'] . '</p>';
                                }
                                echo '</div>';
                                break;

                            case 'select':
                                echo '<div class="wopb-settings-field">';
                                $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');

                                echo '<select name="' . esc_attr($key) . '">';
                                foreach ($value['options'] as $id => $label) {
                                    echo '<option value="' . esc_attr($id) . '" ' . ($val == $id ? ' selected="selected"' : '') . '>';
                                    echo strip_tags(esc_html($label));
                                    echo '</option>';
                                }
                                echo '</select>';
                                if (isset($value['desc'])) {
                                    echo '<p class="description">' . $value['desc'] . '</p>';
                                }
                                echo '</div>';
                                break;

                            case 'color':
                                echo '<div class="wopb-settings-field">';
                                $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                echo '<input name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="wopb-color-picker" />';
                                if (isset($value['desc'])) {
                                    echo '<p class="description">' . $value['desc'] . '</p>';
                                }
                                echo '</div>';
                                break;

                            case 'number':
                                echo '<div class="wopb-settings-field">';
                                $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                echo '<input type="number" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '"/>';
                                if (isset($value['desc'])) {
                                    echo '<p class="description">' . $value['desc'] . '</p>';
                                }
                                echo '</div>';
                                break;

                            case 'switch':
                            case 'switchButton':
                                echo '<div class="wopb-settings-field wopb-settings-field-inline">';
                                $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');

                                $disable = '';
                                if (isset($value['pro'])) {
                                    if (!wopb_function()->isPro()) {
                                        $disable = 'disabled';
                                    }
                                }
                                if (isset($value['options'])) {
                                    foreach ($value['options'] as $option_key => $option_value) {
                                        echo '<div>';
                                        echo '<label class="wopb-multi-switch">';
                                        echo '<input ' . esc_attr($disable) . ' type="checkbox" value="' . esc_attr($option_key) . '" name="' . esc_attr($key) . '[]" ' . (($val && in_array($option_key, $val)) ? 'checked' : '') . ' /> ' . esc_html($option_value);
                                        echo '</label>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<input ' . esc_attr($disable) . ' type="checkbox" value="yes" name="' . esc_attr($key) . '" ' . ($val == 'yes' ? 'checked' : '') . ' />';
                                    if (isset($value['desc'])) {
                                        echo '<p class="switch-description">' . $value['desc'] . ' ' . ($disable ? '<a href="' . esc_url(wopb_function()->get_premium_link()) . '" target="_blank">[' . esc_html__('PRO', 'product-blocks') . ']</a>' : '') . '</p>';
                                    }
                                }
                                if ($value['type'] == 'switchButton') {
                                    echo '<div><span id="wopb-regenerate-css" class="wopb-btn wopb-btn-warning '.($val=='yes'?'active':'').'"><span class="dashicons dashicons-admin-generic"></span><span class="wopb-text">';
                                    echo __('Re-Generate Font Files', 'product-blocks');
                                    echo '</span></span></div>';
                                }
                                echo '</div>';
                                break;

                            case 'text':
                                echo '<div class="wopb-settings-field">';
                                $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                $disabled = '';
                                if (isset($value['disable_depend_on_field']) && isset($option_data[$value['disable_depend_on_field']]) && isset($value['disable_value']) && $option_data[$value['disable_depend_on_field']] == $value['disable_value']) {
                                    $disabled = 'disabled';
                                } elseif (isset($value['disable_value']) && $val == $value['disable_value']) {
                                    $disabled = 'disabled';
                                }
                                echo '<input type="text" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" ' . $disabled . ' />';
                                if (isset($value['desc'])) {
                                    echo '<p class="description">' . $value['desc'] . '</p>';
                                }
                                echo '</div>';
                                break;

                            case 'toggle':
                                $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                $checked = '';
                                if($val == 'yes' || $val != '') {
                                    $checked = 'checked';
                                }elseif (isset($params['base_value']) && isset($params['base_value']['switch_name']) && $params['base_value']['switch_name'] == $key && isset($params['base_value']['switch_default_active'])) {
                                    $checked = 'checked';
                                }
                                $html = '';
                                $html .= '<div class="wopb-settings-field wopb-control-option">';
                                $html .= '<input 
                                                type="checkbox" 
                                                class="wopb-addons-enable wopb-toggle-checkbox" 
                                                value="' . ($val != '' ? $val : '') . '" 
                                                name="' . esc_attr($key) . '" 
                                                data-depend-on-field="' . (isset($value['depend_on_field']) ? esc_attr($value['depend_on_field']) : '' ) . '" 
                                                data-depend-target-field="' . (isset($value['depend_target_field']) ? esc_attr($value['depend_target_field']) : '' )  . '"'
                                    . $checked
                                    . ' />';
                                $html .= '<label class="wopb-toggle-switch">';
                                $html .= '</label>';
                                $html .= isset($value['label']) ? '<span>' . $value['label'] . '</span>' : '';
                                $html .= '</div>';
                                echo $html;
                                break;
                            case 'repeatable':
                                $repeat_items = isset($option_data[$key]) ? $option_data[$key] : '';
                                $params['base_repeater'] = $key;
                                $params['base_value'] = $value;
                                if (isset($value['desc'])) {
                                    $html .= '<p class="description">' . $value['desc'] . '</p>';
                                }
                                echo '<div class="wopb-default-repeater wopb-d-none">';
                                    echo self::repeat_html($params);
                                echo '</div>';
                                if ($repeat_items) {
                                    foreach ($repeat_items as $repeat_item) {
                                        $params['repeat_item'] = $repeat_item;
                                        $html .= self::repeat_html($params);
                                    }
                                }else {
                                    $params['base_value']['switch_default_active'] = true;
                                    $html .= self::repeat_html($params);
                                }
                                $html .= '<a class="wopb-add-btn">Add Item <span class="dashicons dashicons-plus-alt2"></span> </a>';
                                echo $html;
                                break;

                            default:
                                # code...
                                break;

                        }
                    echo '</div>';
                echo '</div>';
            }
        }
    }


    /**
     * Settings page output
     */
    public static function create_admin_page() {
        require_once WOPB_PATH.'classes/options/Banner.php';
        $banner = new \WOPB\Banner();
        $data = self::get_option_settings();
        ?>
        <div class="wopb-dashboard-container">
            <div class="wopb-dashboard-body wopb-card wopb-getstart-body">
                <div class="wopb-container-grid">
                    <form method="post" action="#">
                        <div class="wopb-settings wopb-submit-button wopb-card wopb-p40">
                            <?php
                                if (isset($data['general']['attr'])) {
                                    self::get_settings_data($data['general']['attr']);
                                }
                            ?>
                            <div class="wopb-data-message"></div>
                            <button class="button button-primary button-large"><?php esc_html_e('Save Settings', 'product-blocks'); ?></button>
                        </div>
                    </form>
                    <div>
                        <?php
                            $banner->sidebar_additional_feature('settings');
                            $banner->sidebar_content_rate();
                        ?>
                    </div>
                </div>
            </div>
        </div>

    <?php }

    public static function repeat_html($params) {
        $switch_value = '';
        $value = $params['base_value'];
        $repeat_item = isset($params['repeat_item']) ? $params['repeat_item'] : '';
        $repeat_header_text = isset($value['header_text']) ? $value['header_text'] : '';
        if(isset($value['depend_options']) && isset($value['depend_on_field'])) {
            $repeat_header_text = isset($repeat_item[$value['depend_on_field']]) ? $value['depend_options'][$repeat_item[$value['depend_on_field']]] : $repeat_header_text;
        }
        if(isset($value['switch_name']) && isset($repeat_item[$value['switch_name']])) {
            $switch_value = $repeat_item[$value['switch_name']];
        }elseif (isset($value['switch_default_active'])) {
            $switch_value = $value['switch_default_active'];
        }
        $html = '';
        $html .= '<div class="wopb-repeat-section" data-base-repeater="' . (isset($params['base_repeater']) ? $params['base_repeater'] : '') . '">';
            $html .= '<input type="hidden" class="wopb-repeater-input" name="' . $params['base_repeater'] . '[]" value="1">';
                $html .= '<div class="wopb-collapse-header">';
                    $html .= '<span class="wopb-header-content">';
                        if($value['switch'] == true) {
                            $html .= '<span class="wopb-control-option">';
                                $html .= '<input
                                            type="checkbox"
                                            class="wopb-addons-enable wopb-toggle-checkbox"
                                            value="' . esc_attr($switch_value) . '"
                                            name="' . (isset($value['switch_name']) ? esc_attr($value['switch_name']) : '' )  . '"
                                            data-depend-on-field="' . esc_attr($value['depend_on_field']) . '"
                                            data-depend-target-field="' . esc_attr($value['depend_target_field']) . '"'
                                            . ( $switch_value != '' || $switch_value == 'yes' ? 'checked' : '')
                                        . ' />';
                                $html .= '<label class="wopb-toggle-switch">';
                                $html .= '</label>';
                            $html .= '</span>';
                        }
                        $html .= '<span class="wopb-header-text">' . esc_html($repeat_header_text) . '</span>';
                    $html .= '</span>';
                        $html .= '<span class="wopb-control">';
                            $html .= '<span class="dashicons dashicons-no-alt wopb-remove' . ($switch_value ? ' wopb-d-none' : '') . '"></span>';
                            $html .= '<span class="dashicons dashicons-arrow-down-alt2 wopb-down-arrow wopb-d-none"></span>';
                            $html .= '<span class="dashicons dashicons-arrow-right-alt2 wopb-right-arrow"></span>';
                        $html .= '</span>';
                $html .= '</div>';

                $html .= '<div class="wopb-collapse-body" style="display:none">';
                    ob_start();
                        self::get_settings_data($value['fields'], $params);
                    $html .= ob_get_clean();
                $html .= '</div>';
            $html .= '</div>';
        return $html;
    }


}