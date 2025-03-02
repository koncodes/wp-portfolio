<?php

namespace KN\PortfolioProjects;

class ProjectSettings extends Singleton
{
    /**
     * constants for the keys used to store settings in the database
     */
	const SHOW_LINKS = 'showLinks';
	const SHOW_TECHNOLOGIES = 'showTechnologies';
	const PROJECT_TERM_SINGULAR = 'projectTermSingular';
	const PROJECT_TERM_PLURAL = 'projectTermPlural';


	const SETTINGS_GROUP = 'project-settings';


	/**
     * @var ProjectSettings|null the singleton instance of this class
     */
    protected static $instance;

    /**
     * ProjectSettings constructor
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
	    register_setting(self::SETTINGS_GROUP, self::SHOW_LINKS);
	    register_setting(self::SETTINGS_GROUP, self::SHOW_TECHNOLOGIES);
	    register_setting(self::SETTINGS_GROUP, self::PROJECT_TERM_SINGULAR);
	    register_setting(self::SETTINGS_GROUP, self::PROJECT_TERM_PLURAL);

	    $this->addFields();
    }

    /**
     * adds menu pages
     *
     * @return void
     */
    public function addMenuPages() {

        add_submenu_page(
                'edit.php?post_type=project',
            'Project Plugin Settings',
            'Settings',
            'manage_options',
            'project-settings',
            [$this, 'settingsPage'],
//            position: 99

        );

    }

    /**
     * sdds settings fields to the settings page
     *
     * @return void
     */
    public function settingsPage(){
        ?>
        <h1><?= __('Project Settings', 'kn-projects') ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( self::SETTINGS_GROUP ); ?>
            <?php do_settings_sections( self::SETTINGS_GROUP ); ?>
            <?php submit_button('Save Changes'); ?>

        </form>
        <?php
    }

    public function addFields(){
        add_settings_section(
                'project-general',
            'General Project Settings',
            function() {},
            self::SETTINGS_GROUP // or 'general', 'writing', etc
        );
        ?>
        <style>
            .form-table tr {border-top: 1px solid #ccc}
            .form-table tr:last-of-type {border-bottom: 1px solid #ccc}
            .form-table th {border-right: 1px solid #ccc}
        </style>
        <?php

	    add_settings_field(
		    self::PROJECT_TERM_SINGULAR,
		    'Change Project Term Singular',
		    function() {
			    $projectTermSingular = esc_attr( get_option( self::PROJECT_TERM_SINGULAR) ); // Get the saved value from the database
			    ?>
                <label for="<?= self::PROJECT_TERM_SINGULAR ?>">
                    <input type="text" id="<?= self::PROJECT_TERM_SINGULAR ?>"
                           name="<?= self::PROJECT_TERM_SINGULAR ?>" value="<?= $projectTermSingular ?>">
                </label>
                <?php
		    },
		    self::SETTINGS_GROUP,
		    'project-general'
	    );
	    add_settings_field(
		    self::PROJECT_TERM_PLURAL,
		    'Change Project Term Plural',
		    function() {
			    $projectTermPlural = esc_attr( get_option( self::PROJECT_TERM_PLURAL ) ); // Get the saved value from the database
			    ?>
                <label for="<?= self::PROJECT_TERM_PLURAL ?>">
                    <input type="text" id="<?= self::PROJECT_TERM_PLURAL ?>"
                               name="<?= self::PROJECT_TERM_PLURAL ?>" value="<?= $projectTermPlural ?>">
                </label>
                <?php
		    },
		    self::SETTINGS_GROUP,
		    'project-general'
	    );
        add_settings_field(
            self::SHOW_TECHNOLOGIES,
            'Show Technologies',
            function() {
                $checked = get_option( self::SHOW_TECHNOLOGIES) ? 'checked' : '';
                ?>
                <label for="<?= self::SHOW_TECHNOLOGIES ?>">
                    <input type="checkbox" id="<?= self::SHOW_TECHNOLOGIES ?>"
                           name="<?= self::SHOW_TECHNOLOGIES ?>"
                        <?= $checked ?> value="1">
                </label>
                <?php
            },
            self::SETTINGS_GROUP,
            'project-general'
        );
        add_settings_field(
            self::SHOW_LINKS,
            'Show Links',
            function() {
                $checked = get_option( self::SHOW_LINKS) ? 'checked' : '';
                ?>
                <label for="<?= self::SHOW_LINKS ?>">
                    <input type="checkbox" id="<?= self::SHOW_LINKS ?>"
                           name="<?= self::SHOW_LINKS ?>"
                        <?= $checked ?> value="1">
                </label>
                <?php
            },
            self::SETTINGS_GROUP,
            'project-general'
        );
    }

	/**
	 * retrieves the value setting
	 *
	 * @return bool|null
	 */
	public function showLinks(): ?bool {
		return get_option( self::SHOW_LINKS);
	}

	/**
	 * retrieves the value setting
	 *
	 * @return bool|null
	 */
	public function showTechnologies(): ?bool {
		return get_option( self::SHOW_TECHNOLOGIES);
	}
	/**
	 * retrieves the value setting
	 *
	 * @return string
	 */
	public function projectTermSingular(): string {
		return get_option( self::PROJECT_TERM_SINGULAR);
	}
	/**
	 * retrieves the value setting
	 *
	 * @return string
	 */
	public function projectTermPlural(): string {
		return get_option( self::PROJECT_TERM_PLURAL);
	}
}