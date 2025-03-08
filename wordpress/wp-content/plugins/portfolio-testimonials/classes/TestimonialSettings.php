<?php

namespace KN\PortfolioTestimonials;

class TestimonialSettings extends Singleton
{
    /**
     * constants for the keys used to store settings in the database
     */
	const SHOW_AUTHOR = 'showAuthor';
	const SHOW_AUTHOR_LINK = 'showAuthorLink';
	const TESTIMONIAL_TERM_SINGULAR = 'testimonialTermSingular';
	const TESTIMONIAL_TERM_PLURAL = 'testimonialTermPlural';


	const SETTINGS_GROUP = 'testimonial-settings';


	/**
     * @var TestimonialSettings|null the singleton instance of this class
     */
    protected static $instance;

    /**
     * TestimonialSettings constructor
     *
     * registers settings and admin menus
     */
    public function __construct()
    {
        add_action( 'admin_init', [$this, 'registerSettings'], 0 );
        add_action( 'admin_menu', [$this, 'addMenuPages']);
    }

    /**
     * registers the settings
     *
     * @return void
     */
    function registerSettings() {
	    register_setting(self::SETTINGS_GROUP, self::SHOW_AUTHOR);
	    register_setting(self::SETTINGS_GROUP, self::SHOW_AUTHOR_LINK);
	    register_setting(self::SETTINGS_GROUP, self::TESTIMONIAL_TERM_SINGULAR);
	    register_setting(self::SETTINGS_GROUP, self::TESTIMONIAL_TERM_PLURAL);

	    $this->addFields();
    }

    /**
     * adds menu pages
     *
     * @return void
     */
    public function addMenuPages() {

        add_submenu_page(
                'edit.php?post_type=testimonial',
            'Testimonial Plugin Settings',
            'Settings',
            'manage_options',
            'testimonial-settings',
            [$this, 'settingsPage'],

        );

    }

    /**
     * sdds settings fields to the settings page
     *
     * @return void
     */
    public function settingsPage(){
        ?>
        <h1><?= __('Testimonial Settings', 'kn-testimonials') ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( self::SETTINGS_GROUP ); ?>
            <?php do_settings_sections( self::SETTINGS_GROUP ); ?>
            <?php submit_button('Save Changes'); ?>

        </form>
        <?php
    }

    public function addFields() {
        add_settings_section(
            'testimonial-general',
            'General Testimonial Settings',
            function() {},
            self::SETTINGS_GROUP
        );


        add_settings_field(
            self::TESTIMONIAL_TERM_SINGULAR,
            'Change Testimonial Term Singular',
            function() {
                $testimonialTermSingular = esc_attr(get_option(self::TESTIMONIAL_TERM_SINGULAR));
                ?>
                <style>
                    .form-table tr { border-top: 1px solid #ccc; }
                    .form-table tr:last-of-type { border-bottom: 1px solid #ccc; }
                    .form-table th { border-right: 1px solid #ccc; }
                </style>
                <label for="<?= self::TESTIMONIAL_TERM_SINGULAR ?>">
                    <input type="text" id="<?= self::TESTIMONIAL_TERM_SINGULAR ?>"
                           name="<?= self::TESTIMONIAL_TERM_SINGULAR ?>" value="<?= $testimonialTermSingular ?>">
                </label>
                <?php
            },
            self::SETTINGS_GROUP,
            'testimonial-general'
        );

        add_settings_field(
            self::TESTIMONIAL_TERM_PLURAL,
            'Change Testimonial Term Plural',
            function() {
                $testimonialTermPlural = esc_attr(get_option(self::TESTIMONIAL_TERM_PLURAL));
                ?>
                <label for="<?= self::TESTIMONIAL_TERM_PLURAL ?>">
                    <input type="text" id="<?= self::TESTIMONIAL_TERM_PLURAL ?>"
                           name="<?= self::TESTIMONIAL_TERM_PLURAL ?>" value="<?= $testimonialTermPlural ?>">
                </label>
                <?php
            },
            self::SETTINGS_GROUP,
            'testimonial-general'
        );
        add_settings_field(
            self::SHOW_AUTHOR,
            'Show Author Name',
            function() {
                $checked = get_option(self::SHOW_AUTHOR) ? 'checked' : '';
                ?>
                <label for="<?= self::SHOW_AUTHOR ?>">
                    <input type="checkbox" id="<?= self::SHOW_AUTHOR ?>"
                           name="<?= self::SHOW_AUTHOR ?>" <?= $checked ?> value="1">
                </label>
                <?php
            },
            self::SETTINGS_GROUP,
            'testimonial-general'
        );
        add_settings_field(
            self::SHOW_AUTHOR_LINK,
            'Show Author Link',
            function() {
                $checked = get_option(self::SHOW_AUTHOR_LINK) ? 'checked' : '';
                ?>
                <label for="<?= self::SHOW_AUTHOR_LINK ?>">
                    <input type="checkbox" id="<?= self::SHOW_AUTHOR_LINK ?>"
                           name="<?= self::SHOW_AUTHOR_LINK ?>" <?= $checked ?> value="1">
                </label>
                <?php
            },
            self::SETTINGS_GROUP,
            'testimonial-general'
        );
    }

	/**
	 * retrieves the value setting
	 *
	 * @return bool|null
	 */
	public function showAuthor(): ?bool {
		return get_option( self::SHOW_AUTHOR);
	}

	/**
	 * retrieves the value setting
	 *
	 * @return bool|null
	 */
	public function showAuthorLink(): ?bool {
		return get_option( self::SHOW_AUTHOR_LINK);
	}
	/**
	 * retrieves the value setting
	 *
	 * @return string
	 */
	public function testimonialTermSingular(): string {
		return get_option( self::TESTIMONIAL_TERM_SINGULAR);
	}
	/**
	 * retrieves the value setting
	 *
	 * @return string
	 */
	public function testimonialTermPlural(): string {
		return get_option( self::TESTIMONIAL_TERM_PLURAL);
	}
}