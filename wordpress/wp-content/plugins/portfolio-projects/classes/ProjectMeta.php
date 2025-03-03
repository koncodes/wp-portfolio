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
        <div id="project_gallery_container" class="project-meta-box">
            <div>
                <span class="project-meta-box-label">
                    <input type="button" class="button project-gallery-upload" value="<?php _e('Add Images', TEXT_DOMAIN); ?>">
                </span>
                <span>
                    <ul class="project-gallery-list">
                        <?php
                        if (!empty($gallery)) {
                            foreach ($gallery as $image_id) {
                                echo '<li class="project-gallery-item">';
                                echo wp_get_attachment_image($image_id, 'thumbnail');
                                echo '<input type="hidden" name="'.self::GALLERY.'[]" value="'.$image_id.'">';
                                echo '<button type="button" class="remove-image"><span class="dashicons dashicons-trash"></button>';
                                echo '</li>';
                            }
                        } else {
                            echo '<li class="project-gallery-item project-gallery-placeholder">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 120 120" fill="none">
                                           <rect width="120" height="120" fill="#EFF1F3"/>
                                           <path fill-rule="evenodd" clip-rule="evenodd" d="M33.2503 38.4816C33.2603 37.0472 34.4199 35.8864 35.8543 35.875H83.1463C84.5848 35.875 85.7503 37.0431 85.7503 38.4816V80.5184C85.7403 81.9528 84.5807 83.1136 83.1463 83.125H35.8543C34.4158 83.1236 33.2503 81.957 33.2503 80.5184V38.4816ZM80.5006 41.1251H38.5006V77.8751L62.8921 53.4783C63.9172 52.4536 65.5788 52.4536 66.6039 53.4783L80.5006 67.4013V41.1251ZM43.75 51.6249C43.75 54.5244 46.1005 56.8749 49 56.8749C51.8995 56.8749 54.25 54.5244 54.25 51.6249C54.25 48.7254 51.8995 46.3749 49 46.3749C46.1005 46.3749 43.75 48.7254 43.75 51.6249Z" fill="#687787"/>
                                       </svg>
                                  </li>';
                        }
                        ?>

                    </ul>
                </span>
            </div>
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
                            $('.project-gallery-list').prepend(
                                '<li class="project-gallery-item">' +
                                '<img src="' + attachment.sizes.thumbnail.url + '" width="100">' +
                                '<input type="hidden" name="<?= self::GALLERY ?>[]" value="' + attachment.id + '">' +
                                '<button type="button" class="remove-image"><span class="dashicons dashicons-trash"></button>' +
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
            :is(#project_gallery, #project_information) .inside {
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

            .project-meta-box .project-meta-box-label {
                background: #F9F9F9;
                padding: 20px 10px 20px 20px;
                flex: 0 0 20%;
                min-width: 150px;
                font-weight: 700;
                border-right: 1px solid #dfdfdf;
            }
            .project-meta-box > * > span {
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
                border-bottom: 1px solid #ccc;
                padding: 15px;
            }
            .project-technologies-head {
                flex: 0 0 110px;
            }

            .project-meta-box:last-of-type > label:last-of-type,
            .project-meta-box:last-of-type > div:last-of-type,
            .project-technologies-category:last-of-type {
                border-bottom: 0;
            }


            #project_gallery_container {
                border-top: 1px solid #dfdfdf;
            }
            .project-gallery-list {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin: 0;
            }

            .project-gallery-item {
                display: flex;
                flex-direction: column;
                flex: 0 0 120px;
                height: 120px;
                overflow: hidden;
                position: relative;
                margin: 0;
            }
            .project-gallery-item + .project-gallery-placeholder {
                display: none;
            }
            .project-gallery-item :is(img, svg) {
                width: 120px;
                height: 120px;
                object-fit: cover;
            }
            .project-gallery-item button {
                position: absolute;
                right: 0;
                top: 0;
                padding: 7px;
                border: 0;
                border-radius: 0 0 0 5px;
                color: white;
                background: #bb3333;
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
     * @return string project price
     */
    function getGalleryImages() {
        $post = get_post();
        $gallery = get_post_meta($post->ID, self::GALLERY, true);

        if (!empty($gallery)) {
            $output = '<div class="project-gallery">';
            foreach ($gallery as $image_id) {
                $output .= wp_get_attachment_image($image_id, 'medium');
            }
            $output .= '</div>';
            return $output;
        }

        return '';
    }


    /**
     * set default settings
     * @return void
     */
    public function setDefaults() {
	    add_option( ProjectSettings::SHOW_LINKS, 1);
	    add_option( ProjectSettings::SHOW_TECHNOLOGIES, 1);
	    add_option( ProjectSettings::PROJECT_TERM_SINGULAR, 'Project');
	    add_option( ProjectSettings::PROJECT_TERM_PLURAL, 'Projects');
    }
}