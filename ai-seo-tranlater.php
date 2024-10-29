<?php

/*
Plugin Name: AIKCT Engine Chatbot, ChatGPT, Gemini, GPT-4o Best AI Chatbot
Plugin URI: https://aitoolseo.com
Description: Integrate AI KCT into TinyMCE with KCT AI for content suggestions, text generation, and grammar corrections to enhance your WordPress writing experience.
Author: KCT
Version: 1.6.1
Author URI: https://aitoolseo.com
License: GPL2
*/
if ( !defined( 'ABSPATH' ) ) {
    die( '-1' );
}
define( 'AIKCT_VERSION', '1.6.1' );
error_reporting( E_ALL & ~E_NOTICE & ~E_WARNING );
if ( !function_exists( 'wp_json_encode' ) ) {
    function wp_json_encode(  $value  ) {
        return json_encode( $value );
    }

}
if ( !function_exists( 'ast_fs' ) ) {
    function ast_fs() {
        global $ast_fs;
        if ( !isset( $ast_fs ) ) {
            require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
            $ast_fs = fs_dynamic_init( array(
                'id'              => '16313',
                'slug'            => 'ai-seo-translator',
                'type'            => 'plugin',
                'public_key'      => 'pk_509d52e522ddd417a2299b51ca8d6',
                'is_premium'      => false,
                'premium_suffix'  => 'Pro',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'has_affiliation' => 'all',
                'menu'            => array(
                    'slug'    => 'kctai-settings',
                    'contact' => true,
                    'support' => false,
                    'network' => true,
                ),
                'is_live'         => true,
            ) );
        }
        return $ast_fs;
    }

    ast_fs();
    do_action( 'ast_fs_loaded' );
}
require_once dirname( __FILE__ ) . '/vendor/lib/class-aikct-function.php';
class aikct_suggest_content {
    const VERSION = '1.6.1';

    const CSS_VERSION = self::VERSION;

    public $integrations = [];

    protected $subclasses = [];

    public function __construct() {
        add_action( 'plugins_loaded', array($this, 'load_textdomain') );
        define( 'KCTAI_VER', self::VERSION . random_int( 0, 100000 ) );
        define( 'KCTAI_URL', plugin_dir_url( __FILE__ ) );
        define( 'KCTAI_DIR', plugin_dir_path( __FILE__ ) );
        define( 'KCTAI_BASE', plugin_basename( __FILE__ ) );
        add_action( 'admin_head', [$this, 'kctai_tool_customize_enqueue'] );
        add_action( 'wp_ajax_kct_ai_q', [$this, 'kct_ai_q'] );
        add_action( 'wp_ajax_nopriv_kct_ai_q', [$this, 'kct_ai_q'] );
        add_action( 'add_meta_boxes', [$this, 'my_prompt_meta_box'] );
        add_action(
            'admin_menu',
            array($this, 'kctai_add_settings_menu'),
            10,
            2
        );
        add_action( 'wp_ajax_save_api_keys', array($this, 'save_api_keys') );
        add_action( 'wp_ajax_get_free_api_key', array($this, 'handle_get_free_api_key') );
        add_action( 'wp_ajax_create_image_for_post', array($this, 'create_image_for_post_handler') );
        add_action( 'wp_ajax_suggest_prompt_img', array($this, 'suggest_prompt_img_handler') );
        add_action( 'edit_form_after_title', [$this, 'check_editor_type_and_enqueue_scripts'] );
        add_action( 'wp_ajax_set_post_thumbnail', array($this, 'aikct_set_post_thumbnail_handler') );
        register_activation_hook( __FILE__, array($this, 'activate') );
        register_deactivation_hook( __FILE__, array($this, 'deactivate') );
        add_action( 'wp_ajax_aikct_suggest_tag', array($this, 'aikct_suggest_tag_callback') );
        add_action( 'wp_ajax_aikct_suggest_comments', array($this, 'aikct_suggest_comments_callback') );
        add_action( 'wp_ajax_aikct_add_comments_to_post', array($this, 'aikct_add_comments_to_post_callback') );
        add_action( 'wp_ajax_aikct_idea_title_post', array($this, 'aikct_idea_title_post_callback') );
        add_action( 'wp_ajax_aikct_create_draft_post', array($this, 'aikct_create_draft_callback') );
        add_action( 'wp_ajax_update_integration_option', array($this, 'update_integration_option') );
        $this->kctai_load_func();
        $this->kctai_load_pro();
        $this->aikct_pro_object();
        add_action( 'wp_head', [$this, 'aikct_add_to_head'] );
        add_action( 'wp_footer', [$this, 'aikct_add_to_footer'] );
        $this->integrations = array(
            'aikct_images'           => array(
                'title'  => 'Images AI',
                'desc'   => 'Free AI image generation feature',
                'img'    => '',
                'active' => true,
            ),
            'aikct_tags'             => array(
                'title'  => 'Tags AI',
                'desc'   => 'Free AI tags suggestion feature',
                'img'    => '',
                'active' => true,
            ),
            'aikct_comment'          => array(
                'title'  => 'Comment AI',
                'desc'   => 'Free AI comment suggestion feature',
                'img'    => '',
                'active' => true,
            ),
            'aikct_blogfromyoutube'  => array(
                'title'  => 'Convert YouTube to Blog AI',
                'desc'   => 'Create blog posts from YouTube video links',
                'img'    => '',
                'active' => true,
            ),
            'aikct_rewrite_from_url' => array(
                'title'  => 'Rewrite from URL AI',
                'desc'   => 'Create blog posts from URLs',
                'img'    => '',
                'active' => true,
            ),
            'aikct_idea_title'       => array(
                'title'  => 'Idea Title',
                'desc'   => 'Suggest titles for posts',
                'img'    => '',
                'active' => true,
            ),
        );

        foreach ($this->integrations as $key => $integration) {
            $value = $integration['active'] ? 1 : 0;
            
            if (get_option($key) === false) {
                update_option($key, $value);
            }
        }

    }

    public function aikct_pro_object() {
        if ( class_exists( 'aikct_rewrite_post' ) ) {
            $aikct_rewrite_post = new aikct_rewrite_post();
            $this->addSubclass( $aikct_rewrite_post );
        } else {
            print_r( 'a' );
        }
    }

