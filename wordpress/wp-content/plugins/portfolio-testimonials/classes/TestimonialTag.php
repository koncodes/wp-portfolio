<?php

namespace KN\PortfolioTestimonials;

class TestimonialTag extends Singleton
{
    /**
     * taxonomy slug for testimonial tags
     */
    const TAXONOMY = 'testimonial_tag';
    //redefine instance variable so that instance is separate from the singleton

    /**
     * static property to hold our singleton instance
     * @var TestimonialTag
     */
    protected static $instance;

    /**
     * TestimonialTag constructor
     * registers the testimonial tag taxonomy during the 'init' action
     *
     * @return void
     */
    public function __construct()
    {
        add_action( 'init', [$this, 'registerTag'], 0 );
    }

    /**
     * registers tag custom taxonomy
     *
     * @return void
     */
    function registerTag() {

        $labels = array(
            'name'                       => _x( 'Tags', 'Taxonomy General Name', TEXT_DOMAIN ),
            'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', TEXT_DOMAIN ),
            'menu_name'                  => __( 'Tag', TEXT_DOMAIN ),
            'all_items'                  => __( 'All Tags', TEXT_DOMAIN ),
            'parent_item'                => __( 'Parent Tag', TEXT_DOMAIN ),
            'parent_item_colon'          => __( 'Parent Tag:', TEXT_DOMAIN ),
            'new_item_name'              => __( 'New Tag Name', TEXT_DOMAIN ),
            'add_new_item'               => __( 'Add New Tag', TEXT_DOMAIN ),
            'edit_item'                  => __( 'Edit Tag', TEXT_DOMAIN ),
            'update_item'                => __( 'Update Tag', TEXT_DOMAIN ),
            'view_item'                  => __( 'View Tag', TEXT_DOMAIN ),
            'separate_items_with_commas' => __( 'Separate tags with commas', TEXT_DOMAIN ),
            'add_or_remove_items'        => __( 'Add or remove tags', TEXT_DOMAIN ),
            'choose_from_most_used'      => __( 'Choose from the most used', TEXT_DOMAIN ),
            'popular_items'              => __( 'Popular Tags', TEXT_DOMAIN ),
            'search_items'               => __( 'Search Tags', TEXT_DOMAIN ),
            'not_found'                  => __( 'Not Found', TEXT_DOMAIN ),
            'no_terms'                   => __( 'No tags', TEXT_DOMAIN ),
            'items_list'                 => __( 'Tags list', TEXT_DOMAIN ),
            'items_list_navigation'      => __( 'Items tag navigation', TEXT_DOMAIN ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        );
        register_taxonomy( self::TAXONOMY, array( TestimonialPostType::POST_TYPE ), $args );

    }
}