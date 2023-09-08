<?php
namespace Woolentor\Modules\Popup_Builder\Frontend;

use Woolentor\Modules\Popup_Builder\Helper;
use Woolentor\Modules\Popup_Builder\Admin\Manage_Metabox;
use Woolentor\Modules\Popup_Builder_Pro\Frontend\Popup_Rules_Checker_Pro;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Manage_Popup {

    private static $_instance = null;

    /**
     * Get Instance
     */
    public static function get_instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    function __construct(){
        add_action( 'wp_footer', array($this, 'render_modal_markup_template') );
    }

    /**
     * Render modal markup template.
     */
    public function render_modal_markup_template(){
        global $post;
	
        $popups = $this->get_popups();

        if( empty($popups) ){
            return;
        }

        foreach( $popups as $popup_id ){
            setup_postdata( $post = $popup_id );

            $this->render_single_popup( $popup_id );
        }

        wp_reset_postdata();
    }

    public function render_single_popup( $popup_id ){
        $popup_meta     = get_post_meta( $popup_id, '_wlpb_popup_seetings', true );

        // Check the popup display status. If any rule matched with current page then display the popup.
        $popup_display_rules  = !empty($popup_meta['conditions']) ? $popup_meta['conditions'] : array();

        if( Helper::is_pro_version_active() && class_exists('Woolentor\Modules\Popup_Builder_Pro\Frontend\Popup_Rules_Checker_Pro') ){
            $popup_display_status = Popup_Rules_Checker_Pro::get_instance()->check_rules( $popup_id, $popup_display_rules );
        } else {
            $popup_display_status = Popup_Rules_Checker::get_instance()->check_rules( $popup_id, $popup_display_rules );
        }
        
        $popup_display_status = apply_filters( 'wlpb_popup_display_status', $popup_display_status, $popup_id, $popup_display_rules );

        if( !$popup_display_status ){
            return;
        }

        // Prepare default values so we don't need to check isset() for each value.
        $popup_default_settings = array();
        $popup_default_settings = array_merge($popup_default_settings, Manage_Metabox::get_instance()->get_default_values('general_fields'));
        $popup_default_settings = array_merge($popup_default_settings, Manage_Metabox::get_instance()->get_default_values('customization_fields'));

        // @todo: add the below options later.
        $popup_default_settings['popup_display_as']   = 'modal';
        $popup_default_settings['popup_bar_float']    = '';
        $popup_default_settings['popup_bar_position'] = 'top';
        $popup_default_settings['css_class']          = '';

        // Merge default values with saved values.
        $popup_settings = wp_parse_args($popup_meta, $popup_default_settings);

        wp_enqueue_style( 'wlpb-frontend' );
        wp_enqueue_script( 'wlpb-frontend' );
        ?>

        <?php $popup_area_inline_css = Helper::generate_inline_css($popup_id, $popup_settings, 'popup_area');  ?>
        <div id="wlpb-popup-id-<?php echo esc_attr($popup_id) ?>" class="wlpb-popup <?php echo esc_attr($this->get_popup_classes($popup_id, $popup_settings)) ?>" data-wlpb_popup_id="<?php echo esc_attr($popup_id) ?>" data-settings=<?php echo json_encode($this->get_data_settings( $popup_id, $popup_settings )) ?> style="<?php echo esc_attr($popup_area_inline_css) ?>">
            <div class="wlpb-popup-inner <?php echo esc_attr($this->get_popup_inner_classes($popup_id, $popup_settings)) ?>">
                <div class="wlpb-popup-overlay"></div>

                <!-- Any style for popup modal will apply here in the .wlpb-popup-container -->
                <?php $popup_container_inline_css = Helper::generate_inline_css($popup_id, $popup_settings, 'popup_container'); ?>
                <div class="wlpb-popup-container" style="<?php echo esc_attr($popup_container_inline_css) ?>">
                    <span class="wlpb-popup-close-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"></rect><g><path d="M14.95 6.46L11.41 10l3.54 3.54-1.41 1.41L10 11.42l-3.53 3.53-1.42-1.42L8.58 10 5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"></path></g></svg>
                    </span>

                    <?php $popup_container_inner_inline_css = Helper::generate_inline_css($popup_id, $popup_settings, 'popup_container_inner'); ?>
                    <div class="wlpb-popup-container-inner ps" style="<?php echo esc_attr($popup_container_inner_inline_css) ?>">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }

    public function get_popups(){
        return get_posts( array(
            'post_type'         => 'woolentor-template',
            'posts_per_page'    => Helper::get_instance()->get_dropdown_posts_limit(),
            'fields'            => 'ids',
            // Include templates where key = woolentor_template_meta_type,vlaue = popup.
            // and key = _wlpb_popup_seetings,value = not empty.
            'meta_query' => array(
                array(
                    'key'     => 'woolentor_template_meta_type',
                    'value'   => 'popup',
                    'compare' => '='
                ),
            )
        ) );
    }

    public function get_popup_classes( $popup_id, $popup_settings ){
        $popup_classes = array(
            'wlpb-popup-' . $popup_id,
            'wlpb-popup-' . $popup_settings['popup_display_as'], // modal, bar
            'wlpb-popup-pv-' . $popup_settings['popup_vertical_position'], // top, center, bottom
            'wlpb-popup-ph-' . $popup_settings['popup_horizontal_position'], // left, center, right
            'wlpb-popup-' . $popup_settings['css_class'], // custom css class
        );

        // Add the wlpb-popup-bar class if the popup is a bar.
        if( $popup_settings['popup_display_as'] == 'bar' ){
            $popup_classes[] = 'wlpb-popup-bar-' . $popup_settings['popup_bar_position']; // top, bottom
        }

        // Add the wlpb-popup-bar-float class if the popup is a bar and the float option is enabled.
        if( $popup_settings['popup_display_as'] == 'bar' && $popup_settings['popup_bar_float'] ){
            $popup_classes[] = 'wlpb-popup-bar-float';
        }

        return implode(' ', $popup_classes);
    }

    public function get_popup_inner_classes( $popup_id, $popup_settings ){
        $popup_inner_classes = array(
            'wlpb-popup-pv-' . $popup_settings['popup_vertical_position'], // top, center, bottom
            'wlpb-popup-ph-' . $popup_settings['popup_horizontal_position'], // left, center, right
        );

        return implode(' ', $popup_inner_classes);
    }

    public function get_data_settings( $popup_id, $popup_settings ){
        $data_settings = array();
        $all_fields_default_values = Manage_Metabox::get_instance()->get_default_values();

        // $disable_page_scroll, $dismiss_on_esc_key, $dismiss_on_overlay_click, $close_after_page_scroll, $dismiss_automatically, $dismiss_automatically_delay
        extract($all_fields_default_values);

        $data_settings['triggers']  = !empty($popup_settings['triggers']) ? $popup_settings['triggers'] : Manage_Metabox::get_instance()->get_default_values('trigger_fields');
        
        $data_settings['disable_page_scroll']      = !empty($popup_settings['disable_page_scroll']) ? $popup_settings['disable_page_scroll'] : $disable_page_scroll;
        $data_settings['dismiss_on_esc_key']       = !empty($popup_settings['dismiss_on_esc_key']) ? $popup_settings['dismiss_on_esc_key'] : $dismiss_on_esc_key;
        $data_settings['dismiss_on_overlay_click'] = !empty($popup_settings['dismiss_on_overlay_click']) ? $popup_settings['dismiss_on_overlay_click'] : $dismiss_on_overlay_click;
        $data_settings['close_after_page_scroll']  = !empty($popup_settings['close_after_page_scroll']) ? $popup_settings['close_after_page_scroll'] : $close_after_page_scroll;
        $data_settings['dismiss_automatically']    = !empty($popup_settings['dismiss_automatically']) ? $popup_settings['dismiss_automatically'] : $dismiss_automatically;
        $data_settings['dismiss_automatically_delay'] = !empty($popup_settings['dismiss_automatically_delay']) ? $popup_settings['dismiss_automatically_delay'] : $dismiss_automatically_delay;

        // @todo: Update it later when we will add more popup display options.
        $data_settings['popup_display_as']         = !empty($popup_settings['popup_display_as']) ? $popup_settings['popup_display_as'] : 'modal';
        $data_settings['popup_bar_float']          = !empty($popup_settings['popup_bar_float']) ? $popup_settings['popup_bar_float'] : 0;
        $data_settings['popup_bar_position']       = !empty($popup_settings['popup_bar_position']) ? $popup_settings['popup_bar_position'] : 'bottom';

        return $data_settings;
    }
}