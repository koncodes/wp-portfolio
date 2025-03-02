<?php

namespace KN\PortfolioProjects;

class ProjectLanguage extends Singleton
{
    /**
     * taxonomy slug for project languages
     */
    const TAXONOMY = 'project_language';
    //redefine instance variable so that instance is separate from the singleton

    /**
     * static property to hold our singleton instance
     * @var ProjectLanguage
     */
    protected static $instance;

    /**
     * ProjectLanguage constructor
     * registers the project language taxonomy during the 'init' action
     *
     * @return void
     */
    public function __construct()
    {
        add_action( 'init', [$this, 'registerLanguage'], 0 );
    }

    /**
     * registers language custom taxonomy
     *
     * @return void
     */
    function registerLanguage() {

        $labels = array(
            'name'                       => _x( 'Languages', 'Taxonomy General Name', TEXT_DOMAIN ),
            'singular_name'              => _x( 'Language', 'Taxonomy Singular Name', TEXT_DOMAIN ),
            'menu_name'                  => __( 'Language', TEXT_DOMAIN ),
            'all_items'                  => __( 'All Languages', TEXT_DOMAIN ),
            'parent_item'                => __( 'Parent Language', TEXT_DOMAIN ),
            'parent_item_colon'          => __( 'Parent Language:', TEXT_DOMAIN ),
            'new_item_name'              => __( 'New Language Name', TEXT_DOMAIN ),
            'add_new_item'               => __( 'Add New Language', TEXT_DOMAIN ),
            'edit_item'                  => __( 'Edit Language', TEXT_DOMAIN ),
            'update_item'                => __( 'Update Language', TEXT_DOMAIN ),
            'view_item'                  => __( 'View Language', TEXT_DOMAIN ),
            'separate_items_with_commas' => __( 'Separate languages with commas', TEXT_DOMAIN ),
            'add_or_remove_items'        => __( 'Add or remove languages', TEXT_DOMAIN ),
            'choose_from_most_used'      => __( 'Choose from the most used', TEXT_DOMAIN ),
            'popular_items'              => __( 'Popular Languages', TEXT_DOMAIN ),
            'search_items'               => __( 'Search Languages', TEXT_DOMAIN ),
            'not_found'                  => __( 'Not Found', TEXT_DOMAIN ),
            'no_terms'                   => __( 'No languages', TEXT_DOMAIN ),
            'items_list'                 => __( 'Languages list', TEXT_DOMAIN ),
            'items_list_navigation'      => __( 'Items language navigation', TEXT_DOMAIN ),
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
        register_taxonomy( self::TAXONOMY, array( ProjectPostType::POST_TYPE ), $args );

    }
}