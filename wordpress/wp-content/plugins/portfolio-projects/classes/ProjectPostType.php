<?php

namespace KN\PortfolioProjects;

use DateTime;
use WP_Query;

class ProjectPostType extends Singleton
{
    const POST_TYPE = 'project';

    /**
     * static property to hold our singleton instance
     */
    protected static $instance;

    /**
     * constructor to registers hooks for project post type
     * @return void
     */
    public function __construct()
    {
        add_action( 'init', [$this, 'registerPostType'], 0 );
        add_filter('the_content', [$this, 'projectContent']);
//        add_filter('the_excerpt', [$this, 'projectExcerpt']);
    }

    /**
     * register custom post type of project
     * @return void
     */
    public function registerPostType() {

	    $singular = ProjectSettings::getInstance()->projectTermSingular();
	    $plural = ProjectSettings::getInstance()->projectTermPlural();


        $labels = array(
            'name'                  => _x( $plural, 'Post Type General Name', TEXT_DOMAIN ),
            'singular_name'         => _x(  $singular, 'Post Type Singular Name', TEXT_DOMAIN ),
            'menu_name'             => __( 'Projects', TEXT_DOMAIN ),
            'name_admin_bar'        => __(  $singular, TEXT_DOMAIN ),
            'archives'              => __(  $plural, TEXT_DOMAIN ),
            'attributes'            => __( $singular . ' Attributes', TEXT_DOMAIN ),
            'parent_item_colon'     => __( 'Parent ' . $singular . ':', TEXT_DOMAIN ),
            'all_items'             => __( 'All ' . $plural, TEXT_DOMAIN ),
            'add_new_item'          => __( 'Add New ' . $singular, TEXT_DOMAIN ),
            'add_new'               => __( 'Add New ' . $singular, TEXT_DOMAIN ),
            'new_item'              => __( 'New ' . $singular, TEXT_DOMAIN ),
            'edit_item'             => __( 'Edit ' . $singular, TEXT_DOMAIN ),
            'update_item'           => __( 'Update ' . $singular, TEXT_DOMAIN ),
            'view_item'             => __( 'View ' . $singular, TEXT_DOMAIN ),
            'view_items'            => __( 'View ' . $plural, TEXT_DOMAIN ),
            'search_items'          => __( 'Search ' . $plural, TEXT_DOMAIN ),
            'not_found'             => __( 'Not found', TEXT_DOMAIN ),
            'not_found_in_trash'    => __( 'Not found in Trash', TEXT_DOMAIN ),
            'featured_image'        => __( 'Featured Image', TEXT_DOMAIN ),
            'set_featured_image'    => __( 'Set featured image', TEXT_DOMAIN ),
            'remove_featured_image' => __( 'Remove featured image', TEXT_DOMAIN ),
            'use_featured_image'    => __( 'Use as featured image', TEXT_DOMAIN ),
            'insert_into_item'      => __( 'Insert into ' . strtolower($singular), TEXT_DOMAIN ),
            'uploaded_to_this_item' => __( 'Uploaded to this ' . strtolower($singular), TEXT_DOMAIN ),
            'items_list'            => __( $plural . ' list', TEXT_DOMAIN ),
            'items_list_navigation' => __( $plural . ' list navigation', TEXT_DOMAIN ),
            'filter_items_list'     => __( 'Filter ' . strtolower($plural) . ' list', TEXT_DOMAIN ),
        );
        $args = array(
            'label'                 => __( $singular, TEXT_DOMAIN ),
            'description'           => __( $plural, TEXT_DOMAIN ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
            'taxonomies'            => array( ProjectLanguage::TAXONOMY ),
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
            'menu_icon'             => 'dashicons-portfolio',
            'rewrite'               => array(
	            'slug'       => strtolower($singular),
	            'with_front' => false,
            ),
            'has_archive'           => strtolower($singular),

        );
        register_post_type( self::POST_TYPE, $args );

    }

    // Function to sanitize class names
    public static function sanitize_class_name($name): string {
        // Convert to lowercase
        $name = strtolower($name);
        // Replace any character that is not a-z, 0-9, or a hyphen with a hyphen
        $name = preg_replace('/[^a-z0-9-]/', '', $name);
        // Remove multiple consecutive hyphens
        $name = preg_replace('/-+/', '-', $name);
        // Trim hyphens from the beginning and end
        return trim($name, '-');
    }

    /**
     * edit project content to include meta
     * @param string $content
     * @return string
     */
    public function projectContent($content) {
        // ensure this only runs for projects
        if (get_post_type() == self::POST_TYPE) {
            $meta = ProjectMeta::getInstance();

            $technologies = $meta->getTechnologies();
            $gitLink = $meta->getGitLink();
            $liveLink = $meta->getLiveLink();

            $galleryImages = $meta->getGalleryImages();

            // add project metadata to variable
            $projectMetaContent = '<div class="project-meta">
                <h3>' . __('Links', TEXT_DOMAIN) . '</h3>' .
                               ($gitLink ? '<div>
                	<span class="project-meta-label">' . __('GitHub Link', TEXT_DOMAIN) . '</span>
                	<span class="project-meta-text">' . $gitLink . '</span>
                </div>' : '') .
                               ($liveLink ? '<div>
                	<span class="project-meta-label">' . __('Live Link', TEXT_DOMAIN) . '</span>
                	<span class="project-meta-text">' . $liveLink . '</span>
                </div>' : '') . '
                
            </div>';



            // Add technologies to the content
            if (!empty($technologies)) {
                $techContent = '
                    <div class="project-meta">
                        <h3>' . __('Technologies', TEXT_DOMAIN) . '</h3><div>';

                        foreach ($technologies as $tech) {
                            $techClass = self::sanitize_class_name($tech);
                            $techContent .= '
                            <span class="project-meta-text ' . esc_attr($techClass) . '">' . esc_html($tech) . '</span>
                            ';
                        }

                $techContent .= '</div></div>';
                $projectMetaContent .= $techContent;
            }

	        // get languages associated with the project
	        $projectMetaLanguages = '';
	        $languages = wp_get_post_terms(get_the_ID(), 'project_language');

	        if (!empty($languages)) {
		        $projectMetaLanguages .= '<div class="project-languages"><span class="project-languages-title">' . __('Languages', TEXT_DOMAIN) . '</span>';

		        foreach ($languages as $language) {
					$projectMetaLanguages .= '<span><a href="' . esc_url( get_term_link($language) ) . '">' . esc_html($language->name) . '</a></span>';
		        }

		        $projectMetaLanguages .= '</div>';
	        }

	        $content = $galleryImages . $content . $projectMetaLanguages . $projectMetaContent;

            wp_reset_postdata();
        }
        return $content;
    }
}