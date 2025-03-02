<?php

namespace KN\PortfolioProjects;

class ProjectMeta extends Singleton
{
    /**
     * keys stored in database
     * use constants to avoid type and debugging issues
     */
    const TECHNOLOGIES = 'projectTechnologies';
    const GIT_LINK = 'gitLink';
    const LIVE_LINK = 'liveLink';
    const GALLERY = 'projectGallery';



    /**
     * static property to hold the singleton instance
     *
     * @var ProjectMeta
     */
    protected static $instance;

    /**
     *  constructor for ProjectMeta
     * hooks the registration of meta boxes and the saving of project information through actions
     */
    public function __construct()
    {
        add_action( 'admin_init', [$this, 'registerMetaBox'], 0 );
        add_action('save_post_' . ProjectPostType::POST_TYPE, [$this, 'saveInformation'], 10, 1);
	    add_action('init', [$this, 'setDefaults'], 0);

    }

    /**
     * registers the meta box for project details
     * @return void
     */
    function registerMetaBox() {
        add_meta_box('project_information',
            __('Project Details', TEXT_DOMAIN),
            [$this, 'detailsForm'],
            ProjectPostType::POST_TYPE,
            'normal', 'core');
        add_meta_box('project_gallery',
            __('Project Gallery', TEXT_DOMAIN),
            [$this, 'galleryForm'],
            ProjectPostType::POST_TYPE,
            'normal', 'core'
        );
    }

    function galleryForm() {
        global $post;
        $gallery = get_post_meta($post->ID, self::GALLERY, true);
        ?>
        <div id="project_gallery_container">
            <ul class="project-gallery-list">
                <?php
                if (!empty($gallery)) {
                    foreach ($gallery as $image_id) {
                        echo '<li style="display:inline-block;margin:5px;">';
                        echo wp_get_attachment_image($image_id, 'thumbnail');
                        echo '<input type="hidden" name="'.self::GALLERY.'[]" value="'.$image_id.'">';
                        echo '<button type="button" class="remove-image">Remove</button>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
            <input type="button" class="button project-gallery-upload" value="<?php _e('Add Images', TEXT_DOMAIN); ?>">
        </div>

        <script>
            jQuery(document).ready(function($) {
                var file_frame;
                $('.project-gallery-upload').click(function(e) {
                    e.preventDefault();
                    if (file_frame) {
                        file_frame.open();
                        return;
                    }
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: '<?php _e("Select Images", TEXT_DOMAIN); ?>',
                        button: { text: '<?php _e("Add to Gallery", TEXT_DOMAIN); ?>' },
                        multiple: true
                    });
                    file_frame.on('select', function() {
                        var attachments = file_frame.state().get('selection').map(function(attachment) {
                            attachment = attachment.toJSON();
                            $('.project-gallery-list').append(
                                '<li style="display:inline-block;margin:5px;">' +
                                '<img src="' + attachment.sizes.thumbnail.url + '" width="100">' +
                                '<input type="hidden" name="<?= self::GALLERY ?>[]" value="' + attachment.id + '">' +
                                '<button type="button" class="remove-image">Remove</button>' +
                                '</li>'
                            );
                        });
                    });
                    file_frame.open();
                });

                $(document).on('click', '.remove-image', function() {
                    $(this).parent().remove();
                });
            });
        </script>
        <?php
    }


