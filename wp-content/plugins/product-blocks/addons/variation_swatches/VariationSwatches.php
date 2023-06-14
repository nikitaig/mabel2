<?php
/**
 * Variation Swatches Addons Core.
 *
 * @package WOPB\Variation Swatches
 * @since v.2.2.7
 */

namespace WOPB;

defined('ABSPATH') || exit;

/**
 * Variation Swatches class.
 */
class VariationSwatches
{

    /**
     * Setup class.
     *
     * @since v.2.2.7
     */
    protected $attribute_types;
    const COLOR = 'color';
    const IMAGE = 'image';
    const LABEL = 'label';

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'add_variation_swatches_scripts'));

        $this->attribute_types = [
            self::COLOR => 'Color',
            self::IMAGE => 'Image',
            self::LABEL => 'Label',
        ];

        //Manage Attribute Term (Create attribute term column, column content, field)
        $this->manage_attribute_term();

        //Attribute Type Dropdown in Admin Product Attribute
        add_filter('product_attributes_type_selector', [$this, 'create_attribute_type']);

        //Save Custom Term Meta of Attribute
        add_action('created_term', [$this, 'save_term_meta'], 10, 2);
        add_action('edit_term', [$this, 'save_term_meta'], 10, 2);

        //Set Product Option Term in Product Attribute
        add_action('woocommerce_product_option_terms', [$this, 'set_product_option_terms'], 20, 3);

        //Change Variation Dropdown HTML for Variation Swatch
		add_filter('woocommerce_dropdown_variation_attribute_options_html', [$this, 'variation_swatch_html'], 200, 2);

		//Ajax Add To Cart Mechanism For Variation Swatches Loop Product
		add_action( 'wp_ajax_wopb_loop_add_to_cart_ajax', [$this, 'wopb_loop_add_to_cart_ajax'] );

		// Thickbox.
        add_thickbox();

        //Show Variation Swatch in shop, archive page for default WooCommerce
		$this->swatchesInWcListingPage();

    }

    /**
     * Variation Swatches Script Add
     *
     * @return NULL
     * @since v.2.2.7
     */
    public function add_variation_swatches_scripts()
    {
        wp_enqueue_style('wopb-variation-swatches-style', WOPB_URL . 'addons/variation_swatches/css/variation_swatches.css', array(), WOPB_VER);
        wp_enqueue_script('wopb-variation-swatches', WOPB_URL . 'addons/variation_swatches/js/variation_swatches.js', array('jquery'), WOPB_VER, true);
        wp_enqueue_script('wc-add-to-cart-variation');

?>
        <style>
            .wopb-swatch {
                min-width: <?php esc_attr_e( wopb_function()->get_setting('variation_switch_width')) ?>px;
                min-height: <?php esc_attr_e( wopb_function()->get_setting('variation_switch_height')) ?>px;
            }
            .wopb-swatch img {
                width: <?php esc_attr_e( wopb_function()->get_setting('variation_switch_width')) ?>px;
                height: <?php esc_attr_e( wopb_function()->get_setting('variation_switch_height')) ?>px;
            }
        </style>

<?php
    }

    /**
     * Variation Swatches Addons Initial Setup Action
     *
     * @return NULL
     * @since v.2.2.7
     */
    public function initial_setup()
    {
        // Set Default Value
        $initial_data = array(
            'variation_switch_heading' => 'yes',
            'variation_switch_tooltip_enable' => 'yes',
            'variation_switch_shape_style' => 'circle',
            'variation_switch_label_is_background' => 'yes',
            'variation_switch_dropdown_to_button' => 'yes',
            'variation_switch_width' => '25',
            'variation_switch_height' => '25',
            'variation_switch_position' => 'after_cart',
        );
        foreach ($initial_data as $key => $val) {
            wopb_function()->set_setting($key, $val);
        }
    }

    /**
     * Create Custom Dropdown Attribute Type in Product Attribute
     *
     * @return ARRAY
     * @since v.2.2.7
     */
    public function create_attribute_type($types)
    {
        $types = array_merge($types, $this->attribute_types);
        return $types;
    }

    /**
     * Manage Attribute Term in Product Attribute
     *
     * @return NULL
     * @since v.2.2.7
     */
    public function manage_attribute_term()
    {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $types = $this->attribute_types;

        if (empty($attribute_taxonomies)) {
            return;
        }
        foreach ($attribute_taxonomies as $taxonomy) {
            if (isset($types[$taxonomy->attribute_type])) {
                if ($taxonomy->attribute_type === self::COLOR) {
                    $column = 'color_attribute_column';

                } elseif ($taxonomy->attribute_type === self::IMAGE) {
                    $column = 'image_attribute_column';

                } elseif ($taxonomy->attribute_type === self::LABEL) {
                    $column = 'label_attribute_column';
                }
                add_filter('manage_edit-pa_' . $taxonomy->attribute_name . '_columns', [$this, $column]);
                add_filter('manage_pa_' . $taxonomy->attribute_name . '_custom_column', [$this, 'create_attribute_column_content'], 10, 3);

                add_action('pa_' . $taxonomy->attribute_name . '_add_form_fields', [$this, 'create_attribute_field']);
                add_action('pa_' . $taxonomy->attribute_name . '_edit_form_fields', [$this, 'edit_attribute_field'], 10, 2);
            }
        }
    }

    /**
     * Create Color Column in Product Attribute Term
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function color_attribute_column($columns)
    {
        $column_head = esc_html__($this->attribute_types[self::COLOR], 'product-blocks');
        return $this->create_attribute_column($columns, $column_head);
    }

    /**
     * Create Image Column in Product Attribute Term
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function image_attribute_column($columns)
    {
        $column_head = esc_html__($this->attribute_types[self::IMAGE], 'product-blocks');
        return $this->create_attribute_column($columns, $column_head);
    }

    /**
     * Create Label Column in Product Attribute Term
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function label_attribute_column($columns)
    {
        $column_head = esc_html__($this->attribute_types[self::LABEL], 'product-blocks');
        return $this->create_attribute_column($columns, $column_head);
    }

    /**
     * Column Creation Function in Product Attribute
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function create_attribute_column($columns, $column_head = '')
    {
        $new_columns = [];

        if (isset($columns['cb'])) {
            $new_columns['cb'] = $columns['cb'];
        }

        $new_columns['custom_column'] = $column_head;
        unset($columns['cb']);

        return $new_columns + $columns;
    }

    /**
     * Create Attribute Column Content in Product Attribute Term
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function create_attribute_column_content($columns, $column, $term_id)
    {
        if ('custom_column' !== $column) {
            return $columns;
        }
        $attribute = $this->get_taxonomy_attribute( sanitize_text_field($_REQUEST['taxonomy']) );
        $attribute_value = get_term_meta($term_id, $attribute->attribute_type, true);

        switch ($attribute->attribute_type) {
            case self::COLOR:
                echo "<div class='wopb-attribute-swatch-color' style='background-color:".esc_attr($attribute_value).";'></div>";
                break;

            case self::IMAGE:
                $image = $attribute_value ? wp_get_attachment_image_src($attribute_value) : '';
                $image = $image ? $image[0] : WOPB_URL . 'assets/img/wopb-placeholder.jpg';
                echo "<img class='wopb-attribute-swatch-image' src='".esc_url($image)."'>";
                break;

            case self::LABEL:
                echo "<div>".esc_html($attribute_value)."</div>";
                break;
        }

        return $columns;
    }

    /**
     * Create Attribute Form Field in Product Attribute Term
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function create_attribute_field($taxonomy)
    {
        $attribute = $this->get_taxonomy_attribute($taxonomy);
        $this->attribute_fields($attribute->attribute_type, '', 'add');
    }

    /**
     * Edit Attribute Form Field in Product Attribute Term
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function edit_attribute_field($term, $taxonomy)
    {
        $attribute = $this->get_taxonomy_attribute($taxonomy);
        $attribute_value = get_term_meta($term->term_id, $attribute->attribute_type, true);
        $this->attribute_fields($attribute->attribute_type, $attribute_value, 'edit');
    }

    /**
     * Save Attribute Term Data
     *
     * @return NULL
     * @since v.2.2.7
     */
    public function save_term_meta($term_id, $tt_id)
    {
        foreach ($this->attribute_types as $key => $value) {
            if (isset($_POST[$key])) {
                if ($key == self::COLOR) {
                    update_term_meta($term_id, $key, sanitize_hex_color($_POST[$key]));
                } else {
                    update_term_meta($term_id, $key, sanitize_text_field($_POST[$key]));
                }
            }
        }
    }

    /**
     * Attribute Form Field Creation Function
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function attribute_fields($attribute_type, $attribute_value, $form_type)
    {
        $attribute_types = $this->attribute_types;
        if (!isset($attribute_types[$attribute_type])) {
            return;
        }

        printf(
            '<%s class="form-field form-required">%s<label for="term-%s">%s</label>%s',
            $form_type == 'edit' ? 'tr' : 'div',
            $form_type == 'edit' ? '<th>' : '',
            esc_attr($attribute_type),
            $attribute_types[$attribute_type],
            $form_type == 'edit' ? '</th><td>' : ''
        );

        switch ($attribute_type) {
            case self::COLOR:
                ?>
                <input class="wopb-color-picker" id="term-<?php echo esc_attr($attribute_type) ?>" name="<?php echo esc_attr($attribute_type) ?>" value="<?php echo esc_attr($attribute_value) ?>"/>
                <?php
                break;

            case self::IMAGE:
                $image = $attribute_value ? wp_get_attachment_image_src($attribute_value) : '';
                $image = $image ? $image[0] : WOPB_URL . 'assets/img/wopb-placeholder.jpg';
                ?>
                <div class="wopb-term-img-thumbnail" id="wopb-term-img-thumbnail">
                    <img src="<?php echo esc_url($image) ?>"/>
                </div>

                <div>
                    <input type="hidden" id="wopb-term-img-input" name="<?php echo esc_attr($attribute_type) ?>" value="<?php echo esc_attr($attribute_value) ?>"/>

                    <a class="button" id="wopb-term-upload-img-btn">
                        <?php esc_html_e('Upload Image', 'product-blocks'); ?>
                    </a>

                    <a class="button <?php echo empty($attribute_value) ? 'wopb-d-none' : '' ?>" id="wopb-term-img-remove-btn">
                        <?php esc_html_e('Remove', 'product-blocks'); ?>
                    </a>
                </div>
                <?php
                break;

            case self::LABEL:
                ?>
                <input type="text" id="term-<?php echo esc_attr($attribute_type) ?>" name="<?php echo esc_attr($attribute_type) ?>" value="<?php echo esc_attr($attribute_value) ?>"/>
                <?php
                break;
            default:
        }

        echo $form_type == 'edit' ? '</td></tr>' : '</div>';
    }

    /**
     * Set Dropdown Option Term in Product Attribute Selection
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function set_product_option_terms($taxonomy, $index, $attribute)
    {
        if (!array_key_exists($taxonomy->attribute_type, $this->attribute_types)) {
            return;
        }
        global $thepostid;
        $product_id = isset($_POST['post_id']) ? absint(sanitize_text_field($_POST['post_id'])) : $thepostid; ?>

        <select multiple="multiple" data-placeholder="<?php esc_attr_e('Select terms', 'product-blocks'); ?>"
                class="multiselect attribute_values wc-enhanced-select"
                name="attribute_values[<?php echo esc_attr($index); ?>][]">
            <?php
            $all_terms = get_terms($attribute->get_taxonomy(), apply_filters('woocommerce_product_attribute_terms',
                [
                    'orderby' => 'name',
                    'hide_empty' => false
                ]
            ));

            if ($all_terms) {
                foreach ($all_terms as $term) {
                    echo '<option value="' . esc_attr($term->term_id) . '" ' . selected(has_term(absint($term->term_id), $attribute->get_taxonomy(), $product_id), true, false) . '>'
                            . esc_html(apply_filters('woocommerce_product_attribute_term_name', $term->name, $term)) .
                        '</option>';
                }
            }
        ?>
        </select>

        <button class="button plus select_all_attributes"><?php esc_html_e('Select all', 'product-blocks'); ?></button>
        <button class="button minus select_no_attributes"><?php esc_html_e('Select none', 'product-blocks'); ?></button>
        <button class="button fr plus add_new_attribute" data-type="<?php echo esc_html($taxonomy->attribute_type) ?>">
            <?php esc_html_e('Add new', 'product-blocks'); ?>
        </button>
        <?php
    }

    /**
     * Change Variation Dropdown HTML
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function variation_swatch_html($html, $args) {
		$attribute_types = $this->attribute_types;
		$taxonomy_attribute = $this->get_taxonomy_attribute($args['attribute']);

		$options = $args['options'];
        $product = $args['product'];
        $attribute = $args['attribute'];
        $terms = wc_get_product_terms($product->get_id(), $attribute, ['fields' => 'all']);

        $custom_style = "";
        $custom_image_style = "";
        if(!empty($taxonomy_attribute->attribute_type) && array_key_exists($taxonomy_attribute->attribute_type, $attribute_types) && $taxonomy_attribute->attribute_type != self::LABEL && wopb_function()->get_setting('variation_switch_shape_style') == 'circle') {
            $custom_style .= 'border-radius: 50%;';
            $custom_image_style .= 'border-radius: 50%;';
        }

        if (!empty($taxonomy_attribute) && array_key_exists($taxonomy_attribute->attribute_type, $attribute_types)) {
            $class = "wopb-variation-selector wopb-variation-select-{$taxonomy_attribute->attribute_type}";
            $variation_swatches = '';

            if (empty($options) && !empty($product) && !empty($attribute)) {
                $attributes = $product->get_variation_attributes();
                $options = $attributes[$attribute];
            }

            if (!empty($options) && $product && taxonomy_exists($attribute)) {
                $i = 0;
                foreach ($terms as $term) {
                    $i++;
                    if (in_array($term->slug, $options)) {
                        $selected = sanitize_title($args['selected']) == $term->slug ? 'selected' : '';
                        $name = esc_html(apply_filters('woocommerce_variation_option_name', $term->name));
                        $tooltip = '';
                        if(wopb_function()->get_setting('variation_switch_tooltip_enable') == 'yes') {
                            $tooltip .= '<span class="wopb-variation-swatch-tooltip">' . ($term->description ? $term->description : $name) . '</span>';
                        }

                        $data_variation_id = '';
                        $variation_image = '';
                        foreach ($product->get_available_variations() as $available_variation) {
                            $variation = new \WC_Product_Variation( $available_variation['variation_id'] );
                            $variation_attributes_values = array_values($variation->get_attributes());
                            if(($taxonomy_attribute->attribute_type == self::COLOR || $taxonomy_attribute->attribute_type == self::IMAGE) && in_array($term->slug, $variation_attributes_values) && $variation->get_image_id('edit') != 0) {
                               $data_variation_id = $available_variation['variation_id'];
                               $variation_image = $available_variation['image']['thumb_src'];
                               break;
                            }
                        }
                        switch ($taxonomy_attribute->attribute_type) {
                            case self::COLOR:
                                $bg_color = get_term_meta($term->term_id, $taxonomy_attribute->attribute_type, true);
                                list($r, $g, $b) = sscanf($bg_color, "#%02x%02x%02x");
                                $color = "rgba($r,$g,$b,0.5)";
                                $custom_style .= "background-color: $bg_color; color: $color;";
                                $variation_swatches .= sprintf(
                                    '<span class="wopb-swatch wopb-swatch-color wopb-swatch-%s %s" style="%s" data-value="%s" data-name="%s" data-variation_id="%s">%s</span>',
                                    esc_attr($term->slug),
                                    $selected,
                                    $custom_style,
                                    esc_attr($term->slug),
                                    $name,
                                    $data_variation_id,
                                    $tooltip
                                );
                                break;

                            case self::IMAGE:
                                $image = '';
                                if(wopb_function()->get_setting('product_image_in_variation_switch') && wopb_function()->is_lc_active()) {
                                     $image = $variation_image;
                                }

                                if(empty($image)) {
                                    $image = get_term_meta($term->term_id, $taxonomy_attribute->attribute_type, true);
                                    $image = $image ? wp_get_attachment_image_src($image) : '';
                                    $image = $image ? $image[0] : WOPB_URL . 'assets/img/wopb-placeholder.jpg';
                                }

                                $variation_swatches .= sprintf(
                                    '<span class="wopb-swatch wopb-swatch-image swatch-%s %s" data-value="%s" data-name="%s" data-variation_id="%s"><img src="%s" alt="%s" style="%s">%s</span>',
                                    esc_attr($term->slug),
                                    $selected,
                                    esc_attr($term->slug),
                                    $name,
                                    esc_attr($data_variation_id),
                                    esc_url($image),
                                    $name,
                                    $custom_image_style,
                                    $tooltip
                                );
                                break;

                            case self::LABEL:
                                $label = get_term_meta($term->term_id, $taxonomy_attribute->attribute_type, true);
                                $label = $label ? $label : $name;
                                $label_class = '';
                                $label_separator = '';
                                if(wopb_function()->get_setting('variation_switch_label_is_background') != 'yes' ) {
                                    $label_class = ' wopb-label-only-text';
                                    if($i != count($terms)) {
                                        $label_separator = '<span class="wopb-variation-swatch-separator"> - </span>';
                                    }
                                }
                                $variation_swatches .= sprintf(
                                    '<span class="wopb-swatch wopb-swatch-label' . $label_class . ' swatch-%s %s" style="%s" data-value="%s" data-name="%s" data-variation_id="%s">%s%s</span>%s',
                                    esc_attr($term->slug),
                                    $selected,
                                    $custom_style,
                                    esc_attr($term->slug),
                                    $name,
                                    $data_variation_id,
                                    esc_html($label),
                                    $tooltip,
                                    $label_separator
                                );
                                break;
                        }
                    }
                }
            }
        }elseif(wopb_function()->get_setting('variation_switch_dropdown_to_button') == 'yes') {
		    $class = "wopb-variation-selector wopb-variation-select-{$attribute}";
		    $variation_swatches = '';
		    $i = 0;
            foreach ( $options as $option ) {
                $i++;
                $selected = ( sanitize_title( $option ) == sanitize_title( $args[ 'selected' ] ) ) ? 'selected' : '';
                $name = $option;
                $tooltip = '';
                if(wopb_function()->get_setting('variation_switch_tooltip_enable') == 'yes') {
                    $tooltip .= '<span class="wopb-variation-swatch-tooltip">' . $option . '</span>';
                }
                $label = $name;
                $label_class = '';
                $label_separator = '';
                if(wopb_function()->get_setting('variation_switch_label_is_background') != 'yes' ) {
                    $label_class = ' wopb-label-only-text';
                    if($i != count($options)) {
                        $label_separator = '<span class="wopb-variation-swatch-separator"> - </span>';
                    }
                }
                $variation_swatches .= sprintf(
                    '<span class="wopb-swatch wopb-swatch-label' . $label_class . ' swatch-%s %s" style="%s" data-value="%s" data-name="%s">%s%s</span>%s',
                    esc_attr($name),
                    $selected,
                    $custom_style,
                    esc_attr($name),
                    esc_attr($name),
                    esc_html($label),
                    $tooltip,
                    $label_separator
                );
            }
        }else {
		    return $html;
        }

		if (!empty($variation_swatches)) {
            $class .= " wopb-d-none";
            $variation_swatches = "<div class='wopb-variation-swatches' data-attribute_name='attribute_" . esc_attr($attribute) . "'>{$variation_swatches}</div>";
            $html = "<div class='" . esc_attr($class) . "'>{$html}</div>" . $variation_swatches;
        }

		return $html;
	}

	/**
     * Show Variation Switcher in shop, archive, product grid
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function swatchesInWcListingPage() {
        if(wopb_function()->get_setting('variation_switch_shop_page_enable')) {
            if( wopb_function()->get_setting('variation_switch_position') == 'before_title' || wopb_function()->get_setting('variation_switch_position') == 'after_title' ) {
                remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10);
                add_action( 'woocommerce_shop_loop_item_title', [$this, 'swatches_shop_loop_item_title'] );

            }elseif( wopb_function()->get_setting('variation_switch_position') == 'before_price' || wopb_function()->get_setting('variation_switch_position') == 'after_price' ) {


            }elseif( wopb_function()->get_setting('variation_switch_position') == 'before_cart' || wopb_function()->get_setting('variation_switch_position') == 'after_cart' ) {
                add_action('woocommerce_loop_add_to_cart_link', [$this, 'swatches_show_loop_add_to_cart'], 10, 3);
            }
        }
        add_filter( 'woocommerce_get_price_html', [$this, 'swatches_show_loop_item_price'], 100, 2 );
    }

    /**
     * Show Variation Switcher Before/After Title in Loop Product
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function swatches_shop_loop_item_title() {
        global $product;
        $title = '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h2>';
        if($product->is_type('variable') && !wopb_function()->is_builder()) {
            if(wopb_function()->get_setting('variation_switch_position') == 'before_title') {
                $title = $this->loop_variation_form($product) . $title;
            }elseif( wopb_function()->get_setting('variation_switch_position') == 'after_title' ) {
                $title = $title .$this->loop_variation_form($product);
            }
        }
        echo $title;
    }

    /**
     * Show Variation Switcher Before/After Price in Loop Product
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function swatches_show_loop_item_price($price, $product) {
        if($product->is_type('variable') && !wopb_function()->is_builder()) {
            if(is_shop() || is_archive() || is_product()) {
                $price = "<div class='wopb-variation-switcher-price'>{$price}</div>";
                if( wc_get_loop_prop( 'total' ) && wopb_function()->get_setting('variation_switch_position') == 'before_price') {
                    $price = $this->loop_variation_form($product) . $price;
                }elseif( wc_get_loop_prop( 'total' ) &&  wopb_function()->get_setting('variation_switch_position') == 'after_price') {
                    $price = $price . $this->loop_variation_form($product);
                }
            }
        }

        return $price;
    }

    /**
     * Show Variation Switcher Before/After Add To Cart Button in Loop Product
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function swatches_show_loop_add_to_cart($add_to_cart_html, $product, $args) {
        if($product->is_type('variable') && !wopb_function()->is_builder()) {
            $html = '';
            $html .= $this->loop_variation_form($product);
            $add_to_cart_html = sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
            esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
            isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
            esc_html( $product->add_to_cart_text() )
        );
            if(wopb_function()->get_setting('variation_switch_position') == 'before_cart') {
                return $html.$add_to_cart_html;
            }elseif(wopb_function()->get_setting('variation_switch_position') == 'after_cart') {
                return $add_to_cart_html . $html;
            }
        }
        return $add_to_cart_html;
    }

    /**
     * Show Variation Selection Form in Loop Product
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function loop_variation_form($product) {
        if($product->is_type('variable')) {
            $available_variations = $product->get_available_variations();
            $attributes = $product->get_variation_attributes();
            $attribute_keys = array_keys($attributes);
            $html = '';
            $html .= "<div class='variations_form wopb-loop-variations-form' data-product_id='{$product->get_id()}' data-product_variations='" . htmlspecialchars(wp_json_encode($available_variations)) . "'>";
                $html .= "<table class='variations' cellspacing='0'>";
                    $html .= "<tbody>";
                    foreach ($attributes as $attribute => $attribute_options) {
                        $html .= "<tr>";
                        $html .= "<td class='value'>";
                        ob_start();
                        wc_dropdown_variation_attribute_options(array('options' => $attribute_options, 'attribute' => $attribute, 'product' => $product, 'selected' => '', 'is_archive' => true));
                        $dropdown_variation_attribute = ob_get_clean();
                        $html .= $dropdown_variation_attribute;
                        if (end($attribute_keys) === $attribute) {
                            $html .= wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'product-blocks') . '</a>'));
                        }
                        $html .= "</td>";
                        $html .= "</tr>";
                    }
                    $html .= "</tbody>";
                $html .= "</table>";
            $html .= "</div>";

            return $html;
        }
    }

    /**
     * Add To Cart Ajax in Loop Product
     *
     * @return HTML
     * @since v.2.2.7
     */
    public function wopb_loop_add_to_cart_ajax() {
        if ( ! isset( $_POST['product_id'] ) ) {
            return;
        }

        $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( sanitize_text_field($_POST['product_id']) ) );
        $quantity          = ! empty( $_POST['quantity'] ) ? wc_stock_amount( absint( sanitize_text_field($_POST['quantity']) ) ) : 1;
        $product_status    = get_post_status( $product_id );
        $variation_id      = ! empty( $_POST['variation_id'] ) ? absint( sanitize_text_field($_POST['variation_id']) ) : 0;
        $variation         = ! empty( $_POST['variation'] ) ? array_map( 'esc_attr', $_POST['variation']) : array();
        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );

        if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {
            do_action( 'woocommerce_ajax_added_to_cart', $product_id );

            if ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
                wc_add_to_cart_message( array( $product_id => $quantity ), true );
            }
           \WC_AJAX::get_refreshed_fragments();

        } else {
            // If there was an error adding to the cart, redirect to the product page to show any errors.
            $data = array(
                'error'       => true,
                'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
            );

            wp_send_json( $data );
        }
    }

    /**
     * Query for Get Taxonomy Attribute
     *
     * @return STRING
     * @since v.2.2.7
     */
    public function get_taxonomy_attribute($taxonomy)
    {
        global $wpdb;
        $attribute = substr($taxonomy, 3);
        $attribute = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s", $attribute));

        return $attribute;
    }
}