<?php
namespace Woolentor\Modules\Popup_Builder\Admin;

use Woolentor\Modules\Popup_Builder as Popup_Builder;
use Woolentor\Modules\Popup_Builder\Helper;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Manage_Metabox{

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
        // Add Popup settings button for classic & gutenber editor.
        add_action( 'add_meta_boxes', array($this, 'add_meta_box') );

        // Render modal markup for the popup settings.
        add_action( 'admin_footer', array($this, 'render_modal_markup_template') );

        // Render modal markup for the elementor editor.
        add_action( 'elementor/editor/footer', array($this, 'render_modal_markup_template') );
    }

    /**
     * Add metabox
     */
    public function add_meta_box(){
        if( Helper::get_instance()->is_popup_edit_page() ){
            add_meta_box(
                'wlpb-metabox-button-side',
                __( 'Popup Builder', 'woolentor' ),
                array($this, 'render_meta_box'),
                'woolentor-template',
                'side',
                'core'
            );
        }
    }

    /**
     * Render metabox
     */
    public function render_meta_box(){
        $post_id  = get_the_ID();
        ?>
        <div class="wlpb-metabox-button-wrapper">
            <button type="button" class="button button-primary components-button is-primary" data-post_id="<?php echo esc_attr($post_id) ?>"><?php _e( 'Popup Settings', 'woolentor' ) ?></button>
            <!-- button button-primary classes for the Clssic editor support -->
        </div>
        <?php
    }

    /**
     * Render modal
     */
    public function render_modal_markup_template(){
        if( Helper::get_instance()->is_popup_edit_page() ){
            ob_start();
            include_once( Popup_Builder\MODULE_PATH. '/includes/admin/tmpl-popup-builder-modal.php' );

            echo ob_get_clean();

            // Pro upgrade.
            $dashboard_popup_file = WOOLENTOR_ADDONS_PL_PATH . '/includes/admin/templates/dashboard-popup.php';
            if( !Helper::is_pro_version_active() && file_exists( $dashboard_popup_file ) ){
                ob_start();
                include_once( $dashboard_popup_file );
    
                echo ob_get_clean();
            }
        }
    }
    /**
     * Render modal
     */
    public function get_fields( $group = '' ){
        $fields = array();

        $fields['trigger_fields'] = array(
            array(
                'name'  => 'on_page_load',
                'label' => __('On Page Load', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field',
                'default' => '1'
            ),

            array(
                'name'  => 'page_load_delay',
                'label' => __('Page Load Delay (ms)', 'woolentor'),
                'type'  => 'number',
                'class' => 'wlpb-field',
                'default' => '0'
            ),

            array(
                'name'  => 'on_scroll',
                'label' => __('On Scroll', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field',
                'default' => ''
            ),

            array(
                'name'  => 'scroll_percentage',
                'label' => __('Scroll Percentage (%)', 'woolentor'),
                'type'  => 'number',
                'class' => 'wlpb-field',
                'default' => '',
            ),

            array(
                'name'  => 'scroll_direction',
                'label' => __('Scroll Direction', 'woolentor'),
                'type'  => 'select',
                'class' => 'wlpb-field',
                'default' => 'down',
                'options' => array(
                    'down' => __('Down', 'woolentor'),
                    'up'   => __('Up', 'woolentor'),
                )
            ),

            array(
                'name'  => 'on_click',
                'label' => __('On Click', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field',
                'default' => ''
            ),

            array(
                'name'  => 'clicks_count',
                'label' => __('Clicks Count', 'woolentor'),
                'type'  => 'number',
                'class' => 'wlpb-field',
                'default' => '1'
            ),

            array(
                'name'  => 'on_inactivity',
                'label' => __('On Inactivity', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field',
                'default' => ''
            ),

            array(
                'name'  => 'inactivity_time',
                'label' => __('Inactivity Time (ms)', 'woolentor'),
                'type'  => 'number',
                'class' => 'wlpb-field',
                'default' => '10'
            ),

            array(
                'name'  => 'on_exit_intent',
                'label' => __('On Exit Intent', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field',
                'default' => ''
            ),
        );

        $fields['general_fields'] = array(
            array(
                'name'  => 'disable_page_scroll',
                'label' => __('Disable Page Scroll', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field',
                'default' => ''
            ),
            array(
                'name'  => 'dismiss_on_esc_key',
                'label' => __('Dismiss on Esc Key', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field',
                'default' => 'on'
            ),
            array(
                'name'  => 'dismiss_on_overlay_click',
                'label' => __('Dismiss on Overlay Click', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field wlpb-pro-adv',
                'default' => '',
            ),
            array(
                'name'  => 'close_after_page_scroll',
                'label' => __('Close After Page Scroll', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field wlpb-pro-opacity',
                'default' => ''
            ),
            array(
                'name'  => 'dismiss_automatically',
                'label' => __('Dismiss Automatically', 'woolentor'),
                'type'  => 'checkbox',
                'class' => 'wlpb-field wlpb-pro-opacity',
                'default' => ''
            ),
            array(
                'name'  => 'dismiss_automatically_delay',
                'label' => __('Dismiss Automatically Delay (sec)', 'woolentor'),
                'type'  => 'number',
                'min'  	=> 0,
                'step'  => .5,
                'class' => 'wlpb-field wlpb-pro-opacity',
                'default' => '',
                'placeholder' => '10'
            ),

        );

        $fields['customization_fields'] = array(
            array(
                'name'  => 'popup_vertical_position',
                'label' => __('Position Vertical', 'woolentor'),
                'type'  => 'select',
                'options' => array(
                    'top' => __('Top', 'woolentor'),
                    'center' => __('Center', 'woolentor'),
                    'bottom' => __('Bottom', 'woolentor'),
                ),
                'class' => 'wlpb-field wlpb-pro-adv',
                'default' => 'center',
            ),
            array(
                'name'  => 'popup_horizontal_position',
                'label' => __('Position Horizontal', 'woolentor'),
                'type'  => 'select',
                'options' => array(
                    'left' => __('Left', 'woolentor'),
                    'center' => __('Center', 'woolentor'),
                    'right' => __('Right', 'woolentor'),
                ),
                'class' => 'wlpb-field wlpb-pro-opacity',
                'default' => 'center',
            ),
            array(
                'name'  => 'width',
                'label' => __('Width', 'woolentor'),
                'type'  => 'text',
                'desc' 	=> __('Example: 500px', 'woolentor'),
                'class' => 'wlpb-field',
                'default' => ''
            ),
            array(
                'name'  => 'height',
                'label' => __('Height', 'woolentor'),
                'type'  => 'text',
                'desc' 	=> __('Example: 500px', 'woolentor'),
                'class' => 'wlpb-field',
                'default' => ''
            ),
            array(
                'name'  => 'z_index',
                'label' => __('Z-Index', 'woolentor'),
                'type'  => 'number',
                'desc' 	=> __('Example: 9999', 'woolentor'),
                'class' => 'wlpb-field',
                'default' => ''
            ),
            array(
                'name'  => 'margin',
                'label' => __('Margin', 'woolentor'),
                'type'  => 'text',
                'desc' 	=> __('Example: <code>10px</code> or <code>10px 20px 10px 20px</code>', 'woolentor'),
                'class' => 'wlpb-field',
                'default' => ''
            ),
            array(
                'name'  => 'padding',
                'label' => __('Padding', 'woolentor'),
                'type'  => 'text',
                'desc' 	=> __('Example: <code>10px</code> or <code>10px 20px 10px 20px</code>', 'woolentor'),
                'class' => 'wlpb-field',
                'default' => ''
            ),
        );

        $fields = apply_filters( 'wlpb_module_fields', $fields );

        if( $group && isset($fields[$group]) ){
            return $fields[$group];
        }

        return $fields;
    }

    public function get_default_values( $group = '' ){
        $defaults = array();

        $fields = $this->get_fields();

        // Trigger Fields.
        foreach ($fields['trigger_fields'] as $field) {
            $defaults[$field['name']] = $field['default'];
        }

        // General Fields.
        foreach ($fields['general_fields'] as $field) {
            $defaults[$field['name']] = $field['default'];
        }

        // Customization Fields.
        foreach ($fields['customization_fields'] as $field) {
            $defaults[$field['name']] = $field['default'];
        }

        if( $group && isset($defaults[$group]) ){
            return $defaults[$group];
        }

        return $defaults;
    }
}