    /**
     * displays the form fields in the custom meta box
     * @return void
     */
    function detailsForm() {

        $technologies = $this->getTechnologies();
        $technologiesList = [
            'Frameworks' => ['Vue.js', 'React.js', 'Angular', 'jQuery', 'Bootstrap', 'Next.js', 'Tailwind CSS', 'Pinia'],
            'Languages' => ['JavaScript', 'PHP', 'TypeScript', 'Python', 'Lua', 'HTML5', 'CSS', 'SCSS', 'SQL'],
            'Tools' => ['Git', 'GitHub', 'Visual Studio Code', 'PhpStorm', 'phpMyAdmin', 'Docker', 'WP-CLI', 'Webpack', 'Prettier', 'Gulp', 'Node.js'],
            'APIs' => ['The Movie Database API', 'Firebase'],
            'Design Tools' => ['Figma', 'Adobe Photoshop', 'Adobe Illustrator'],
        ];
        $gitLink = $this->getGitLink();
        $liveLink = $this->getLiveLink();

        ?>
        <style>
            :is(#project_publishing_information, #project_information) .inside {
                padding: 0;
                margin: 0;
            }
            .project-meta-box h4 {
                margin: 0;
                padding: 20px;
                border-bottom: 1px solid #dfdfdf;
            }
            .project-meta-box > :is(label, div) {
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #dfdfdf;
            }
            .project-meta-box:last-of-type > label:last-of-type, .project-meta-box:last-of-type > div:last-of-type {
                border-bottom: 0;
            }
            .project-meta-box .project-meta-box-label {
                background: #F9F9F9;
                padding: 20px 10px 20px 20px;
                flex: .35;
                font-weight: 700;
                border-right: 1px solid #dfdfdf;
            }
            .project-meta-box span {
                flex: 1;
                padding: 15px;
            }
            .project-meta-box label :is(input:not[type=checkbox], select) {
                flex: 2;
                padding: 5px;
                border: 1px solid #ccc;
                border-radius: 4px;
                width: 100%;
            }
            .project-technologies {
                display: inline-block;
                margin: 0 5px 5px 0;
            }
            .project-technologies label {
                display: flex;
                align-items: flex-end;
                padding: 10px 13px;
                gap: 5px;
                border: 1px solid #ccc;
                border-radius: 100px;
                width: fit-content;


            }
            .project-technologies:last-of-type {
                margin: 0 ;
            }
            .components-panel__body.is-opened {
                height: fit-content;
            }
            .project-meta-box .project-technologies-con {
                padding: 0;
            }
            .project-technologies-category {
                display: flex;
                align-items: flex-start;
                border-bottom: 1px solid #ccc;;
                padding: 15px;
            }
            .project-technologies-head {
                flex: 0 0 110px;
            }
        </style>
        <div class="project-meta-box">
            <h4><?= __('Add Project Links', TEXT_DOMAIN) ?></h4>
            <label><span class="project-meta-box-label"><?= __('GitHub Link', TEXT_DOMAIN) ?></span> <span><input type="text" name="<?= self::GIT_LINK ?>" value="<?= $gitLink ?>"></span></label>
            <label><span class="project-meta-box-label"><?= __('Live Link', TEXT_DOMAIN) ?></span> <span><input type="text" name="<?= self::LIVE_LINK ?>" value="<?= $liveLink ?>"></span></label>
        </div>
        <div class="project-meta-box">
            <h4><?= __('Select One Or More Technologies', TEXT_DOMAIN) ?></h4>
            <div>
                <span class="project-meta-box-label"><?= __('Technologies', TEXT_DOMAIN) ?></span>
                <span class="project-technologies-con">
                <?php foreach ($technologiesList as $category => $items) : ?>
                    <div class="project-technologies-category">
                        <div class="project-technologies-head"><?= esc_html($category) ?></div>
                        <div class="project-technologies-body">
                            <?php foreach ($items as $tech) : ?>
                                <div class="project-technologies">
                                    <label for="tech-<?= esc_attr(sanitize_title($tech)) ?>">
                                        <input type="checkbox"
                                               name="<?= self::TECHNOLOGIES ?>[]"
                                               id="tech-<?= esc_attr(sanitize_title($tech)) ?>"
                                               value="<?= esc_attr($tech) ?>"
                                           <?= checked(in_array($tech, $technologies), true, false) ?>>
                                        <?= esc_html($tech) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </span>
            </div>
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
        if (get_post_type($post_id) !== ProjectPostType::POST_TYPE) {
            return;
        }

        // Get values from $_POST
        $technologies = isset($_POST[self::TECHNOLOGIES]) ? array_map('sanitize_text_field', $_POST[self::TECHNOLOGIES]) : [];
        $gitLink = isset($_POST[self::GIT_LINK]) ? sanitize_text_field($_POST[self::GIT_LINK]) : '';
        $liveLink = isset($_POST[self::LIVE_LINK]) ? sanitize_text_field($_POST[self::LIVE_LINK]) : '';
        $gallery = isset($_POST[self::GALLERY]) ? array_map('intval', $_POST[self::GALLERY]) : [];

        // Update post meta
        update_post_meta($post_id, self::TECHNOLOGIES, $technologies);
        update_post_meta($post_id, self::GIT_LINK, $gitLink);
        update_post_meta($post_id, self::LIVE_LINK, $liveLink);
        update_post_meta($post_id, self::GALLERY, $gallery);

    }

    /**
     * gets technologies
     * @return array project price
     */
    public function getTechnologies() {
        $post = get_post();
        $technologies = get_post_meta($post->ID, self::TECHNOLOGIES, true);
        return is_array($technologies) ? $technologies : [];
    }

    /**
     * gets git link
     * @return string project price
     */
    public function getGitLink() {
        $post = get_post();
        return get_post_meta($post->ID, self::GIT_LINK, true);
    }

    /**
     * gets live link
     * @return string project price
     */
    public function getLiveLink() {
        $post = get_post();
        return get_post_meta($post->ID, self::LIVE_LINK, true);
    }

    /**
     * gets live link
     * @return array project price
     */
    function getGalleryImages() {
        $post = get_post();
        $gallery = get_post_meta($post->ID, self::GALLERY, true);
        if (!empty($gallery)) {
            echo '<div class="project-gallery">';
            foreach ($gallery as $image_id) {
                echo wp_get_attachment_image($image_id, 'medium');
            }
            echo '</div>';
        }
    }

    /**
     * set default settings
     * @return void
     */
    public function setDefaults() {
//	    add_option( ProjectSettings::SHOW_LINKS, 1);
//	    add_option( ProjectSettings::SHOW_TECHNOLOGIES, 1);
//	    add_option( ProjectSettings::PROJECT_TERM_SINGULAR, 'Project');
//	    add_option( ProjectSettings::PROJECT_TERM_PLURAL, 'Projects');
    }
}