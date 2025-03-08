<?php

namespace KN\PortfolioTestimonials;

class Plugin extends Singleton
{

    /**
     * plugin constructor
     *
     * registers activation and deactivation hooks, and initializes components
     */
    public function __construct() {
        // Add activation and deactivation hooks
        register_activation_hook(PLUGIN_FILE, [$this, 'activatePlugin']); // Use PLUGIN_FILE constant

        // Initialize post types and shortcodes
        TestimonialPostType::getInstance();
        TestimonialTag::getInstance();
        TestimonialMeta::getInstance();
        RecentTestimonialsShortcode::getInstance();
        TestimonialSettings::getInstance();

	    add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);

    }

    /**
     * activates the plugin
     * called when the plugin is activated
     * manually registers the post types and flushes the permalink cache
     *
     * @return void
     */
    public function activatePlugin() {
        //manually register the post type
        TestimonialPostType::getInstance()->registerPostType();
        TestimonialTag::getInstance()->registerTag();
        RecentTestimonialsShortcode::getInstance()->displayRecentTestimonials();


        //flush the permalink cache
        flush_rewrite_rules();
    }

	/**
	 * enqueue plugin styles
	 * @return void
	 */
	public function enqueueStyles() {
		wp_enqueue_style(
			'kn-testimonials-style',
			plugin_dir_url(PLUGIN_FILE) . 'css/portfolio-testimonials.css',
			[],  // Dependencies
			'1.0.0' // Version number
		);
	}

}