    public function addSubclass( $subclass ) {
        $this->subclasses[] = $subclass;
    }

    public function aikct_add_to_head() {
        echo '<!-- aikct head-->';
    }

    public function aikct_add_to_footer() {
        echo '<!-- aikct footer-->';
    }

    public function render_list_feature() {
        foreach ( $this->subclasses as $subclass ) {
            $subclass->render_feature( $subclass->settings );
        }
    }

    public function aikct_create_draft_callback() {
        if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'aikct_nonce' ) ) {
            wp_send_json_error( 'Invalid security token!' );
            wp_die();
        }
        $title = sanitize_text_field( $_POST['title'] );
        $categories = sanitize_text_field( $_POST['categories'] );
        $tags = sanitize_text_field( $_POST['tags'] );
        $post_data = array(
            'post_title'   => $title,
            'post_content' => '',
            'post_status'  => 'draft',
            'post_author'  => get_current_user_id(),
        );
        $post_id = wp_insert_post( $post_data );
        if ( $post_id ) {
            $category_ids = array();
            $categories_array = explode( ',', $categories );
            foreach ( $categories_array as $category_name ) {
                $category_name = trim( $category_name );
                if ( empty( $category_name ) ) {
                    continue;
                }
                $category_id = term_exists( $category_name, 'category' );
                if ( $category_id === 0 || $category_id === null ) {
                    $new_category = wp_insert_term( $category_name, 'category' );
                    if ( is_wp_error( $new_category ) ) {
                        wp_send_json_error( 'Error creating category: ' . $category_name . ' - ' . $new_category->get_error_message() );
                        wp_die();
                    }
                    $category_id = $new_category['term_id'];
                }
                $category_ids[] = $category_id;
            }
            wp_set_post_categories( $post_id, $category_ids );
            $tags_array = explode( ',', $tags );
            $tags_array = array_map( 'trim', $tags_array );
            wp_set_post_tags( $post_id, $tags_array );
            $edit_link = get_edit_post_link( $post_id );
            wp_send_json_success( array(
                'edit_link' => $edit_link,
            ) );
        } else {
            wp_send_json_error();
        }
        wp_die();
    }

    public function aikct_suggest_tag_callback() {
        if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'aikct_nonce' ) ) {
            wp_send_json_error( 'Invalid security token!' );
            wp_die();
        }
        $title = sanitize_text_field( $_POST['title'] );
        $content = wp_kses_post( $_POST['content'] );
        $tag = $this->aikct_prompt_tags_generation( $title, $content );
        if ( isset( $tag ) ) {
            wp_send_json_success( $tag );
        } else {
            wp_send_json_error( 'no tag' );
        }
    }

    public function aikct_idea_title_post_callback() {
        if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'aikct_nonce' ) ) {
            wp_send_json_error( 'Invalid security token!' );
            wp_die();
        }
        $categories = get_categories();
        $result = [];
        foreach ( $categories as $category ) {
            $args = [
                'category'       => $category->term_id,
                'posts_per_page' => 10,
                'orderby'        => 'rand',
                'fields'         => 'titles',
            ];
            $posts = get_posts( $args );
            $category_titles = "--" . $category->name . ":\n";
            foreach ( $posts as $post ) {
                $category_titles .= "----" . $post->post_title . "\n";
            }
            $result[] = $category_titles;
        }
        $output = implode( "\n", $result );
        $prompt = "The most important: the response must be in the SAME LANGUAGE as the original text (text between \"======\"). Given the following sitemap structure of a website with various uncategorized topics, generate 10 relevant post suggestions that align with the content areas listed. The topics include:\r\n            ======  %%SITEMAP%% ====== \r\n            Ensure the suggestions are engaging, informative, and suitable for the target audience of the website. The suggestions should also consider current trends and user interests in the respective fields. Output struct json all in ideas array.  {title:'',categoires:'',tags:''}";
        $question = str_replace( '%%SITEMAP%%', $output, $prompt );
        $proxy = new kctaiproxy();
        if ( $proxy->status ) {
            $result = $proxy->sendRequest( $question );
            $result['output'] = $this->aikct_return_json( $result['output'] );
            wp_send_json_success( $result );
        } else {
            $result = [
                'output' => 'Please set API KEY',
                'token'  => '',
                'msg'    => 'Please set API KEY',
                'model'  => 'Aikct',
            ];
            wp_send_json_error( $result );
        }
        wp_die();
    }

    public function aikct_suggest_comments_callback() {
        if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'aikct_nonce' ) ) {
            wp_send_json_error( 'Invalid security token!' );
            wp_die();
        }
        $title = sanitize_text_field( $_POST['title'] );
        $content = wp_kses_post( $_POST['content'] );
        $comments = $this->aikct_prompt_comments_generation( $title, $content );
        if ( isset( $comments ) ) {
            wp_send_json_success( $comments );
        } else {
            wp_send_json_error( 'no tag' );
        }
    }

    public function aikct_prompt_tags_generation( $title, $content ) {
        $question = "The most important: The results must only be in JSON format, the response must be in the SAME LANGUAGE as the original text (text between \"======\").\r\n    Create %%NUMMBERCOMMENT%% tag for the article.\r\n    tag only 2 or 3 word\r\n    tag must have a ====== %%TITLTE%% , %%CONTENT%% ======  intellectual level.\r\n    The post\\'s content is between \"=========\". \r\n    The results must only be in JSON format, with this exact format, you have to fill empty values,Each item in tag has the form { \"tag\": \"\" }\r\n    ";
        $question = str_replace( '%%TITLTE%%', $title, $question );
        $question = str_replace( '%%CONTENT%%', $content, $question );
        $question = str_replace( '%%NUMMBERCOMMENT%%', rand( 8, 15 ), $question );
        $proxy = new kctaiproxy();
        if ( $proxy->status ) {
            $result = $proxy->sendRequest( $question );
            $result['output'] = $this->aikct_return_json( $result['output'] );
            return $result;
        } else {
            $result = [
                'output' => 'Please set API KEY',
                'token'  => '',
                'msg'    => 'Please set API KEY',
                'model'  => 'Aikct',
            ];
            return $result;
        }
    }

    public function aikct_prompt_comments_generation( $title, $content ) {
        $question = "The most important: the response must be in the SAME LANGUAGE as the original text (text between \"======\").\r\nCreate %%NUMMBERCOMMENT%% comments from readers of the article.\r\nEach comment have a different tone about the post\\'s content. Tone must be either positive, negative, informative, argumentative, ironic, sarcastic or comical.\r\nCommenters must have a ====== %%TITLTE%% , %%CONTENT%% ======  intellectual level.\r\nMost of comment must have several syntaxics errors.\r\nThe answer must also include neutral nicknames (unrelated to the topic) of one to two words separated by spaces. Ensure that the nicknames are diverse in style, gender-neutral where possible, and suitable for various uses, such as online gaming, social media, or personal branding. Nickname must be in the SAME LANGUAGE as the original text\r\nThe post\\'s content is between \"=========\". \r\nThe answers must only be in JSON format, with this exact format, you have to fill empty values,Each item in comments has the form { \"nickname\": \"\", \"comment\": \"\", \"tone\": \"\" }\r\n    ";
        $question = str_replace( '%%TITLTE%%', $title, $question );
        $question = str_replace( '%%CONTENT%%', $content, $question );
        $question = str_replace( '%%NUMMBERCOMMENT%%', rand( 8, 15 ), $question );
        $proxy = new kctaiproxy();
        if ( $proxy->status ) {
            $result = $proxy->sendRequest( $question );
            $result['output'] = $this->aikct_return_json( $result['output'] );
            return $result;
        } else {
            $result = [
                'output' => 'Please set API KEY',
                'token'  => '',
                'msg'    => 'Please set API KEY',
                'model'  => 'Aikct',
            ];
            return $result;
        }
    }

    public function aikct_add_comments_to_post_callback() {
        if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'aikct_nonce' ) ) {
            wp_send_json_error( 'Invalid security token!' );
            wp_die();
        }
        if ( !isset( $_POST['post_id'] ) || !isset( $_POST['comments'] ) ) {
            wp_send_json_error( 'Invalid data provided' );
            return;
        }
        $post_id = intval( $_POST['post_id'] );
        $comments = $_POST['comments'];
        if ( get_post( $post_id ) === null ) {
            wp_send_json_error( 'Post not found' );
            return;
        }
        foreach ( $comments as $comment_data ) {
            $comment_content = sanitize_text_field( $comment_data['comment'] );
            $comment_author = sanitize_text_field( $comment_data['nickname'] );
            $comment = array(
                'comment_post_ID'  => $post_id,
                'comment_content'  => $comment_content,
                'comment_author'   => $comment_author,
                'comment_approved' => 1,
            );
            $comment_id = wp_insert_comment( $comment );
            if ( !$comment_id ) {
                wp_send_json_error( 'Failed to insert comment: ' . $comment_content );
                return;
            }
        }
        wp_send_json_success( 'Comments added successfully' );
    }

    public function aikct_return_json( $result ) {
        $pattern = '/\\{(?:[^{}]|(?R))*\\}/';
        preg_match_all( $pattern, $result, $matches );
        $arritem = array();
        foreach ( $matches[0] as $jsonString ) {
            $decodedJson = json_decode( $jsonString, true );
            if ( $decodedJson !== null ) {
                if ( isset( $decodedJson['tags'] ) ) {
                    foreach ( $decodedJson['tags'] as $item ) {
                        $arritem[] = $item;
                    }
                } elseif ( isset( $decodedJson['comments'] ) ) {
                    foreach ( $decodedJson['comments'] as $item ) {
                        $arritem[] = $item;
                    }
                } elseif ( isset( $decodedJson['ideas'] ) ) {
                    foreach ( $decodedJson['ideas'] as $item ) {
                        $arritem[] = $item;
                    }
                } else {
                    $arritem[] = $decodedJson;
                }
            } else {
                return null;
            }
        }
        return $arritem;
    }

    public function activate() {
        aikct_pingbackstatus( 'active ' . AIKCT_VERSION );
    }

    public function deactivate() {
        aikct_pingbackstatus( 'deactivate ' . AIKCT_VERSION );
    }

    public function suggest_prompt_img_handler() {
        if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'aikct_nonce' ) ) {
            wp_send_json_error( 'Invalid security token!' );
            wp_die();
        }
        $title = sanitize_text_field( $_POST['title'] );
        $content = wp_kses_post( $_POST['content'] );
        $prompt = $this->aikct_find_prompt( $title, $content );
        $prompt = $prompt['output'];
        if ( $prompt ) {
            wp_send_json_success( $prompt );
        } else {
            wp_send_json_error( 'False' );
        }
    }

    public function create_image_for_post_handler() {
        if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'aikct_nonce' ) ) {
            wp_send_json_error( 'Invalid security token!' );
            wp_die();
        }
        $title = sanitize_text_field( $_POST['title'] );
        $content = wp_kses_post( $_POST['content'] );
        $prompt = wp_kses_post( $_POST['prompt'] );
        $img = $this->aikct_create_imagesAI( $prompt, $title, $content );
        if ( $img ) {
            wp_send_json_success( $img );
        } else {
            wp_send_json_error( 'False' );
        }
    }

    public function aikct_set_post_thumbnail_handler() {
        if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'aikct_nonce' ) ) {
            wp_send_json_error( 'Invalid security token!' );
            wp_die();
        }
        $post_id = intval( $_POST['post_id'] );
        $thumbnail_url = esc_url( $_POST['thumbnail_url'] );
        $attachment_id = media_sideload_image(
            $thumbnail_url,
            $post_id,
            null,
            'id'
        );
        if ( is_wp_error( $attachment_id ) ) {
            wp_send_json_error( 'Failed to upload image.' );
        } else {
            set_post_thumbnail( $post_id, $attachment_id );
            wp_send_json_success( 'Thumbnail set successfully!' );
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'ai-seo-translator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    public function check_editor_type_and_enqueue_scripts() {
        global $post;
        if ( $post->post_type === 'post' ) {
            $editor_type = $this->wpse_is_gutenberg_editor();
            switch ( $editor_type ) {
                case 'gutenberg':
                    add_action( 'admin_enqueue_scripts', [$this, 'wpse_gutenberg_editor_action'] );
                    add_action( 'enqueue_block_editor_assets', [$this, 'aikct_enqueue_gutenberg_button'] );
                    break;
                case 'classic':
                    add_filter( 'mce_buttons', array($this, 'add_custom_tinymce_button') );
                    add_filter( 'mce_external_plugins', array($this, 'add_custom_tinymce_plugin') );
                    break;
                default:
                    break;
            }
        }
    }

    public function aikct_create_imagesAI( $prompt, $title, $content = '' ) {
        // $model = 'stabilityai/stable-diffusion-3-medium-diffusers';
        $model = 'black-forest-labs/FLUX.1-dev';
        $aikct_image_control = new aikct_image_control('', '');
        $aikct_apikey_token_hungingface = get_option( 'aikct_apikey_token_huggingface' );
        if ( empty( $aikct_apikey_token_hungingface ) ) {
            $arr = array(
                'url'    => false,
                'prompt' => 'API key is missing. Please set the Hugging Face API key.' . $aikct_apikey_token_hungingface,
            );
            return $arr;
        }
        $image = $aikct_image_control->hungingfaceimg( $aikct_apikey_token_hungingface, $prompt, $model );
        if ( $image ) {
            $upload_dir = wp_upload_dir();
            $filename = sanitize_file_name( strtolower( $title ) ) . '_' . time() . '.png';
            $file_path = $upload_dir['path'] . '/' . $filename;
            file_put_contents( $file_path, $image );
            $image_size = @getimagesize( $file_path );
            if ( $image_size !== false ) {
                $wp_file_type = wp_check_filetype( $filename, null );
                $attachment = array(
                    'post_mime_type' => $wp_file_type['type'],
                    'post_title'     => sanitize_file_name( $title ),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                );
                $attachment_id = wp_insert_attachment( $attachment, $file_path );
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
                wp_update_attachment_metadata( $attachment_id, $attachment_data );
                $image_url = wp_get_attachment_url( $attachment_id );
                $arr = array(
                    'url'    => $image_url,
                    'prompt' => $prompt,
                );
                return $arr;
            } else {
                return false;
            }
        }
        return false;
    }

    public function wpse_is_gutenberg_editor() {
        if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
            return 'gutenberg';
        }
        if ( function_exists( 'get_current_screen' ) ) {
            $current_screen = get_current_screen();
            if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
                return 'gutenberg';
            }
        }
        return 'classic';
    }

    public function wpse_gutenberg_editor_action() {
    }

    public function aikct_enqueue_gutenberg_button() {
        wp_enqueue_script(
            'aikct-gutenberg-button',
            plugin_dir_url( __FILE__ ) . 'js/gutenberg-ai-kct.js',
            array(
                'wp-plugins',
                'wp-edit-post',
                'wp-element',
                'wp-components'
            ),
            filemtime( plugin_dir_path( __FILE__ ) . 'js/gutenberg-ai-kct.js' ),
            true
        );
    }

    public function add_custom_tinymce_button( $buttons ) {
        array_push( $buttons, 'aikct_ask', 'aikct_ask_pro' );
        return $buttons;
    }

    public function add_custom_tinymce_plugin( $plugins ) {
        $plugins['aikct_ask'] = KCTAI_URL . 'js/buttonmce.js?v=' . KCTAI_VER;
        $plugins['aikct_ask_pro'] = KCTAI_URL . 'js/kct_pro.js?v=' . KCTAI_VER;
        return $plugins;
    }

    public function aikct_find_prompt( $title, $content ) {
        $string = "You are a prompt engineer. Your task is to carefully analyze the provided blog title and content. And create a prompt for the stable-diffusion-xl-lightning model, enabling the generation of a visual that matches the text. This prompt should concisely capture the essence, main themes, and nuances of the provided blog post, aiming to facilitate the creation of the most accurate and engaging image that reflects the main message and context without being overly complex or detailed. The prompt must be that is simple enough to generate an image and not too detailed. You must create a different prompt each time a request is sent.  You must generate only a prompt. The prompt must be in English. Title: '{$title}, content : '" . strip_tags( $content );
        $proxy = new kctaiproxy();
        if ( $proxy->status ) {
            $result = $proxy->sendRequest( $string );
            return $result;
        } else {
            $result = [
                'output' => 'Please set API KEY',
                'token'  => '',
                'msg'    => 'Please set API KEY',
                'model'  => 'Aikct',
            ];
            return $result;
        }
    }

    public function handle_get_free_api_key() {
        if ( !isset( $_POST['get_free_api_key_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['get_free_api_key_nonce'] ) ), 'get_free_api_key_action' ) ) {
            wp_send_json_error( 'Invalid nonce.' );
            wp_die();
        }
        $token = get_option( 'aikct_apikey_token_huggingface', false );
        if ( $token === false ) {
            update_option( 'aikct_apikey_token_huggingface', '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' );
        }
        $url = 'https://raw.githubusercontent.com/aiautotool/aikct/main/aikct.txt';
        $file_path = KCTAI_DIR . '/inc/aikct.php';
        if ( file_exists( $file_path ) ) {
            if ( wp_delete_file( $file_path ) ) {
                wp_send_json_success( 'File deleted successfully.' );
            } else {
                wp_send_json_error( 'Failed to delete the file.' );
            }
        } else {
            $response = wp_remote_get( $url );
            if ( is_wp_error( $response ) ) {
                wp_send_json_error( 'Failed to fetch the content.' );
                return;
            }
            $content = wp_remote_retrieve_body( $response );
            if ( empty( $content ) ) {
                wp_send_json_error( 'No content retrieved.' );
                return;
            }
            global $wp_filesystem;
            if ( !function_exists( 'WP_Filesystem' ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
            WP_Filesystem();
            if ( $wp_filesystem->put_contents( $file_path, $content, FS_CHMOD_FILE ) ) {
                wp_send_json_success( esc_html__( 'Save API key free success', 'ai-seo-translator' ) );
            } else {
                wp_send_json_error( esc_html__( 'Failed to write the file.', 'ai-seo-translator' ) );
            }
        }
    }

    public function save_api_keys() {
        if ( isset( $_POST['save_api_keys_nonce'] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $_POST['save_api_keys_nonce'] ) );
            if ( !wp_verify_nonce( $nonce, 'save_api_keys_action' ) ) {
                wp_send_json_error( 'Invalid nonce.' );
                wp_die();
            }
        }
        $apiKeyGPT35 = ( isset( $_POST['apiKeyGPT35'] ) ? sanitize_text_field( wp_unslash( $_POST['apiKeyGPT35'] ) ) : '' );
        $apiKeyGPT4 = ( isset( $_POST['apiKeyGPT4'] ) ? sanitize_text_field( wp_unslash( $_POST['apiKeyGPT4'] ) ) : '' );
        $apiKeyGemini = ( isset( $_POST['apiKeyGemini'] ) ? sanitize_text_field( wp_unslash( $_POST['apiKeyGemini'] ) ) : '' );
        // $apiKeyGemini = isset($_POST['apiKeyGemini']) ? sanitize_text_field(wp_unslash($_POST['apiKeyGemini'])) : '';
        $languages = ( isset( $_POST['language'] ) ? array_map( 'sanitize_text_field', $_POST['language'] ) : array() );
        $apiKeyCloudflareToken = ( isset( $_POST['apiKeyCloudflareToken'] ) ? sanitize_text_field( wp_unslash( $_POST['apiKeyCloudflareToken'] ) ) : '' );
        $apiKeyCloudflareAccountID = ( isset( $_POST['apiKeyCloudflareAccountID'] ) ? sanitize_text_field( wp_unslash( $_POST['apiKeyCloudflareAccountID'] ) ) : '' );
        $apiKeyHuggingface = ( isset( $_POST['apiKeyHuggingface'] ) ? sanitize_text_field( wp_unslash( $_POST['apiKeyHuggingface'] ) ) : '' );
        if ( $apiKeyGPT35 !== '' ) {
            update_option( 'aikct_apikey_gpt35', $apiKeyGPT35 );
        }
        if ( $apiKeyGPT4 !== '' ) {
            update_option( 'aikct_apikey_gpt4o', $apiKeyGPT4 );
        }
        if ( $apiKeyGemini !== '' ) {
            update_option( 'aikct_apikey_gemini', $apiKeyGemini );
        }
        if ( $apiKeyCloudflareToken !== '' ) {
            update_option( 'aikct_apikey_token_cloudfalre', $apiKeyCloudflareToken );
        }
        if ( $apiKeyCloudflareAccountID !== '' ) {
            update_option( 'aikct_apikey_account_id_cloudfalre', $apiKeyCloudflareAccountID );
        }
        if ( $apiKeyHuggingface !== '' ) {
            update_option( 'aikct_apikey_token_huggingface', $apiKeyHuggingface );
        }
        if ( !empty( $languages ) ) {
            update_option( 'aikct_apikey_languages', $languages );
        }
        wp_send_json_success();
    }

    private function kctai_load_func() {
        $inc_directory = KCTAI_DIR . 'inc';
        $inc_files = glob( $inc_directory . '/*.php' );
        if ( $inc_files ) {
            foreach ( $inc_files as $file ) {
                if ( is_file( $file ) ) {
                    include $file;
                }
            }
        }
    }

    private function kctai_load_pro() {
        $inc_directory = KCTAI_DIR . 'pro';
        if ( is_dir( $inc_directory ) ) {
            $inc_files = glob( $inc_directory . '/*.php' );
            if ( $inc_files ) {
                foreach ( $inc_files as $file ) {
                    if ( is_file( $file ) ) {
                        include $file;
                    }
                }
            }
        } else {
            error_log( 'Directory does not exist: ' . $inc_directory );
        }
    }

    public function kctai_tool_customize_enqueue() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_style(
            'kctai',
            KCTAI_URL . 'css/index.css',
            array(),
            KCTAI_VER
        );
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
            array(),
            KCTAI_VER
        );
        wp_enqueue_script(
            'sweetalert2',
            'https://cdn.jsdelivr.net/npm/sweetalert2@11"',
            array(),
            '4.6.0'
        );
        wp_enqueue_script(
            'aikct_wordcount',
            plugin_dir_url( __FILE__ ) . 'js/wordcount.js',
            array(),
            KCTAI_VER,
            true
        );
        wp_enqueue_script(
            'aikct_Readability',
            plugin_dir_url( __FILE__ ) . 'js/Readability.js',
            array(),
            KCTAI_VER,
            true
        );
        wp_enqueue_style(
            'tagify-css',
            'https://cdnjs.cloudflare.com/ajax/libs/tagify/4.31.2/tagify.css',
            array(),
            KCTAI_VER,
            'all'
        );
        // Enqueue Tagify JavaScript
        wp_enqueue_script(
            'tagify-js',
            'https://cdnjs.cloudflare.com/ajax/libs/tagify/4.31.2/tagify.min.js',
            array(),
            KCTAI_VER,
            true
        );
        wp_enqueue_script(
            'aikct',
            plugin_dir_url( __FILE__ ) . 'js/aikct.js',
            array(),
            KCTAI_VER,
            true
        );
        $languages = get_option( 'aikct_apikey_languages', array('US English') );
        $default_language = ( is_array( $languages ) && !empty( $languages ) ? $languages[0] : 'US English' );
        wp_localize_script( 'aikct', 'aikct_js', array(
            'nonce'      => wp_create_nonce( 'aikct_nonce' ),
            'audio_key'  => KCTAI_URL . 'img/keyboard.mp3',
            'ajax_url'   => admin_url( 'admin-ajax.php' ),
            'aikct_lang' => $default_language,
        ) );
        $editor_type = $this->wpse_is_gutenberg_editor();
        switch ( $editor_type ) {
            case 'gutenberg':
                wp_enqueue_script(
                    'ai-kct-gutenberg-js',
                    plugin_dir_url( __FILE__ ) . 'js/gutenberg-ai-kct.js',
                    array(
                        'wp-plugins',
                        'wp-edit-post',
                        'wp-components',
                        'wp-data',
                        'wp-compose'
                    ),
                    KCTAI_VER,
                    true
                );
                wp_localize_script( 'ai-kct-gutenberg-js', 'wpseEditor', array(
                    'editorType' => $editor_type,
                ) );
                break;
            case 'classic':
                wp_enqueue_script(
                    'wpse-editor-detect',
                    plugin_dir_url( __FILE__ ) . 'js/script.js',
                    array(),
                    KCTAI_VER,
                    true
                );
                wp_localize_script( 'wpse-editor-detect', 'wpseEditor', array(
                    'editorType' => $editor_type,
                ) );
                break;
            default:
                break;
        }
    }

    public function kct_ai_q() {
        check_ajax_referer( 'kct_ai_q_nonce', 'security' );
        $languages = get_option( 'aikct_apikey_languages', array('US English') );
        $default_language = ( is_array( $languages ) && !empty( $languages ) ? $languages[0] : 'US English' );
        if ( isset( $_POST['prompt'] ) ) {
            $prompt = sanitize_text_field( wp_unslash( $_POST['prompt'] ) );
        } else {
            $prompt = '';
        }
        $prompt .= ', Output in language : ' . $default_language;
        $proxy = new kctaiproxy();
        if ( $proxy->status ) {
            if ( isset( $_POST['aikct_chatlog'] ) ) {
                $result = $proxy->sendRequest( $prompt, wp_unslash( $_POST['aikct_chatlog'] ) );
            } else {
                $result = $proxy->sendRequest( $prompt );
            }
            wp_send_json_success( $result );
            wp_die();
        } else {
            $result = [
                'output' => 'Please set API KEY',
                'token'  => '',
                'msg'    => 'Please set API KEY',
                'model'  => 'Aikct',
            ];
            wp_send_json_success( $result );
            wp_die();
        }
    }

    public function my_prompt_meta_box() {
        global $post;
        if ( $post->post_type === 'post' ) {
            add_meta_box(
                'my_prompt_meta',
                'Prompt Input',
                [$this, 'my_prompt_meta_box_callback'],
                'post',
                'side',
                'high'
            );
        }
    }

    public function my_prompt_meta_box_callback() {
        global $post;
        $aikct_chatbot = new aikct_chatbot();
        $aikct_chatbot->render_meta_box( $post );
    }

    public function kctai_add_settings_menu() {
        add_menu_page(
            'KCT AI Engine Settings',
            'KCT AI Engine Settings',
            'manage_options',
            'kctai-settings',
            [$this, 'kctai_settings_page'],
            '',
            2
        );
        add_submenu_page(
            'kctai-settings',
            'Integrations',
            'Integrations',
            'manage_options',
            'kctai-integrations',
            [$this, 'kctai_Integrations']
        );
    }

    public function kctai_Integrations() {
        // Khai báo các tính năng
        $arr = $this->integrations;
        ?>
    <div class="aikct_box">
    <h3>Pro features</h3>
        <div class="integration-grid">
            <?php 
        $this->render_list_feature();
        ?>
        </div>
    </div>
    <div class="aikct_box">
        <h3>Free features</h3>
        <div class="integration-grid">
            <?php 
        if ( count( $arr ) > 0 ) {
            foreach ( $arr as $key => $item ) {
                $is_enabled = get_option( $key, false );
                ?>
                    <div class="integration-card">
                        <div class="integration-header">
                            <h2><i class="fa-regular fa-circle-check"></i> <?php 
                echo esc_html( $item['title'] );
                ?></h2>

                            <label>
                                         <span class="aikct_box_toggle-switch">
                                            <input <?php 
                checked( $is_enabled );
                ?> class="integration-toggle" data-key="<?php 
                echo esc_attr( $key );
                ?>"  type="checkbox">
                                            <span class="aikct_box_slider"></span>
                                        </span>
                                    </label>
                        </div>
                        <p><?php 
                echo esc_html( $item['desc'] );
                ?>.</p>
                    </div>
                    <?php 
            }
        }
        ?>
        </div>
    </div>


    <script>
    jQuery(document).ready(function($) {
        $('.integration-toggle').on('change', function() {
            const key = $(this).data('key');
            const value = $(this).is(':checked') ? 1 : 0; // 1 cho true, 0 cho false
            
            // Gửi AJAX request để cập nhật wp_option
            $.ajax({
                url: '<?php 
        echo admin_url( "admin-ajax.php" );
        ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_integration_option',
                    key: key,
                    value: value
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Cập nhật thành công');
                    } else {
                        console.log('Cập nhật thất bại');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Có lỗi xảy ra: ' + error);
                }
            });
        });
    });
</script>

    <?php 
    }

    public function update_integration_option() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'You do not have permission to perform this action.' );
            return;
        }
        if ( isset( $_POST['key'] ) && isset( $_POST['value'] ) ) {
            $key = sanitize_text_field( wp_unslash( $_POST['key'] ) );
            $value = intval( wp_unslash( $_POST['value'] ) );
            if ( in_array( $value, [0, 1] ) ) {
                if ( update_option( $key, $value ) ) {
                    wp_send_json_success( 'Option updated successfully.' );
                } else {
                    wp_send_json_error( 'Update failed or no change was made.' );
                }
            } else {
                wp_send_json_error( 'Invalid value.' );
            }
        } else {
            wp_send_json_error( 'Missing key or value.' );
        }
        wp_die();
    }

    public function kctai_settings_page() {
        $apiKeyGpt35 = get_option( 'aikct_apikey_gpt35', '' );
        $apiKeyGpt4o = get_option( 'aikct_apikey_gpt4o', '' );
        $apiKeyGemini = get_option( 'aikct_apikey_gemini', '' );
        $aikct_apikey_token_cloudfalre = get_option( 'aikct_apikey_token_cloudfalre' );
        $aikct_apikey_account_id_cloudfalre = get_option( 'aikct_apikey_account_id_cloudfalre' );
        $aikct_apikey_token_hungingface = get_option( 'aikct_apikey_token_huggingface' );
        ?>
        <div class="wrap">
            <h2></h2>
            <div class="aikct_box_container">
                <div class="aikct_box_header">
                    <h2 class="icon-text"><svg width="24" height="24" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H5Zm6.8 11.5.5 1.2a68.3 68.3 0 0 0 .7 1.1l.4.1c.3 0 .5 0 .7-.3.2-.1.3-.3.3-.6l-.3-1-2.6-6.2a20.4 20.4 0 0 0-.5-1.3l-.5-.4-.7-.2c-.2 0-.5 0-.6.2-.2 0-.4.2-.5.4l-.3.6-.3.7L5.7 15l-.2.6-.1.4c0 .3 0 .5.3.7l.6.2c.3 0 .5 0 .7-.2l.4-1 .5-1.2h3.9ZM9.8 9l1.5 4h-3l1.5-4Zm5.6-.9v7.6c0 .4 0 .7.2 1l.7.2c.3 0 .6 0 .8-.3l.2-.9V8.1c0-.4 0-.7-.2-.9a1 1 0 0 0-.8-.3c-.2 0-.5.1-.7.3l-.2 1Z"></path></svg> KCT AI Engine AI Chatbot</h2>
                </div>
                <div class="aikct_box_tab">
                     
                    <button class="tablink active" onclick="aikcttab(event, 'tab1')"><i class="fa-solid fa-key"></i> Set API Key</button>
                    <button class="tablink" onclick="aikcttab(event, 'tab2')"><i class="fa-solid fa-unlock-keyhole"></i> Active Free API</button>
                    <button class="tablink" onclick="aikcttab(event, 'tab3')"><i class="fa-solid fa-address-card"></i> About us</button>
                </div>
               

                <div id="tab1" class="aikct_box_tabcontent active">
                    <div class="aikct_box_content">
                        <form id="apiKeyForm">
                            <?php 
        wp_nonce_field( 'save_api_keys_action', 'save_api_keys_nonce' );
        ?>

                            

                            <div class="aikct_box">
                                <h3>Language default</h3>
                                <?php 
        $languages = get_option( 'aikct_apikey_languages', array('US English') );
        $default_language = ( is_array( $languages ) && !empty( $languages ) ? $languages[0] : 'US English' );
        ?>
                                 <label for="aikct_language"><?php 
        esc_html_e( 'Output Language', 'ai-seo-translator' );
        ?></label>
                                <input name="aikct_language" id="aikct_language" value="<?php 
        esc_html_e( $default_language, 'ai-seo-translator' );
        ?>">
                                <script type="text/javascript">
                                     jQuery(document).ready(function ($) {
                                   
                                });
                                </script>
                            </div>

                            <div class="aikct_box">
                                <h3>Config API key for Content</h3>
                                <label for="apiKeyGPT3.5"><i class="fa-regular fa-circle-check"></i> API Key Chat GPT 3.5</label>
                                <input type="text" id="apiKeyGPT35" value="<?php 
        echo esc_html( $apiKeyGpt35 );
        ?>" name="apiKeyGPT35" placeholder="Enter API Key for GPT 3.5">

                                <label for="apiKeyGPT4"><i class="fa-regular fa-circle-check"></i> API Key GPT-4o</label>
                                <input type="text" id="apiKeyGPT4" value="<?php 
        echo esc_html( $apiKeyGpt4o );
        ?>" name="apiKeyGPT4" placeholder="Enter API Key for GPT-4o">

                                <label for="apiKeyGemini"><i class="fa-regular fa-circle-check"></i> API Key Gemini</label>
                                <input type="text" id="apiKeyGemini" value="<?php 
        echo esc_html( $apiKeyGemini );
        ?>" name="apiKeyGemini" placeholder="Enter API Key for Gemini">

                            </div>
                            <div class="aikct_box">
                                <h3>Config API key for AI-generated images :   huggingface.co</h3>
                                <!--  <label for="aikct_apikey_token_cloudfalre"><i class="fa-regular fa-circle-check"></i> Api Token Key for Workers AI Cloudflare <a target="_blank" href="https://developers.cloudflare.com/fundamentals/api/get-started/create-token/">Help?</a></label>
                                <input type="text" id="aikct_apikey_token_cloudfalre" value="<?php 
        echo esc_html( $aikct_apikey_token_cloudfalre );
        ?>" name="aikct_apikey_token_cloudfalre" placeholder="Enter API token for Cloudfalre">

                                <label for="aikct_apikey_account_id_cloudfalre"><i class="fa-regular fa-circle-check"></i> ID Account cloudflare <a target="_blank" href="https://developers.cloudflare.com/fundamentals/setup/find-account-and-zone-ids/">Help?</a></label>
                                <input type="text" id="aikct_apikey_account_id_cloudfalre" value="<?php 
        echo esc_html( $aikct_apikey_account_id_cloudfalre );
        ?>" name="aikct_apikey_account_id_cloudfalre" placeholder="Enter Acount ID for Cloudfalre"> -->


                                <label for="aikct_apikey_token_hungingface"><i class="fa-regular fa-circle-check"></i> Api Token Key Huggingface  <a target="_blank" href="https://huggingface.co/docs/api-inference/quicktour#get-your-api-token">Help?</a></label>
                                <input type="text" id="aikct_apikey_token_hungingface" value="<?php 
        echo esc_html( $aikct_apikey_token_hungingface );
        ?>" name="aikct_apikey_token_hungingface" placeholder="Enter API Token for Huggingface">
                            </div>

                            <p class="help-text">Please enter your API keys in the fields above.</p>
                            <?php 
        echo loader( '0' );
        ?>
                            
                             <button class="aikct_box_save-btn"  id="saveApiKeysButton">
                                <i class="aikct_box_icon">&#x1F4BE;</i> 
                                Save API Keys
                            </button>
                        </form>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {

                                 var aikct_language = inittagfy(document.querySelector('#aikct_language'), '<?php 
        esc_html_e( 'US English, UK English, French, German, Spanish, Italian, Portuguese, Dutch, Russian, Chinese, Japanese, Korean, Arabic, Hindi, Vietnamese, Thai, Turkish, Polish, Swedish, Norwegian, Danish, Finnish, Greek, Czech, Hungarian, Romanian, Hebrew', 'ai-seo-translator' );
        ?>', 1);


                                $('#saveApiKeysButton').on('click', function(event) {
                                    event.preventDefault(); 
                                    $('.aikct_box_loading').addClass('show').removeClass('hidden');

                                    var apiKeyGPT35 = $('#apiKeyGPT35').val();
                                    var apiKeyGPT4 = $('#apiKeyGPT4').val();
                                    var apiKeyGemini = $('#apiKeyGemini').val();
                                    var apiKeyCloudflareToken = $('#aikct_apikey_token_cloudfalre').val();
                                    var apiKeyCloudflareAccountID = $('#aikct_apikey_account_id_cloudfalre').val();
                                    var apiKeyHuggingface = $('#aikct_apikey_token_hungingface').val();
                                    var apiKeyHuggingface = $('#aikct_apikey_token_hungingface').val();
                                    var languageValues = aikct_language.getValue(); 
                                     var nonce = $('#save_api_keys_nonce').val(); // Lấy nonce từ form

                                    $.ajax({
                                        url: ajaxurl, // Sử dụng 'ajaxurl' do WordPress cung cấp
                                        type: 'POST',
                                        data: {
                                            action: 'save_api_keys',
                                            apiKeyGPT35: apiKeyGPT35,
                                            apiKeyGPT4: apiKeyGPT4,
                                            apiKeyGemini: apiKeyGemini,
                                            apiKeyCloudflareToken: apiKeyCloudflareToken,
                                            apiKeyCloudflareAccountID: apiKeyCloudflareAccountID,
                                            apiKeyHuggingface: apiKeyHuggingface,
                                            save_api_keys_nonce: nonce,
                                            language:languageValues
                                        },
                                        success: function(response) {
                                            
                                            if (response.success) {
                                                $('.aikct_box_loading').addClass('hidden').removeClass('show');
                                                aikct_mess_success('API keys have been saved successfully.');
                                            } else {
                                                aikct_mess_error('Error saving API keys.');
                                            }
                                        },
                                        error: function() {
                                            aikct_mess_error('Error saving API keys.');
                                        }
                                    });
                                });
                            });

</script>
                    </div>
                </div>


                <div id="tab2" class="aikct_box_tabcontent">
                    <div class="aikct_box_content">

                        
                            <!-- Button -->
                        <div class="aikct_box_button_wrapper">

                            <?php 
        echo loader( '0' );
        ?>
                              
                            <?php 
        wp_nonce_field( 'get_free_api_key_action', 'get_free_api_key_nonce' );
        ?>

                            <button id="getApiKeyButton" class="aikct_box_button"><i class="fa-solid fa-download"></i>
                                <?php 
        if ( class_exists( 'gpt35' ) && class_exists( 'gpt4o' ) ) {
            echo esc_html_e( 'Disable API key Free', 'ai-kct' );
        } else {
            echo esc_html_e( 'Get API key Free', 'ai-kct' );
        }
        ?>
                            </button>
                        </div>

                        

                        <!-- Success Message -->
                        <div id="successMessage" class="aikct_box_success_message" style="display: none;">
                            API key has been updated successfully!
                        </div>

                        <script type="text/javascript">
jQuery(document).ready(function($) {
    $('#getApiKeyButton').on('click', function() {
        $('#successMessage').hide();
        $('.aikct_box_loading').addClass('show').removeClass('hidden');

        var nonce = $('#get_free_api_key_nonce').val(); 

        $.ajax({
            url: ajaxurl, 
            type: 'POST',
            data: {
                action: 'get_free_api_key',
                get_free_api_key_nonce: nonce 
            },
            success: function(response) {
                $('.aikct_box_loading').addClass('hidden').removeClass('show');
                if (response.success) {
                    $('#successMessage').show();
                } else {
                    console.error('Error fetching API key.');
                }
            },
            error: function() {
                $('.aikct_box_loading').addClass('hidden').removeClass('show');
                console.error('Error fetching API key.');
            }
        });
    });
});
</script>

                            
                        
                    </div>
                  
                </div>


                <div id="tab3" class="aikct_box_tabcontent">
                    <div class="aikct_box_content aikct_box">

                        <h2><i class="fa-solid fa-cat"></i> Welcome to AI KCT Engine</h2>
                         <?php 
        $site_url = get_site_url();
        $site_language = get_option( 'WPLANG', 'en_US' );
        $active_plugins = get_option( 'active_plugins' );
        $plugin_count = count( $active_plugins );
        $plugin_names = array();
        foreach ( $active_plugins as $plugin ) {
            $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
            $plugin_names[] = $plugin_data['Name'];
        }
        $current_theme = wp_get_theme();
        $theme_name = $current_theme->get( 'Name' );
        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;
        $user_role = ( !empty( $user_roles ) ? ucfirst( $user_roles[0] ) : 'Subscriber' );
        ?>
                            
                            <ul>
                                <li><strong>Domain:</strong> <?php 
        echo esc_html( $site_url );
        ?></li>
                                <li><strong>Site Language:</strong> <?php 
        echo esc_html( $site_language );
        ?></li>
                                <li><strong>Active Plugins:</strong> <?php 
        echo esc_html( $plugin_count );
        ?></li>
                                <li><strong>Plugin Names:</strong>
                                    <ul>
                                        <?php 
        foreach ( $plugin_names as $name ) {
            ?>
                                            <li><?php 
            echo esc_html( $name );
            ?></li>
                                        <?php 
        }
        ?>
                                    </ul>
                                </li>
                                <li><strong>Theme Name:</strong> <?php 
        echo esc_html( $theme_name );
        ?></li>
                                <li><strong>Account Type:</strong> <?php 
        echo esc_html( $user_role );
        ?></li>
                            </ul>
                        <p>
                            The <strong>AI KCT Engine</strong> is a cutting-edge WordPress plugin designed to integrate with advanced AI technologies, including ChatGPT, Gemini, and GPT-4o. This plugin offers a powerful suite of tools to enhance your WordPress site with intelligent chatbot capabilities and AI-driven content suggestions.
                        </p>
                        <p>
                            Developed by <strong>KCT</strong>, the AI KCT Engine aims to provide seamless and efficient solutions for leveraging AI within WordPress. Our goal is to help users create engaging and interactive experiences by harnessing the power of the latest AI advancements.
                        </p>
                        <p>
                            Whether you're looking to enhance your site's conversational abilities or streamline content creation with the latest AI models, the AI KCT Engine is the ultimate tool for elevating your WordPress experience.
                        </p>

                        
                    </div>
                </div>
               
            </div>
        </div>
         <script>
        function aikcttab(evt, tabName) {
            var i, tabcontent, tablinks;

            tabcontent = document.getElementsByClassName("aikct_box_tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
                tabcontent[i].classList.remove("active");
            }

            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            document.getElementById(tabName).style.display = "block";
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.className += " active";
        }
    </script>
        <?php 
    }

}

new aikct_suggest_content();
require_once dirname( __FILE__ ) . '/admin/aikct-prompt-manager.php';