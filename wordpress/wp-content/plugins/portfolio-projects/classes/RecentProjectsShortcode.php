<?php

namespace KN\PortfolioProjects;

use DateTime;
use WP_Query;

class RecentProjectsShortcode extends Singleton
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
        add_shortcode('recent-projects', [$this, 'displayRecentProjects']);
    }

    /**
     * Shortcode function to output recent projects.
     *
     * @param array $attributes Attributes passed with the shortcode
     * @return string HTML output for the shortcode
     */
    public function displayRecentProjects(array $attributes = []): string
    {

        $a = shortcode_atts([
            'posts_per_page' => 5, // Default number of posts
        ], $attributes, 'recent-projects');

	    // Get current post ID if we're on a single project page
	    $exclude_id = (is_singular('project')) ? get_the_ID() : 0;

	    $args = [
            'post_type'      => 'project',
            'posts_per_page' => (int)$a['posts_per_page'],
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => [$exclude_id], // Exclude current project page
	    ];

        $recentProjects = new WP_Query($args);

        if ($recentProjects->have_posts()) {
            $output = '<div class="recent-projects-con"><div class="recent-projects">';
            while ($recentProjects->have_posts()) {
                $recentProjects->the_post();
                $output .= '<div class="project-item">';
	            if (has_post_thumbnail()) {
		            $output .= get_the_post_thumbnail(get_the_ID(), 'full');
	            }
                $output .= '<div class="project-item-text"><h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';

	            $publisherName = get_post_meta(get_the_ID(), 'projectPublisherName', true);
	            if ($publisherName) {
		            $output .= '<div class="project-publisher">' . esc_html($publisherName) . '</div>';
	            }
	            $publishDate = get_post_meta(get_the_ID(), 'projectPublishDate', true);
	            $date = new DateTime($publishDate);
	            $formattedPublishDate = $date->format('M j, Y');
	            if ($formattedPublishDate) {
		            $output .= '<div class="project-date">' . __('Published', TEXT_DOMAIN) . ' ' . esc_html($formattedPublishDate) . '</div>';
	            }
	            $output .= '<div class="project-excerpt">' . get_the_excerpt() . '</div>';
	            $projectCost = get_post_meta(get_the_ID(), 'projectProjectCost', true);
	            if ($projectCost) {
		            $output .= '<div class="project-cost">$' . esc_html( number_format($projectCost, 2) ) . '</div>';
	            }
				$output .= '</div></div>';
            }
            $output .= '</div></div>';
            wp_reset_postdata();
        } else {
	        $plural = ProjectSettings::getInstance()->projectTermPlural();
	        $output = '<p>' . __('No ' . $plural . ' found.', TEXT_DOMAIN) . '</p>';
        }
        return $output;
    }
}
