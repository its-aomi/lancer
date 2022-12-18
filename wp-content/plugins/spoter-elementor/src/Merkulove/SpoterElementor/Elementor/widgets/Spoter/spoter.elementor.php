<?php /** @noinspection PhpUndefinedClassInspection */
/**
 * Spoter for Elementor
 * Customizable hotspots for Elementor editor
 * Exclusively on https://1.envato.market/spoter-elementor
 *
 * @encoding        UTF-8
 * @version         1.0.2
 * @copyright       (C) 2018 - 2022 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Envato License https://1.envato.market/KYbje
 * @contributors    Nemirovskiy Vitaliy (nemirovskiyvitaliy@gmail.com), Dmitry Merkulov (dmitry@merkulov.design), Cherviakov Vlad (vladchervjakov@gmail.com)
 * @support         help@merkulov.design
 **/

namespace Merkulove\SpoterElementor;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

use Exception;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Merkulove\SpoterElementor\Unity\Plugin as UnityPlugin;

/** @noinspection PhpUnused */
/**
 * Spoter - Custom Elementor Widget.
 **/
class spoter_elementor extends Widget_Base {

    /**
     * Use this to sort widgets.
     * A smaller value means earlier initialization of the widget.
     * Can take negative values.
     * Default widgets and widgets from 3rd party developers have 0 $mdp_order
     **/
    public $mdp_order = 1;

    /**
     * Widget base constructor.
     * Initializing the widget base class.
     *
     * @access public
     * @throws Exception If arguments are missing when initializing a full widget instance.
     * @param array      $data Widget data. Default is an empty array.
     * @param array|null $args Optional. Widget default arguments. Default is null.
     *
     * @return void
     **/
    public function __construct( $data = [], $args = null ) {

        parent::__construct( $data, $args );

        wp_register_style(
        'mdp-spoter-elementor-admin',
        UnityPlugin::get_url() . 'src/Merkulove/Unity/assets/css/elementor-admin' . UnityPlugin::get_suffix() . '.css',
            [], UnityPlugin::get_version()
        );
        wp_register_style( 'mdp-spoter-elementor',
            UnityPlugin::get_url() . 'css/spoter-elementor' . UnityPlugin::get_suffix() . '.css',
                [],
                UnityPlugin::get_version()
        );
	    wp_register_script( 'mdp-spoter-elementor',
        UnityPlugin::get_url() . 'js/spoter-elementor' . UnityPlugin::get_suffix() . '.js',
            [ 'elementor-frontend' ],
            UnityPlugin::get_version(), true
        );

    }

    /**
     * Return a widget name.
     *
     * @return string
     **/
    public function get_name() {

        return 'mdp-spoter-elementor';

    }

    /**
     * Return the widget title that will be displayed as the widget label.
     *
     * @return string
     **/
    public function get_title() {

        return esc_html__( 'Spoter', 'spoter-elementor' );

    }

    /**
     * Set the widget icon.
     *
     * @return string
     */
    public function get_icon() {

        return 'mdp-spoter-elementor-widget-icon';

    }

    /**
     * Set the category of the widget.
     *
     * @return array with category names
     **/
    public function get_categories() {

        return [ 'general' ];

    }

    /**
     * Get widget keywords. Retrieve the list of keywords the widget belongs to.
     *
     * @access public
     *
     * @return array Widget keywords.
     **/
    public function get_keywords() {

        return [ 'Merkulove', 'Spoter', 'hotspot' ];

    }

    /**
     * Get style dependencies.
     * Retrieve the list of style dependencies the widget requires.
     *
     * @access public
     *
     * @return array Widget styles dependencies.
     **/
    public function get_style_depends() {

        return [ 'mdp-spoter-elementor', 'mdp-spoter-elementor-admin', 'elementor-icons-fa-solid' ];

    }

	/**
	 * Get script dependencies.
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @access public
     *
	 * @return array Element scripts dependencies.
	 **/
	public function get_script_depends() {

		return [ 'mdp-spoter-elementor' ];

    }

    /**
     * Add the widget controls.
     *
     * @access protected
     * @return void with category names
     **/
    protected function register_controls() {

        /** Content Tab. */
        $this->tab_content();

        /** Style Tab. */
        $this->tab_style();

    }

    /**
     * Add widget controls on Content tab.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function tab_content() {

        /** Content -> Image Content Section. */
        $this->section_content_image();

        /** Content -> Hotspot Content Section. */
        $this->section_content_hotspot();

