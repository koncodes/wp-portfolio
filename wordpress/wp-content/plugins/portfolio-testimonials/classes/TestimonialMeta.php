<?php

namespace KN\PortfolioTestimonials;

class TestimonialMeta extends Singleton
{
    /**
     * keys stored in database
     * use constants to avoid type and debugging issues
     */
    const AUTHOR_NAME = 'authorName';
    const AUTHOR_LINK = 'authorLink';


    /**
     * static property to hold the singleton instance
     *
     * @var TestimonialMeta
     */
    protected static $instance;

    /**
     *  constructor for TestimonialMeta
     * hooks the registration of meta boxes and the saving of testimonial information through actions
     */
    public function __construct()
    {
        add_action( 'admin_init', [$this, 'registerMetaBox'], 0 );
        add_action('save_post_' . TestimonialPostType::POST_TYPE, [$this, 'saveInformation'], 10, 1);
	    add_action('init', [$this, 'setDefaults'], 0);

    }

    /**
     * registers the meta box for testimonial details
     * @return void
     */
    function registerMetaBox() {
        add_meta_box('testimonial_information',
            __('Project Testimonials', TEXT_DOMAIN),
            [$this, 'detailsForm'],
            TestimonialPostType::POST_TYPE,
            'normal', 'core');
    }


    /**
     * displays the form fields in the custom meta box
     * @return void
     */
    function detailsForm() {

        $authorName = $this->getAuthorName();
        $authorLink = $this->getAuthorLink();

        ?>
        <style>
            :is(#testimonial_information) .inside {
                padding: 0;
                margin: 0;
            }
            .testimonial-meta-box {
                border-top: 1px solid #dfdfdf;
                border-bottom: 1px solid #dfdfdf;
            }
            .testimonial-meta-box > :is(label, div) {
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #dfdfdf;
            }

            .testimonial-meta-box .testimonial-meta-box-label {
                background: #F9F9F9;
                padding: 20px 10px 20px 20px;
                flex: 0 0 20%;
                min-width: 150px;
                font-weight: 700;
                border-right: 1px solid #dfdfdf;
            }
            .testimonial-meta-box > * > span {
                flex: 1;
                padding: 15px;
            }
            .testimonial-meta-box label :is(input:not[type=checkbox], select) {
                flex: 2;
                padding: 5px;
                border: 1px solid #ccc;
                border-radius: 4px;
                width: 100%;
            }

            .components-panel__body.is-opened {
                height: fit-content;
            }

            .testimonial-meta-box:last-of-type > label:last-of-type,
            .testimonial-meta-box:last-of-type > div:last-of-type {
                border-bottom: 0;
            }

        </style>
        <div class="testimonial-meta-box">
            <label><span class="testimonial-meta-box-label"><?= __('Author Name', TEXT_DOMAIN) ?></span> <span><input type="text" name="<?= self::AUTHOR_NAME ?>" value="<?= $authorName ?>"></span></label>
            <label><span class="testimonial-meta-box-label"><?= __('Author Link', TEXT_DOMAIN) ?></span> <span><input type="text" name="<?= self::AUTHOR_LINK ?>" value="<?= $authorLink ?>"></span></label>
        </div>
        <?php
    }

    /**
     * @return void
     */
    function saveInformation($post_id) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check if the user has permissions to save data
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check if it's the correct post type
        if (get_post_type($post_id) !== TestimonialPostType::POST_TYPE) {
            return;
        }

        // Get values from $_POST
        $authorName = isset($_POST[self::AUTHOR_NAME]) ? sanitize_text_field($_POST[self::AUTHOR_NAME]) : '';
        $authorLink = isset($_POST[self::AUTHOR_LINK]) ? sanitize_text_field($_POST[self::AUTHOR_LINK]) : '';

        // Update post meta
        update_post_meta($post_id, self::AUTHOR_NAME, $authorName);
        update_post_meta($post_id, self::AUTHOR_LINK, $authorLink);

    }

    /**
     * gets git link
     * @return string testimonial price
     */
    public function getAuthorName() {
        $post = get_post();
        return get_post_meta($post->ID, self::AUTHOR_NAME, true);
    }

    /**
     * gets live link
     * @return string testimonial price
     */
    public function getAuthorLink() {
        $post = get_post();
        return get_post_meta($post->ID, self::AUTHOR_LINK, true);
    }



    /**
     * set default settings
     * @return void
     */
    public function setDefaults() {
        add_option( TestimonialSettings::SHOW_AUTHOR, 1);
        add_option( TestimonialSettings::SHOW_AUTHOR_LINK, 1);
	    add_option( TestimonialSettings::TESTIMONIAL_TERM_SINGULAR, 'Testimonial');
	    add_option( TestimonialSettings::TESTIMONIAL_TERM_PLURAL, 'Testimonials');
    }
}