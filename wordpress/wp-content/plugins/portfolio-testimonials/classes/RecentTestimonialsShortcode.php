<?php

namespace KN\PortfolioTestimonials;

use DateTime;
use WP_Query;

class RecentTestimonialsShortcode extends Singleton
{

    /**
     * Static property to hold our singleton instance
     *
     */
    protected static $instance;

    /**
     * Constructor - Registers the shortcode
     *
     * @return void
     */
    protected function __construct() {
        add_shortcode('recent-testimonials', [$this, 'displayRecentTestimonials']);
    }

    /**
     * Shortcode function to output recent testimonials.
     *
     * @param array $attributes Attributes passed with the shortcode
     * @return string HTML output for the shortcode
     */
    public function displayRecentTestimonials(array $attributes = []): string
    {

        $a = shortcode_atts([
            'posts_per_page' => 5, // Default number of posts
        ], $attributes, 'recent-testimonials');

	    // Get current post ID if we're on a single testimonial page
	    $exclude_id = (is_singular('testimonial')) ? get_the_ID() : 0;

	    $args = [
            'post_type'      => 'testimonial',
            'posts_per_page' => (int)$a['posts_per_page'],
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => [$exclude_id], // Exclude current testimonial page
	    ];

        $recentTestimonials = new WP_Query($args);

        if ($recentTestimonials->have_posts()) {
            $output = '<div class="recent-testimonials-con"><div class="recent-testimonials">';
            while ($recentTestimonials->have_posts()) {
                $recentTestimonials->the_post();
                $output .= '<div class="testimonial-item">';
	            if (has_post_thumbnail()) {
		            $output .= get_the_post_thumbnail(get_the_ID(), 'full');
	            }
                $output .= '<div class="testimonial-item-text"><h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';

	            $publisherName = get_post_meta(get_the_ID(), 'testimonialPublisherName', true);
	            if ($publisherName) {
		            $output .= '<div class="testimonial-publisher">' . esc_html($publisherName) . '</div>';
	            }
	            $publishDate = get_post_meta(get_the_ID(), 'testimonialPublishDate', true);
	            $date = new DateTime($publishDate);
	            $formattedPublishDate = $date->format('M j, Y');
	            if ($formattedPublishDate) {
		            $output .= '<div class="testimonial-date">' . __('Published', TEXT_DOMAIN) . ' ' . esc_html($formattedPublishDate) . '</div>';
	            }
	            $output .= '<div class="testimonial-excerpt">' . get_the_excerpt() . '</div>';
	            $testimonialCost = get_post_meta(get_the_ID(), 'testimonialTestimonialCost', true);
	            if ($testimonialCost) {
		            $output .= '<div class="testimonial-cost">$' . esc_html( number_format($testimonialCost, 2) ) . '</div>';
	            }
				$output .= '</div></div>';
            }
            $output .= '</div></div>';
            wp_reset_postdata();
        } else {
	        $plural = TestimonialSettings::getInstance()->testimonialTermPlural();
	        $output = '<p>' . __('No ' . $plural . ' found.', TEXT_DOMAIN) . '</p>';
        }
        return $output;
    }
}
