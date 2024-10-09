<?php

namespace UltimateStoreKit\Modules\EddStandardGrid\Widgets;

use UltimateStoreKit\Base\Module_Base;
use UltimateStoreKit\Traits\Global_EDD_Widget_Controls;
use UltimateStoreKit\Traits\Global_Widget_Template;
use UltimateStoreKit\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class EDD_Standard_Grid extends Module_Base {
    use Global_EDD_Widget_Controls;
    use Global_Widget_Template;
    use Group_Control_Query;
    /**
     * @var \WP_Query
     */
    private $_query = null;
    public function get_name() {
        return 'usk-edd-standard-grid';
    }

    public function get_title() {
        return esc_html__('EDD Standard Grid', 'ultimate-store-kit');
    }

    public function get_icon() {
        return 'usk-widget-icon usk-icon-edd-standard-grid';
    }

    public function get_categories() {
        return ['ultimate-store-kit'];
    }

    public function get_keywords() {
        return ['product', 'product grid', 'table', 'wc', 'grid', 'list'];
    }



    public function get_style_depends() {
        if ($this->usk_is_edit_mode()) {
            return ['usk-all-styles'];
        } else {
            return ['ultimate-store-kit-font', 'usk-edd-standard-grid'];
        }
    }

    public function get_query() {
        return $this->_query;
    }
    protected function register_controls() {
        /**
         * render controls layout
         */
        $this->register_global_edd_controls_grid_layout();
        $this->register_global_edd_controls_query();
        $this->register_global_edd_controls_additional();

        /**
         * render style controls
         */
        $this->register_global_edd_style_controls_items();
        // $this->register_global_edd_controls_grid_image();
        $this->register_global_edd_style_controls_title();
        $this->register_global_edd_style_controls_category();
        $this->register_global_edd_style_controls_price();
        $this->register_global_edd_style_controls_action_button();
        $this->register_global_edd_controls_grid_pagination();
    }

    public function render_header() {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('usk-edd-standard-grid', 'class', ['usk-edd-standard-grid'], true);
        $this->add_render_attribute('usk-edd-standard-grid', 'data-filter', [$settings['show_tab']]);
?>
        <div class="ultimate-store-kit">
            <div <?php $this->print_render_attribute_string('usk-edd-standard-grid'); ?>>
            <?php
        }
        public function render_footer() { ?>
            </div>
        </div>
        <?php
        }
        public function render() {
            $this->render_header();
            $this->render_loop_item();
            $this->render_footer();
        }
        public function render_loop_item() {
            $settings = $this->get_settings_for_display();
            $id       = 'usk-edd-standard-grid-' . $this->get_id();
            $this->query_product();
            $wp_query = $this->get_query();
            if ($wp_query->have_posts()) {
                $this->add_render_attribute(
                    [
                        'edd-standard-grid-wrapper' => [
                            'class' => [
                                'usk-edd-standard-grid-wrapper'
                            ],
                            'id' => esc_attr($id),
                        ],
                    ]
                );

        ?>
            <div <?php $this->print_render_attribute_string('edd-standard-grid-wrapper'); ?>>
                <?php
                while ($wp_query->have_posts()) {
                    $wp_query->the_post();

                    $this->add_render_attribute('edd-standard-grid-item', 'class', 'usk-edd-standard-grid-item', true);
                ?>
                    <div <?php $this->print_render_attribute_string('edd-standard-grid-item'); ?>>
                        <div class="usk-edd-standard-grid-image">
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo esc_url(wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size'])); ?>" alt="<?php echo esc_html(get_the_title()); ?>">
                            </a>
                        </div>
                        <div class="usk-edd-content">
                            <div class="usk-inner-content">
                                <?php
                                if ($settings['show_category']) :
                                    $category_list = wp_get_post_terms(get_the_ID(), 'download_category');
                                    foreach ($category_list as $term) {
                                        $term_link = get_term_link($term);
                                        echo '<span class="usk-edd-category"><a href="' . esc_url($term_link) . '">' . esc_html($term->name) . '</a></span> ';
                                    }
                                endif;

                                if ($settings['show_title']) :
                                    printf('<%1$s class="usk-edd-title"><a href="%2$s">%3$s</a></%1$s>', esc_attr($settings['title_tags']), esc_url(get_the_permalink()), esc_html(get_the_title()));
                                endif;

                                if ($settings['show_price']) : ?>
                                    <div class="usk-edd-price">
                                        <?php if (edd_has_variable_prices(get_the_ID())) {
                                            esc_html_e('Starting at: ', 'ultimate-store-kit');
                                            edd_price(get_the_ID());
                                        } else {
                                            edd_price(get_the_ID());
                                        }
                                        ?>
                                    </div>
                                <?php
                                endif; ?>
                            </div>
                            <div class="usk-action-button">
                                <?php if (function_exists('edd_price')) { ?>
                                    <?php if (!edd_has_variable_prices(get_the_ID())) { ?>
                                        <?php echo wp_kses_post(edd_get_purchase_link(get_the_ID(), 'Add to Cart', 'button')); ?>
                                    <?php } ?>
                                <?php } ?>
                                <div class="usk-details-button">
                                    <a href="<?php the_permalink(); ?>"><span><?php esc_html_e('View Details', 'ultimate-store-kit'); ?></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
                if ($settings['show_pagination']) {
            ?>
                <div class="usk-pagination">
                    <?php ultimate_store_kit_post_pagination($wp_query); ?>
                </div>
<?php
                    wp_reset_postdata();
                }
            }
        }
        public function query_product() {
            $settings = $this->get_settings_for_display();
            $args = [];
            if ($settings['show_pagination']) {
                $args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
            }
            $default = $this->getGroupControlQueryArgs();
            $args['post_type'] = 'download';
            $args['posts_per_page'] = $settings['product_limit'];
            $default = $this->getGroupControlQueryArgs();
            $args = array_merge($default, $args);
            $this->_query =  new WP_Query($args);
        }
    }