<?php

namespace KN\PortfolioTestimonials;

use DateTime;
use WP_Query;

class TestimonialPostType extends Singleton
{
    const POST_TYPE = 'testimonial';

    /**
     * static property to hold our singleton instance
     */
    protected static $instance;

    /**
     * constructor to registers hooks for testimonial post type
     * @return void
     */
    public function __construct()
    {
        add_action( 'init', [$this, 'registerPostType'], 0 );
        add_filter('the_content', [$this, 'testimonialContent']);
//        add_filter('the_excerpt', [$this, 'testimonialExcerpt']);
    }

    /**
     * register custom post type of testimonial
     * @return void
     */
    public function registerPostType() {

	    $singular = TestimonialSettings::getInstance()->testimonialTermSingular();
	    $plural = TestimonialSettings::getInstance()->testimonialTermPlural();


        $labels = array(
            'name'                  => _x( $plural, 'Post Type General Name', TEXT_DOMAIN ),
            'singular_name'         => _x(  $singular, 'Post Type Singular Name', TEXT_DOMAIN ),
            'menu_name'             => __( 'Testimonials', TEXT_DOMAIN ),
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
            'taxonomies'            => array( TestimonialTag::TAXONOMY ),
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
            'menu_icon'             => 'dashicons-format-quote',
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
     * edit testimonial content to include meta
     * @param string $content
     * @return string
     */
    public function testimonialContent($content) {
        // ensure this only runs for testimonials
        if (get_post_type() == self::POST_TYPE) {
            $meta = TestimonialMeta::getInstance();

            $gitLink = $meta->getAuthorName();
            $liveLink = $meta->getAuthorLink();

            // add testimonial metadata to variable
            $testimonialMetaContent = '<div class="testimonial-meta">
                <h3>' . __('Links', TEXT_DOMAIN) . '</h3>' .
                               ($gitLink ? '<div>
                	<span class="testimonial-meta-label">' . __('GitHub Link', TEXT_DOMAIN) . '</span>
                	<span class="testimonial-meta-text">' . $gitLink . '</span>
                </div>' : '') .
                               ($liveLink ? '<div>
                	<span class="testimonial-meta-label">' . __('Live Link', TEXT_DOMAIN) . '</span>
                	<span class="testimonial-meta-text">' . $liveLink . '</span>
                </div>' : '') . '
                
            </div>';

	        // get tags associated with the testimonial
	        $testimonialMetaTags = '';
	        $tags = wp_get_post_terms(get_the_ID(), 'testimonial_tag');

	        if (!empty($tags)) {
		        $testimonialMetaTags .= '<div class="testimonial-tags"><span class="testimonial-tags-title">' . __('Tags', TEXT_DOMAIN) . '</span>';

		        foreach ($tags as $tag) {
					$testimonialMetaTags .= '<span><a href="' . esc_url( get_term_link($tag) ) . '">' . esc_html($tag->name) . '</a></span>';
		        }

		        $testimonialMetaTags .= '</div>';
	        }

	        $content = $content . $testimonialMetaTags . $testimonialMetaContent;

            wp_reset_postdata();
        }
        return $content;
    }
}