        /** Content -> Tooltip Content Section. */
        $this->section_content_tooltip();

    }

    /**
     * Add widget controls on Style tab.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function tab_style() {

        /** Style -> Section Style Image. */
        $this->section_style_image();

        /** Style -> Section Style Tooltip. */
        $this->section_style_tooltip();

        /** Style -> Section Style Hotspot. */
        $this->section_style_hotspot();

        /** Style -> Section Style Tooltip Arrow. */
        $this->section_style_tooltip_arrow();

        /** Style -> Section Style Price. */
        $this->section_style_price();

        /** Style -> Section Style Product Image. */
        $this->section_style_product_image();

    }

    /**
     * Checks if woocommerce is active
     *
     * @since 1.0.0
     * @access public
     *
     * @return bool
     **/
    private function is_active_woocommerce() {
        $woocommerce_active = false;
        if (  in_array(
            'woocommerce/woocommerce.php',
                    apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $woocommerce_active = true;
        }

        return $woocommerce_active;
    }

    /**
     * Add widget controls: Content -> Image Content Section.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_content_image() {

        $this->start_controls_section( 'section_content_image', [
            'label' => esc_html__( 'Image', 'spoter-elementor' ),
            'tab'   => Controls_Manager::TAB_CONTENT
        ] );

        $this->add_control(
            'main_image',
            [
                'label' => esc_html__( 'Choose Image', 'spoter-elementor' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'main_image',
                'include' => [],
                'default' => 'large',
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Add widget controls: Content -> Hotspot Content Section.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_content_hotspot() {

        $this->start_controls_section( 'section_content_hotspot', [
            'label' => esc_html__( 'Hotspot', 'spoter-elementor' ),
            'tab'   => Controls_Manager::TAB_CONTENT
        ] );

        $repeater = new Repeater();

        $repeater->start_controls_tabs( 'control_tabs' );

        $repeater->start_controls_tab(
                'hotspot_tab',
                       ['label' => esc_html__( 'Hotspot', 'spoter-elementor' )]
        );

        $repeater->add_control(
            'repeater_item_title', [
                'label' => esc_html__( 'Item title', 'spoter-elementor' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Hotspot' , 'spoter-elementor' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'hotspot_type',
            [
                'label' => esc_html__( 'Type', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'icon',
                'options' => [
                    'icon'  => esc_html__( 'Icon', 'spoter-elementor' ),
                    'text' => esc_html__( 'Text', 'spoter-elementor' ),
                ],
            ]
        );

        $repeater->add_control(
            'hotspot_icon',
            [
                'label' => esc_html__( 'Icon', 'spoter-elementor' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'solid',
                ],
                'condition' => [
                     'hotspot_type' => 'icon'
                ]
            ]
        );

        $repeater->add_control(
            'hotspot_text', [
                'label' => esc_html__( 'Text', 'spoter-elementor' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Hotspot' , 'spoter-elementor' ),
                'label_block' => true,
                'condition' => [
                    'hotspot_type' => 'text'
                ]
            ]
        );

        $repeater->add_responsive_control(
            'hotspot_offset_top',
            [
                'label' => esc_html__( 'Offset top', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'hotspot_offset_left',
            [
                'label' => esc_html__( 'Offset left', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_control(
            'hotspot_link',
            [
                'label' => esc_html__( 'Enable link', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'spoter-elementor' ),
                'label_off' => esc_html__( 'No', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $repeater->add_control(
            'open_in_new_tab',
            [
                'label' => esc_html__( 'Open link in new tab', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'spoter-elementor' ),
                'label_off' => esc_html__( 'No', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'hotspot_link',
                            'operator' => '==',
                            'value' => 'yes'
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'terms' => [
                                        [
                                            'name' => 'tooltip_content_type',
                                            'operator' => '==',
                                            'value' => 'post'
                                        ],
                                    ]
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'tooltip_content_type',
                                            'operator' => '==',
                                            'value' => 'product'
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $repeater->add_control(
            'hotspot_link_url',
            [
                'label' => esc_html__( 'Link', 'spoter-elementor' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'spoter-elementor' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'hotspot_link' => 'yes',
                    'tooltip_content_type' => 'custom'
                ]
            ]
        );

        $repeater->add_control(
            'glow_effect',
            [
                'label' => esc_html__( 'Glow effect', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'spoter-elementor' ),
                'label_off' => esc_html__( 'No', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab( 'tooltip_tab',
                    ['label' => esc_html__( 'Tooltip', 'spoter-elementor' )] );

        $repeater->add_control(
            'show_tooltip',
            [
                'label' => esc_html__( 'Enable tooltip', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $repeater->add_responsive_control(
            'tooltip_width',
            [
                'label' => esc_html__( 'Tooltip width', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'separator' => 'before',
                'condition' => [
                    'show_tooltip' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-hotspot-tooltip-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'tooltip_height',
            [
                'label' => esc_html__( 'Tooltip height', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'separator' => 'after',
                'condition' => [
                    'show_tooltip' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-hotspot-tooltip-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_control(
            'tooltip_position',
            [
                'label' => esc_html__( 'Position', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top'  => esc_html__( 'Top', 'spoter-elementor' ),
                    'bottom' => esc_html__( 'Bottom', 'spoter-elementor' ),
                    'left' => esc_html__( 'Left', 'spoter-elementor' ),
                    'right' => esc_html__( 'Right', 'spoter-elementor' ),
                    'custom' => esc_html__( 'Custom', 'spoter-elementor' ),
                ],
                'condition' => [
                    'show_tooltip' => 'yes'
                ],
            ]
        );

        $repeater->add_responsive_control(
            'tooltip_offset_top',
            [
                'label' => esc_html__( 'Offset top', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'tooltip_position' => 'custom',
                    'show_tooltip' => 'yes'

                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-hotspot-tooltip' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'tooltip_offset_left',
            [
                'label' => esc_html__( 'Offset left', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'tooltip_position' => 'custom',
                    'show_tooltip' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-hotspot-tooltip' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'hotspot_tooltip_spacing',
            [
                'label' => esc_html__( 'Tooltip spacing', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'condition' => [
                    'tooltip_position!' => 'custom',
                    'show_tooltip' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-tooltip-position-bottom.mdp-spoter-elementor-hotspot-tooltip' => 'transform: translate(-50%, {{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-tooltip-position-top.mdp-spoter-elementor-hotspot-tooltip' => 'transform: translate(-50%, -{{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-tooltip-position-left.mdp-spoter-elementor-hotspot-tooltip' => 'transform: translate(-{{SIZE}}{{UNIT}}, -50%);',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-tooltip-position-right.mdp-spoter-elementor-hotspot-tooltip' => 'transform: translate({{SIZE}}{{UNIT}}, -50%);'
                ],
            ]
        );

        $repeater->add_control(
            'tooltip_content_type',
            [
                'label' => esc_html__( 'Tooltip content', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => [
                    'product'  => esc_html__( 'Product', 'spoter-elementor' ),
                    'post' => esc_html__( 'Posts/Page', 'spoter-elementor' ),
                    'custom' => esc_html__( 'Custom', 'spoter-elementor' ),
                ],
                'condition' => [
                    'show_tooltip' => 'yes'
                ],
            ]
        );

        $repeater->add_control(
            'tooltip_content_word_count',
            [
                'label' => esc_html__( 'Content word limit', 'spoter-elementor' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'default' => 10,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'show_tooltip',
                            'operator' => '==',
                            'value' => 'yes'
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'tooltip_content_type',
                                    'operator' => '==',
                                    'value' => 'product'
                                ],
                                [
                                    'name' => 'tooltip_content_type',
                                    'operator' => '==',
                                    'value' => 'post'
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        );


        $products_options = [];

        $products = [];

        if ( $this->is_active_woocommerce() ) {
            $products = wc_get_products( ['return' => 'ids', 'limit' => -1] );
            foreach ( $products as $product ) {
                $products_options[$product] = get_the_title( $product );
            }
        }

        $repeater->add_control(
            'tooltip_content_product',
            [
                'label' => esc_html__( 'Select product', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => !empty( $products_options ) ? array_keys( $products_options )[0] : '',
                'options' => $products_options,
                'condition' => [
                    'tooltip_content_type' => 'product',
                    'show_tooltip' => 'yes'

                ]
            ]
        );

        $posts = get_posts(
            [
                'post_type' => 'any',
                'exclude' => $products,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'numberposts' => -1
            ]
        );

        $options = wp_list_pluck( $posts, 'post_title', 'ID' );

        $repeater->add_control(
            'tooltip_content_post',
            [
                'label' => esc_html__( 'Select post', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => !empty( $posts ) ? array_keys( $options )[0] : '',
                'options' => $options,
                'condition' => [
                    'tooltip_content_type' => 'post',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'show_product_title',
            [
                'label' => esc_html__( 'Product title', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'tooltip_content_type' => 'product',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'show_product_image',
            [
                'label' => esc_html__( 'Product image', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'tooltip_content_type' => 'product',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'show_product_desc',
            [
                'label' => esc_html__( 'Product description', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'tooltip_content_type' => 'product',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'show_product_price',
            [
                'label' => esc_html__( 'Product price', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'tooltip_content_type' => 'product',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'show_post_title',
            [
                'label' => esc_html__( 'Post title', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'tooltip_content_type' => 'post',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'show_post_description',
            [
                'label' => esc_html__( 'Post excerpt', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'tooltip_content_type' => 'post',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'tooltip_custom_content', [
                'label' => esc_html__( 'Content', 'spoter-elementor' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__( 'Content' , 'spoter-elementor' ),
                'show_label' => false,
                'condition' => [
                    'tooltip_content_type' => 'custom',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'tooltip_content_align',
            [
                'label' => esc_html__( 'Content align', 'spoter-elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'spoter-elementor' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'spoter-elementor' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'spoter-elementor' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'condition' => [ 'show_tooltip' => 'yes' ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-hotspot-tooltip-content' => 'text-align: {{VALUE}}'
                ]
            ]
        );

        $repeater->add_control(
            'show_tooltip_arrow',
            [
                'label' => esc_html__( 'Tooltip arrow', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [ 'show_tooltip' => 'yes' ]
            ]
        );

        $repeater->add_control(
            'set_custom_arrow_position',
            [
                'label' => esc_html__( 'Custom arrow position', 'spoter-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'spoter-elementor' ),
                'label_off' => esc_html__( 'Hide', 'spoter-elementor' ),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'show_tooltip_arrow' => 'yes',
                    'show_tooltip' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'custom_arrow_position',
            [
                'label' => esc_html__( 'Arrow position', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'bottom'  => esc_html__( 'Top', 'spoter-elementor' ),
                    'top' => esc_html__( 'Bottom', 'spoter-elementor' ),
                    'right' => esc_html__( 'Left', 'spoter-elementor' ),
                    'left' => esc_html__( 'Right', 'spoter-elementor' ),
                ],
                'condition' => [
                    'set_custom_arrow_position' => 'yes',
                    'show_tooltip' => 'yes',
                    'show_tooltip_arrow' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'arrow_offset',
            [
                'label' => esc_html__( 'Offset', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'set_custom_arrow_position' => 'yes',
                    'show_tooltip' => 'yes',
                    'show_tooltip_arrow' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-arrow-top, {{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-arrow-bottom' => 'left: {{SIZE}}%; transform: translateX(-{{SIZE}}%)',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-arrow-left, {{WRAPPER}} {{CURRENT_ITEM}} .mdp-spoter-elementor-arrow-right' => 'top: {{SIZE}}%; transform: translateY(-{{SIZE}}%)'
                ],
            ]
        );


        $this->end_controls_tab();

        $this->add_control(
            'hotspot_list',
            [
                'label' => esc_html__( 'Hotspot list', 'spoter-elementor' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'repeater_item_title' => esc_html__( 'Hotspot #1', 'spoter-elementor' ),
                    ],
                ],
                'title_field' => '{{{ repeater_item_title }}}',
            ]
        );

        $repeater->end_controls_tabs();

        $this->end_controls_section();

    }

    /**
     * Add widget controls: Content -> Tooltip Content Section.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_content_tooltip()
    {

        $this->start_controls_section( 'section_content_tooltip', [
            'label' => esc_html__( 'Tooltip', 'spoter-elementor' ),
            'tab' => Controls_Manager::TAB_CONTENT
        ] );

        $this->add_control(
            'tooltip_open',
            [
                'label' => esc_html__( 'Tooltip open', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'hover',
                'options' => [
                    'click'  => esc_html__( 'Click', 'spoter-elementor' ),
                    'hover' => esc_html__( 'Hover', 'spoter-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'tooltip_close',
            [
                'label' => esc_html__( 'Tooltip close', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'on-click',
                'options' => [
                    'on-click'  => esc_html__( 'Click on empty space', 'spoter-elementor' ),
                    'on-leave' => esc_html__( 'On mouse leave from hotspot', 'spoter-elementor' ),
                ],
                'condition' => [
                    'tooltip_open' => 'hover'
                ]
            ]
        );

        $this->add_control(
            'tooltip_animation',
            [
                'label' => esc_html__( 'Animation', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'default' => 'none',
                'options' => [
                    'grow' => esc_html__( 'Grow', 'spoter-elementor' ),
                    'shrink' => esc_html__( 'Shrink', 'spoter-elementor' ),
                    'slide-up' => esc_html__( 'Slide up', 'spoter-elementor' ),
                    'slide-down' => esc_html__( 'Slide down', 'spoter-elementor' ),
                    'swing' => esc_html__( 'Swing', 'spoter-elementor' ),
                    'fade' => esc_html__( 'Fade', 'spoter-elementor' ),
                    'none' => esc_html__( 'None', 'spoter-elementor' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-hotspot-tooltip-show' => 'animation: {{VALUE}} both'
                ]
            ]
        );

        $this->add_control(
            'tooltip_animation_easing',
            [
                'label' => esc_html__( 'Easing', 'spoter-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ease',
                'options' => [
                    'ease' => esc_html__( 'Ease', 'spoter-elementor' ),
                    'ease-in' => esc_html__( 'Ease-in', 'spoter-elementor' ),
                    'ease-out' => esc_html__( 'Ease-out', 'spoter-elementor' ),
                    'ease-in-out' => esc_html__( 'Ease-in-out', 'spoter-elementor' ),
                    'linear' => esc_html__( 'Linear', 'spoter-elementor' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-hotspot-tooltip-show' => 'animation-timing-function: {{VALUE}}'
                ],
                'condition' => [
                    'tooltip_animation!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'tooltip_animation_delay',
            [
                'label' => esc_html__( 'Delay', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's' ],
                'range' => [
                    's' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 's',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-hotspot-tooltip-show' => 'animation-delay: {{SIZE}}{{UNIT}}'
                ],
                'condition' => [
                    'tooltip_animation!' => 'none',
                ],
            ]
        );


        $this->add_control(
            'tooltip_animation_duration',
            [
                'label' => esc_html__( 'Duration', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's' ],
                'range' => [
                    's' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 's',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-hotspot-tooltip-show' => 'animation-duration: {{SIZE}}{{UNIT}}'
                ],
                'condition' => [
                    'tooltip_animation!' => 'none',
                ],
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Method for generating margin padding controls.
     *
     * @param $section_id
     * @param $html_class
     * @param array $default_padding
     * @param array $default_margin
     * @return void
     * @since 1.0.0
     * @access private
     */
    private function generate_margin_padding_controls( $section_id, $html_class, $default_padding = [], $default_margin = [] ) {


        $this->add_responsive_control(
            $section_id.'_margin',
            [
                'label' => esc_html__( 'Margin', 'spoter-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'default' => $default_margin,
                'selectors' => [
                    "{{WRAPPER}} .$html_class" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            $section_id.'_padding',
            [
                'label' => esc_html__( 'Padding', 'spoter-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'default' => $default_padding,
                'selectors' => [
                    "{{WRAPPER}} .$html_class" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    }


    /**
     * Method for generating typography and tabs controls.
     *
     * @param $section_id
     * @param $opts
     * @return void
     * @since 1.0.0
     * @access private
     */
    private function generate_typography_tabs_controls( $section_id, $opts = [] ) {
        $style_opts = [
            'html_class' => array_key_exists( 'html_class', $opts ) ?
                $opts['html_class'] : '',
            'hover_html_class' => array_key_exists( 'hover_html_class', $opts ) ?
                $opts['hover_html_class'] : '',
            'additional_border_radius_class' => array_key_exists( 'additional_border_radius_class', $opts ) ?
                $opts['additional_border_radius_class'] : '',
            'additional_border_radius_hover_class' => array_key_exists( 'additional_border_radius_hover_class', $opts ) ?
                $opts['additional_border_radius_hover_class'] : '',
            'include_color' => array_key_exists( 'include_color', $opts ) ?
                $opts['include_color'] : true,
            'include_bg' => array_key_exists( 'include_bg', $opts ) ?
                $opts['include_color'] : true,
            'include_typography' => array_key_exists( 'include_typography', $opts ) ?
                $opts['include_typography'] : true,
            'include_transition' => array_key_exists( 'include_transition', $opts ) ?
                $opts['include_transition'] : true,
            'additional_color' => array_key_exists( 'additional_color', $opts ) ?
                $opts['additional_color'] : false,
            'include_css_filters' => array_key_exists( 'include_css_filters', $opts ) ?
                $opts['include_css_filters'] : false,
            'css_filters_selector' => array_key_exists( 'css_filters_selector', $opts ) ?
                $opts['css_filters_selector'] : false,
            'css_filters_selector_hover' => array_key_exists( 'css_filters_selector_hover', $opts ) ?
                $opts['css_filters_selector_hover'] : false,
            'color_prefix' => array_key_exists( 'color_prefix', $opts ) ?
                $opts['color_prefix'] : '',
            'color_class' => array_key_exists( 'color_class', $opts ) ?
                $opts['color_class'] : '',
            'color_hover_class' => array_key_exists( 'color_hover_class', $opts ) ?
                $opts['color_hover_class'] : '',
            'color_hover_selector' => array_key_exists( 'color_hover_selector', $opts ) ?
                $opts['color_hover_selector'] : '',
            'additional_color_name' => array_key_exists( 'additional_color_name', $opts ) ?
                $opts['additional_color_name'] : '',
            'additional_color_class' => array_key_exists( 'additional_color_class', $opts ) ?
                $opts['additional_color_class'] : '',
            'additional_color_hover_class' => array_key_exists( 'additional_color_hover_class', $opts ) ?
                $opts['additional_color_hover_class'] : '',
            'additional_transition_selector' => array_key_exists( 'additional_transition_selector', $opts ) ?
                $opts['additional_transition_selector'] : '',
            'typography_class' => array_key_exists( 'typography_class', $opts ) ?
                $opts['typography_class'] : '',
            'color_scheme_default' => array_key_exists( 'color_scheme_default', $opts ) ?
                $opts['color_scheme_default'] : Color::COLOR_3,
            'additional_color_scheme_default' => array_key_exists( 'additional_color_scheme_default', $opts ) ?
                $opts['additional_color_scheme_default'] : Color::COLOR_3,
            'border_radius_default' => array_key_exists( 'border_radius_default', $opts ) ?
                $opts['border_radius_default'] : []
        ];


        if ( $style_opts['include_typography'] ) {
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => $section_id . '_typography',
                    'label' => esc_html__('Typography', 'spoter-elementor'),
                    'scheme' => Typography::TYPOGRAPHY_1,
                    'selector' => "{{WRAPPER}} .".$style_opts['typography_class'],
                ]
            );
        }

        $this->start_controls_tabs( $section_id.'_style_tabs' );

        $this->start_controls_tab( $section_id.'_normal_style_tab',
            ['label' => esc_html__( 'NORMAL', 'spoter-elementor' )] );

        if ( $style_opts['include_color'] ) {

            $this->add_control(
                $section_id . '_normal_text_color',
                [
                    'label' => esc_html__($style_opts['color_prefix'].'Color', 'spoter-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'scheme' => [
                        'type' => Color::get_type(),
                        'value' => $style_opts['color_scheme_default'],
                    ],
                    'selectors' => [
                        "{{WRAPPER}} .".$style_opts['color_class'] => 'color: {{VALUE}};',
                    ],
                ]
            );

        }

        if ( $style_opts['include_css_filters'] ) {
            $this->add_group_control(
                Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'css_filters_normal',
                    'selector' => '{{WRAPPER}} .'.$style_opts['css_filters_selector'],
                ]
            );
        }

        if ( $style_opts['additional_color'] ) {
            $this->add_control(
                $section_id . '_' . $style_opts['additional_color_name'] . '_normal_text_color',
                [
                    'label' => esc_html__( $style_opts['additional_color_name'], 'spoter-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'scheme' => [
                        'type' => Color::get_type(),
                        'value' => $style_opts['additional_color_scheme_default'],
                    ],
                    'selectors' => [
                        "{{WRAPPER}} .".$style_opts['additional_color_class'] => 'color: {{VALUE}};',

                    ],
                ]
            );
        }

        if ( $style_opts['include_bg'] ) {

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => $section_id . '_normal_background',
                    'label' => esc_html__('Background type', 'spoter-elementor'),
                    'types' => ['classic', 'gradient', 'video'],
                    'selector' => "{{WRAPPER}} .".$style_opts['html_class'],
                ]
            );

        }

        $this->add_control(
            $section_id . '_separate_normal',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => $section_id.'_border_normal',
                'label' => esc_html__( 'Border Type', 'spoter-elementor' ),
                'selector' => "{{WRAPPER}} .".$style_opts['html_class'],
            ]
        );

        $this->add_responsive_control(
            $section_id.'_border_radius_normal',
            [
                'label' => esc_html__( 'Border radius', 'spoter-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => $style_opts['border_radius_default'],
                'selectors' => [
                    "{{WRAPPER}} .".$style_opts['html_class'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    $style_opts['additional_border_radius_class'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => $section_id.'_box_shadow_normal',
                'label' => esc_html__( 'Box Shadow', 'spoter-elementor' ),
                'selector' => "{{WRAPPER}} .".$style_opts['html_class'],
            ]
        );


        $this->end_controls_tab();

        $this->start_controls_tab( $section_id.'_hover_style_tab',
            ['label' => esc_html__( 'HOVER', 'spoter-elementor' )] );

        $hover_html_class = $style_opts['hover_html_class']  !== '' ?
            $style_opts['hover_html_class'] : "{{WRAPPER}} .".$style_opts['html_class'].":hover";

        if ( $style_opts['include_color'] ) {
            $this->add_control(
                $section_id . '_hover_color',
                [
                    'label' => esc_html__($style_opts['color_prefix'].'Color', 'spoter-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'scheme' => [
                        'type' => Color::get_type(),
                        'value' => $style_opts['color_scheme_default'],
                    ],
                    'selectors' => [
                        "{{WRAPPER}} .".$style_opts['color_hover_class'] => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
        }

        if ( $style_opts['include_css_filters'] ) {
            $this->add_group_control(
                Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'css_filters_hover',
                    'selector' => '{{WRAPPER}} .' . $style_opts['css_filters_selector_hover'],
                ]
            );
        }

        if ( $style_opts['additional_color'] ) {
            $this->add_control(
                $section_id . '_' . $style_opts['additional_color_name'] . '_hover_text_color',
                [
                    'label' => esc_html__( $style_opts['additional_color_name'], 'spoter-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'scheme' => [
                        'type' => Color::get_type(),
                        'value' => $style_opts['additional_color_scheme_default'],
                    ],
                    'selectors' => [
                        "{{WRAPPER}} .".$style_opts['additional_color_hover_class'] => 'color: {{VALUE}} !important;',
                    ],
                ]
            );
        }

        if ( $style_opts['include_bg'] ) {
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => $section_id . '_background_hover',
                    'label' => esc_html__('Background type', 'spoter-elementor'),
                    'types' => ['classic', 'gradient', 'video'],
                    'selector' => $hover_html_class
                ]
            );
        }

        if ( $style_opts['include_transition'] ) {
            $this->add_control(
                $section_id.'_hover_transition',
                [
                    'label' => esc_html__( 'Hover transition duration', 'spoter-elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 's' ],
                    'range' => [
                        's' => [
                            'min' => 0.1,
                            'max' => 5,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'unit' => 's',
                        'size' => 0,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .'.$style_opts['html_class'] => 'transition: color {{SIZE}}{{UNIT}}, background {{SIZE}}{{UNIT}}, box-shadow {{SIZE}}{{UNIT}}, border-radius {{SIZE}}{{UNIT}}, border {{SIZE}}{{UNIT}}, filter {{SIZE}}{{UNIT}}, stroke {{SIZE}}{{UNIT}};',
                        $style_opts['additional_transition_selector'] => 'transition: color {{SIZE}}{{UNIT}}, background {{SIZE}}{{UNIT}}, box-shadow {{SIZE}}{{UNIT}}, border-radius {{SIZE}}{{UNIT}}, border {{SIZE}}{{UNIT}}, filter {{SIZE}}{{UNIT}}, stroke {{SIZE}}{{UNIT}};;'
                    ],
                ]
            );
        }

        $this->add_control(
            $section_id.'_separate_hover',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => $section_id.'_border_hover',
                'label' => esc_html__( 'Border Type', 'spoter-elementor' ),
                'selector' => $hover_html_class,
            ]
        );

        $this->add_responsive_control(
            $section_id.'_border_radius_hover',
            [
                'label' => esc_html__( 'Border radius', 'spoter-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    $hover_html_class => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    $style_opts['additional_border_radius_hover_class'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => $section_id.'_box_shadow_hover',
                'label' => esc_html__( 'Box Shadow', 'spoter-elementor' ),
                'selector' => $hover_html_class,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
    }


    /**
     * Add widget controls: Style -> Section Style Image.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_style_image() {

        $this->start_controls_section( 'section_style_image', [
            'label' => esc_html__( 'Image', 'spoter-elementor' ),
            'tab'   => Controls_Manager::TAB_STYLE
        ] );

        $this->generate_margin_padding_controls(
            'section_style_image',
            'mdp-spoter-elementor-image'
        );

        $this->generate_typography_tabs_controls( 'section_style_image', [
            'html_class' => 'mdp-spoter-elementor-image',
            'include_color' => false,
            'include_css_filters' => true,
            'additional_border_radius_hover_class' => '{{WRAPPER}} .mdp-spoter-elementor-image:hover img',
            'additional_border_radius_class' => '{{WRAPPER}} .mdp-spoter-elementor-image img',
            'css_filters_selector' => 'mdp-spoter-elementor-image img',
            'css_filters_selector_hover' => 'mdp-spoter-elementor-image:hover img',
            'include_typography' => false

        ] );

        $this->end_controls_section();

    }

    /**
     * Add widget controls: Style -> Section Style Tooltip.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_style_tooltip() {

        $this->start_controls_section( 'section_style_tooltip', [
            'label' => esc_html__( 'Tooltip', 'spoter-elementor' ),
            'tab'   => Controls_Manager::TAB_STYLE
        ] );

        $default = [
            'top' => '10',
            'right' => '10',
            'bottom' => '10',
            'left' => '10',
            'isLinked' => false
        ];

        $this->generate_margin_padding_controls(
            'section_style_tooltip',
            'mdp-spoter-elementor-hotspot-tooltip-wrapper',
            $default
        );

        $this->generate_typography_tabs_controls( 'section_style_tooltip', [
            'html_class' => 'mdp-spoter-elementor-hotspot-tooltip-wrapper',
            'include_color' => true,
            'include_typography' => true,
            'color_class' => 'mdp-spoter-elementor-hotspot-tooltip-content',
            'color_hover_class' => 'mdp-spoter-elementor-hotspot-tooltip-wrapper:hover mdp-spoter-elementor-hotspot-tooltip-content',
            'typography_class' => 'mdp-spoter-elementor-hotspot-tooltip-content',
        ] );

        $this->end_controls_section();

    }

    /**
     * Add widget controls: Style -> Section Style Tooltip Arrow.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_style_tooltip_arrow() {
        $this->start_controls_section( 'section_style_tooltip_arrow', [
            'label' => esc_html__( 'Tooltip arrow', 'spoter-elementor' ),
            'tab'   => Controls_Manager::TAB_STYLE
        ] );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => esc_html__( 'Arrow size', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-arrow-left' => 'border-width: {{SIZE}}{{UNIT}} !important; right: -{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mdp-spoter-elementor-arrow-right' => 'border-width: {{SIZE}}{{UNIT}} !important; left: -{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mdp-spoter-elementor-arrow-top' => 'border-width: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .mdp-spoter-elementor-arrow-bottom' => 'border-width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'tabs_arrow_color',
            [
                'label' => esc_html__( 'Arrow Color', 'spoter-elementor' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000',
                'scheme' => [
                    'type' => Color::get_type(),
                    'value' => Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-arrow-left' => 'border-left-color: {{VALUE}}',
                    '{{WRAPPER}} .mdp-spoter-elementor-arrow-right' => 'border-right-color: {{VALUE}}',
                    '{{WRAPPER}} .mdp-spoter-elementor-arrow-top' => 'border-top-color: {{VALUE}}',
                    '{{WRAPPER}} .mdp-spoter-elementor-arrow-bottom' => 'border-bottom-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }


   /**
     * Add widget controls: Style -> Section Style Hotspot.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_style_hotspot() {

        $this->start_controls_section( 'section_style_hotspot', [
            'label' => esc_html__( 'Hotspot', 'spoter-elementor' ),
            'tab'   => Controls_Manager::TAB_STYLE
        ] );

        $padding_default = [
            'top' => '20',
            'right' => '20',
            'bottom' => '20',
            'left' => '20',
            'isLinked' => false
        ];

        $this->generate_margin_padding_controls(
            'section_style_hotspot',
            'mdp-spoter-elementor-hotspot-wrapper',
                     $padding_default
        );

        $border_radius_default = [
            'top' => '50',
            'right' => '50',
            'bottom' => '50',
            'left' => '50',
            'isLinked' => false
        ];

        $this->add_responsive_control(
            'hotspot_icon_offset_top',
            [
                'label' => esc_html__( 'Icon offset top', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-hotspot-icon' => 'top: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'hotspot_icon_offset_left',
            [
                'label' => esc_html__( 'Icon offset left', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-hotspot-icon' => 'left: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'hotspot_height',
            [
                'label' => esc_html__( 'Height', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-hotspot-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hotspot_width',
            [
                'label' => esc_html__( 'Width', 'spoter-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mdp-spoter-elementor-hotspot-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->generate_typography_tabs_controls( 'section_style_hotspot', [
            'html_class' => 'mdp-spoter-elementor-hotspot-wrapper, {{WRAPPER}} .mdp-spoter-elementor-hotspot-glow-effect::before',
            'hover_html_class' => '{{WRAPPER}} .mdp-spoter-elementor-hotspot-wrapper:hover, {{WRAPPER}} .mdp-spoter-elementor-hotspot-link:hover .mdp-spoter-elementor-hotspot-wrapper, {{WRAPPER}} .mdp-spoter-elementor-hotspot-link:hover .mdp-spoter-elementor-hotspot-glow-effect::before, {{WRAPPER}} .mdp-spoter-elementor-hotspot-wrapper:hover.mdp-spoter-elementor-hotspot-glow-effect::before',
            'include_color' => true,
            'color_class' => 'mdp-spoter-elementor-hotspot-wrapper',
            'color_hover_class' => 'mdp-spoter-elementor-hotspot-wrapper:hover',
            'include_typography' => true,
            'typography_class' => 'mdp-spoter-elementor-hotspot-wrapper',
            'border_radius_default' => $border_radius_default
        ] );

        $this->end_controls_section();

    }

    /**
     * Add widget controls: Style -> Section Style Price.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_style_price() {

        $this->start_controls_section( 'section_style_price', [
            'label' => esc_html__( 'Price', 'spoter-elementor' ),
            'tab'   => Controls_Manager::TAB_STYLE
        ] );

        $this->generate_margin_padding_controls(
            'section_style_price',
            'mdp-spoter-elementor-hotspot-tooltip-product-desc .price'
        );

        $this->generate_typography_tabs_controls( 'section_style_price', [
            'html_class' => 'mdp-spoter-elementor-hotspot-tooltip-product-desc .price',
            'include_color' => true,
            'include_typography' => true,
            'color_class' => 'mdp-spoter-elementor-hotspot-tooltip-product-desc .price',
            'color_hover_class' => 'mdp-spoter-elementor-hotspot-tooltip-product-desc .price:hover',
            'typography_class' => 'mdp-spoter-elementor-hotspot-tooltip-product-desc .price',

        ] );

        $this->end_controls_section();

    }

    /**
     * Add widget controls: Style -> Section Style Product Image.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function section_style_product_image() {

        $this->start_controls_section( 'section_style_product_image', [
            'label' => esc_html__( 'Product image', 'spoter-elementor' ),
            'tab'   => Controls_Manager::TAB_STYLE
        ] );

        $this->generate_margin_padding_controls(
            'section_style_product_image',
            'mdp-spoter-elementor-hotspot-tooltip-product-image'
        );

        $this->generate_typography_tabs_controls( 'section_style_product_image', [
            'html_class' => 'mdp-spoter-elementor-hotspot-tooltip-product-image',
            'include_color' => false,
            'include_typography' => false
        ] );

        $this->end_controls_section();

    }

    /**
     * Method for generating tooltip arrow.
     *
     * @param $item
     * @return string
     * @since 1.0.0
     * @access private
     */
    private function create_tooltip_arrow( $item ) {

        $arrow_position = ( $item['tooltip_position'] !== 'custom' && $item['set_custom_arrow_position'] !== 'yes' ) ?
                          $item['tooltip_position'] : (
                          ( $item['tooltip_position'] === 'custom' && $item['set_custom_arrow_position'] !== 'yes' ) ?
                          'top' :
                          ( ( $item['set_custom_arrow_position'] === 'yes' ) ?
                          $item['custom_arrow_position'] :
                          '' ) );

        return sprintf(
            '<div class="mdp-spoter-elementor-tooltip-arrow mdp-spoter-elementor-arrow-%s"></div>',
                   esc_attr( $arrow_position )
        );
    }

    /**
     * Method for generating tooltip.
     *
     * @param $item
     * @return string
     * @since 1.0.0
     * @access private
     */
    private function create_tooltip( $item ) {

        $position_class = 'mdp-spoter-elementor-tooltip-position-'.$item['tooltip_position'];
        $tooltip_arrow = $item['show_tooltip_arrow'] === 'yes' ?
            $this->create_tooltip_arrow( $item ) : '';

        return sprintf(
            '<span class="mdp-spoter-elementor-hotspot-tooltip %s">
                        <span class="mdp-spoter-elementor-hotspot-tooltip-wrapper">
                            %s %s
                        </span>
                    </span>',
                   esc_attr( $position_class ),
                   $this->create_tooltip_content( $item ),
                   $tooltip_arrow
        );
    }


    /**
     * Method for generating tooltip content part.
     *
     * @param $part_class
     * @param $part_text
     * @return string
     * @since 1.0.0
     * @access private
     */
    private function create_tooltip_content_part( $part_class, $part_text ) {
        return sprintf(
            '<div class="%s">%s</div>',
                   $part_class,
                   $part_text
        );
    }


    /**
     * Method for generating whole tooltip content.
     *
     * @param $item
     * @return string
     * @since 1.0.0
     * @access private
     */
    private function create_tooltip_content( $item ) {

        switch ( $item['tooltip_content_type'] ) {
            case 'product':
                if ( !$this->is_active_woocommerce() ) { return; }
                global $product;
                $product = wc_get_product( esc_sql( $item['tooltip_content_product'] ) );
                $product_title = $item['show_product_title'] === 'yes' && !empty( $product ) ?
                    $this->create_tooltip_content_part(
                        'mdp-spoter-elementor-hotspot-tooltip-product-title',
                        esc_html( get_the_title( esc_sql( $item['tooltip_content_product'] ) ) )
                    ) : '';
                $product_image = $item['show_product_image'] === 'yes' && !empty( $product ) ?
                    $this->create_tooltip_content_part(
                        'mdp-spoter-elementor-hotspot-tooltip-product-image',
                        $product->get_image( $size = 'shop_thumbnail' )
                    ) : '';
                $product_desc = $item['show_product_desc'] === 'yes' && !empty( $product ) ?
                    $this->create_tooltip_content_part(
                        'mdp-spoter-elementor-hotspot-tooltip-product-desc',
                        esc_html(
                            wp_trim_words(
                                get_the_excerpt( esc_sql( $item['tooltip_content_product'] ) ),
                                $item['tooltip_content_word_count'], '...'
                            )
                        )
                    ) : '';
                $product_price = $item['show_product_price'] === 'yes' && !empty( $product ) ?
                    $this->create_tooltip_content_part(
                        'mdp-spoter-elementor-hotspot-tooltip-product-desc',
                        wc_get_template_html( '/single-product/price.php' )
            ) : '';
                return sprintf(
                    '<span class="mdp-spoter-elementor-hotspot-tooltip-content">%s %s %s %s</span>',
                            $product_image,
                            $product_title,
                            $product_desc,
                            $product_price
                );

            case 'post' :
                $post = !empty( $item['tooltip_content_post'] ) ? get_post( $item['tooltip_content_post'] ) : '';
                $post_title = $item['show_post_title'] === 'yes' && !empty( $post ) ?
                    $this->create_tooltip_content_part(
                        'mdp-spoter-elementor-hotspot-tooltip-product-title',
                                 esc_html( $post->post_title )
                    ) : '';
                $post_excerpt = $item['show_post_description'] === 'yes' && !empty( $post ) ?
                    $this->create_tooltip_content_part(
                        'mdp-spoter-elementor-hotspot-tooltip-product-desc',
                                 esc_html(
                                     wp_trim_words(
                                         get_the_excerpt( $post->ID ),
                                         $item['tooltip_content_word_count'], '...'
                                     )
                                 )
                    ) : '';
                return sprintf(
                    '<span class="mdp-spoter-elementor-hotspot-tooltip-content">%s %s</span>',
                           $post_title,
                           $post_excerpt
                );

            case 'custom':
                return sprintf(
                    '<span class="mdp-spoter-elementor-hotspot-tooltip-content">%s</span>',
                           wp_kses_post( $item['tooltip_custom_content'] )
                );

            default:
                return '';
        }
    }

    /**
     * Method for generating hotspot icon.
     *
     * @param $item
     * @return string
     * @since 1.0.0
     * @access private
     */
    private function create_hotspot_icon( $item ) {
        $icon = $item['hotspot_icon']['library'] === 'svg' ?
            Icons_Manager::render_uploaded_svg_icon( $item['hotspot_icon']['value'] ) :
            '<i class="'.esc_attr( $item['hotspot_icon']['value'] ).'"></i>';

        return sprintf(
            '<span class="mdp-spoter-elementor-hotspot-icon">%s</span>',
                   $icon
        );
    }

    /**
     * Method for generating hotspot text.
     *
     * @param $item
     * @return string
     * @since 1.0.0
     * @access private
     */
    private function create_hotspot_text( $item ) {
        return sprintf(
            '<span class="mdp-spoter-elementor-hotspot-text">%s</span>',
            esc_html( $item['hotspot_text'] )
        );
    }

    /**
     * Method for generating all hotspots.
     *
     * @param $settings
     * @return string
     * @since 1.0.0
     * @access private
     */
    private function create_hotspots( $settings ) {
        $hotspots = [];
        $hotspot_link_attrs = '';

        foreach ( $settings['hotspot_list'] as $hotspot ) {

            $hotspot_link = ( $hotspot['tooltip_content_type'] === 'post'  ) ?
                get_post_permalink( $hotspot['tooltip_content_post'] ) :
                ( ( $hotspot['tooltip_content_type'] === 'product' ) ?
                get_permalink( $hotspot['tooltip_content_product'] ) :
                ( ( $hotspot['tooltip_content_type'] === 'custom' && isset( $hotspot['hotspot_link_url']['url']  ) ) ?
                $hotspot['hotspot_link_url']['url'] :
                '' ) );

            $target = isset( $hotspot['hotspot_link_url'] ) && $hotspot['hotspot_link_url']['is_external'] ?
                'target="_blank" ' :
                '';
            $nofollow = isset( $hotspot['hotspot_link_url'] ) && $hotspot['hotspot_link_url']['nofollow'] ?
                ' rel="nofollow" ' :
                '';

            if ( $hotspot['tooltip_content_type'] === 'custom' ) {
                $hotspot_link_attrs =  $target . $nofollow;
            } elseif ( $hotspot['open_in_new_tab'] === 'yes' ) {
                $hotspot_link_attrs = 'target="_blank"';
            }

            $hotspot_type = '';
            if ( $hotspot['hotspot_type'] === 'icon' ) {
                $hotspot_type = $this->create_hotspot_icon( $hotspot );
            } elseif ( $hotspot['hotspot_type'] === 'text' ) {
                $hotspot_type = $this->create_hotspot_text( $hotspot );
            }

            $hotspot_glow_effect = $hotspot['glow_effect'] === 'yes' ?
                                      'mdp-spoter-elementor-hotspot-glow-effect' :
                                      '';

            $tooltip = $hotspot['show_tooltip'] === 'yes' ?
                $this->create_tooltip( $hotspot ) :
                '';
            if ( $hotspot['hotspot_link'] === 'yes' ) {
                $hotspots[] = sprintf(
            '<a class="mdp-spoter-elementor-hotspot-link" href="%s" %s>
                        <span class="mdp-spoter-elementor-hotspot">
                            <div class="mdp-spoter-elementor-hotspot-wrapper %s elementor-repeater-item-%s">
                               %s %s
                            </div>   
                        </span>
                    </a>',
                    esc_url( $hotspot_link ),
                    esc_attr( $hotspot_link_attrs ),
                    esc_attr( $hotspot_glow_effect ),
                    esc_attr( $hotspot['_id'] ),
                    $hotspot_type,
                    $tooltip
                );
            } else {
                $hotspots[] = sprintf(
                    '<span class="mdp-spoter-elementor-hotspot">
                            <div class="mdp-spoter-elementor-hotspot-wrapper %s elementor-repeater-item-%s">
                               %s %s
                            </div>   
                        </span>',
                    esc_attr( $hotspot_glow_effect ),
                    esc_attr( $hotspot['_id'] ),
                    $hotspot_type,
                    $tooltip
                );
            }
        }

        return implode( ' ', $hotspots );
    }

    /**
     * Method for generating hotspot image.
     *
     * @param $settings
     * @return string
     * @since 1.0.0
     * @access private
     */
    private function create_hotspot_image( $settings ) {
        $image = wp_kses_post( Group_Control_Image_Size::get_attachment_image_html(
                         $settings,
            'main_image' )
        );

        return sprintf(
            '<div class="mdp-spoter-elementor-image">%s %s</div>',
            $image,
            $this->create_hotspots( $settings )
        );
    }


    /**
     * Render Frontend Output. Generate the final HTML on the frontend.
     *
     * @access protected
     *
     * @return void
     **/
    protected function render() {
        $settings = $this->get_settings_for_display();

        echo sprintf(
            '<!-- Start Spoter for Elementor WordPress Plugin -->
                   <div class="mdp-spoter-elementor-box" 
                    data-tooltip-open="%s"
                    data-tooltip-close="%s"
                   >%s</div>
                   <!-- End Spoter for Elementor WordPress Plugin -->',
                    esc_attr( $settings['tooltip_open'] ),
                    esc_attr( $settings['tooltip_close'] ),
                    $this->create_hotspot_image( $settings )
        );
    }

    /**
     * Return link for documentation
     * Used to add stuff after widget
     *
     * @access public
     *
     * @return string
     **/
    public function get_custom_help_url() {

        return 'https://docs.merkulov.design/tag/spoter';

    }

}
