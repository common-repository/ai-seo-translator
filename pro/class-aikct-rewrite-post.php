<?php

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
if ( !class_exists( 'Aikct_Rewrite_post_List_Table' ) ) {
    class Aikct_Rewrite_post_List_Table extends WP_List_Table {
        public function __construct() {
            parent::__construct( array(
                'singular' => __( 'Rewrite Post', 'ai-seo-tranlators' ),
                'plural'   => __( 'Rewrite Posts', 'ai-seo-tranlators' ),
                'ajax'     => false,
            ) );
        }

        public function prepare_items() {
            $per_page = 10;
            $current_page = $this->get_pagenum();
            $all_items = $this->get_items();
            $total_items = count( $all_items );
            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page ),
            ) );
            $this->items = array_slice( $all_items, ($current_page - 1) * $per_page, $per_page );
        }

        public function get_column_info() {
            $columns = [
                'stt'     => 'STT',
                'url'     => 'URL',
                'prompt'  => 'Prompt',
                'status'  => 'Status',
                'date_in' => 'Date',
                'actions' => 'Actions',
            ];
            $hidden = [];
            $sortable = [];
            return [$columns, $hidden, $sortable];
        }

        public function get_items() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'aikct_rewrite_post';
            // Retrieve data from the custom table
            $query = "SELECT * FROM {$table_name}";
            $results = $wpdb->get_results( $query, ARRAY_A );
            $data = [];
            foreach ( $results as $index => $item ) {
                $data[] = [
                    'stt'     => $index + 1,
                    'url'     => ( isset( $item['url'] ) ? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $item['url'] ), esc_url( $item['url'] ) ) : '' ),
                    'prompt'  => ( isset( $item['prompt'] ) ? esc_html( $item['prompt'] ) : '' ),
                    'status'  => ( isset( $item['status'] ) ? ( $item['status'] == 0 ? '<i class="fas fa-clock" style="color: orange;"></i> Pending' : '<i class="fas fa-check-circle" style="color: green;"></i> Completed' ) : 'Unknown' ),
                    'date_in' => ( isset( $item['date_in'] ) ? esc_html( $item['date_in'] ) : '' ),
                    'actions' => sprintf( '<a href="#" class="delete-item" data-id="%s" data-nonce="%s">Delete</a>', esc_attr( $item['id'] ), wp_create_nonce( 'delete_url_prompt_nonce' ) ),
                ];
            }
            return $data;
        }

        public function column_default( $item, $column_name ) {
            switch ( $column_name ) {
                case 'stt':
                    return $item['stt'];
                case 'url':
                case 'prompt':
                case 'status':
                case 'date_in':
                    return $item[$column_name];
                case 'actions':
                    return $item[$column_name];
                default:
                    return print_r( $item, true );
            }
        }

    }

}
class aikct_rewrite_post extends aikct {
    private $option_name = 'aikct_rewrite_post_options';

    private $cron_hook = 'aikct_rewrite_post_cron_hook';

    public $settings;

    public function __construct() {
        $this->settings = [
            'title'      => 'Rewrite content old post',
            'desc'       => 'Rewrite/replace content old post',
            'key'        => 'aikct_rewrite_content_old_post',
            'is_enabled' => get_option( 'aikct_rewrite_content_old_post' ) == 1,
        ];
    }

    public function add_custom_cron_interval( $schedules ) {
    }

    public function schedule_cron() {
    }

    public function run_cron_job() {
    }

    public function register_settings_page() {
    }

    public function register_settings() {
        register_setting( $this->option_name, 'aikct_rewrite_post_cron_time' );
    }

    public function render_settings_page() {
    }

}

add_action( 'wp_ajax_get_prompt_content', 'get_prompt_content' );
function get_prompt_content() {
    $arr_replace_prompt = [
        '%TITLE%',
        '%CONTENT%',
        '%AUTHOR%',
        '%CATEGORIES_NAME%',
        '%TAGS_NAME%'
    ];
    // Check if the required data is passed
    if ( !isset( $_POST['random_url'] ) || empty( $_POST['random_url'] ) ) {
        wp_send_json_error( array(
            'message' => 'URL not provided.',
        ) );
        return;
    }
    $random_url = esc_url_raw( $_POST['random_url'] );
    $post_id = url_to_postid( $random_url );
    if ( !$post_id ) {
        wp_send_json_error( array(
            'message' => 'Post ID does not exist for the provided URL.',
        ) );
        return;
    }
    $post = get_post( $post_id );
    if ( !$post || $post->post_status !== 'publish' ) {
        wp_send_json_error( array(
            'message' => 'Post not found or is not published.',
        ) );
        return;
    }
    $categories = get_the_category( $post_id );
    $category_names = wp_list_pluck( $categories, 'name' );
    $tags = get_the_tags( $post_id );
    $tag_names = ( $tags ? wp_list_pluck( $tags, 'name' ) : [] );
    $replace_values = [
        $post->post_title,
        // %TITLE%
        $post->post_content,
        // %CONTENT%
        get_the_author_meta( 'display_name', $post->post_author ),
        // %AUTHOR%
        implode( ', ', $category_names ),
        // %CATEGORIES_NAME%
        implode( ', ', $tag_names ),
    ];
    $prompt_content = ( isset( $_POST['prompt_content'] ) ? sanitize_textarea_field( $_POST['prompt_content'] ) : '' );
    $replaced_prompt_content = str_replace( $arr_replace_prompt, $replace_values, $prompt_content );
    $proxy = new kctaiproxy();
    if ( $proxy->status ) {
        $result = $proxy->sendRequest( $replaced_prompt_content );
        $content_new = $result['output'];
    } else {
        $content_new = 'Please set API KEY';
    }
    wp_send_json_success( array(
        'post_id'                 => $post_id,
        'post_title'              => $post->post_title,
        'post_content'            => $post->post_content,
        'post_content_new'        => $content_new,
        'replaced_prompt_content' => $replaced_prompt_content,
    ) );
}

add_action( 'wp_ajax_load_customer_prompt_properties', 'load_customer_prompt_properties' );
function load_customer_prompt_properties() {
    $arr_replace_prompt = [
        '%TITLE%',
        '%CONTENT%',
        '%AUTHOR%',
        '%CATEGORIES_NAME%',
        '%TAGS_NAME%'
    ];
    $prompt_id = ( isset( $_POST['prompt_id'] ) ? sanitize_text_field( $_POST['prompt_id'] ) : '' );
    $postprompt = get_post( $prompt_id );
    if ( !empty( $prompt_id ) ) {
        $params = get_post_meta( $prompt_id, '_aikct_prompt_params', true );
        $arr = array();
        $arr['prompt_content'] = $postprompt->post_content;
        $arr['prompt_params'] = $params;
        $arr['prompt_arr_replace'] = $arr_replace_prompt;
        echo wp_send_json_success( $arr );
        wp_die();
    }
    wp_die( 'Invalid prompt ID' );
}

add_action( 'wp_ajax_save_url_prompts', 'save_url_prompts' );
function save_url_prompts() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aikct_rewrite_post';
    if ( !isset( $_POST['items'] ) || !is_array( $_POST['items'] ) ) {
        wp_send_json_error( array(
            'message' => 'Invalid data.',
        ) );
        return;
    }
    $url_prompt_map = $_POST['items'];
    foreach ( $url_prompt_map as $new_item ) {
        if ( isset( $new_item['url'] ) ) {
            $url = $new_item['url'];
            $data = array(
                'url'     => $url,
                'prompt'  => ( isset( $new_item['prompt'] ) ? $new_item['prompt'] : '' ),
                'status'  => ( isset( $new_item['status'] ) ? ( $new_item['status'] == 'true' ? 1 : 0 ) : 0 ),
                'date_in' => ( isset( $new_item['date_in'] ) ? $new_item['date_in'] : current_time( 'mysql' ) ),
            );
            $format = array(
                '%s',
                '%s',
                '%d',
                '%s'
            );
            $existing_row = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$table_name} WHERE url = %s", $url ) );
            if ( $existing_row ) {
                $wpdb->update(
                    $table_name,
                    $data,
                    array(
                        'url' => $url,
                    ),
                    $format
                );
            } else {
                $wpdb->insert( $table_name, $data, $format );
            }
        }
    }
    wp_send_json_success( array(
        'message' => 'Data saved successfully.',
    ) );
}

add_action( 'wp_ajax_delete_url_prompt', 'delete_url_prompt' );
function delete_url_prompt() {
    if ( !isset( $_GET['_wpnonce'] ) || !wp_verify_nonce( $_GET['_wpnonce'], 'delete_url_prompt_nonce' ) ) {
        wp_send_json_error( array(
            'message' => 'Nonce verification failed.',
        ) );
        return;
    }
    if ( !isset( $_GET['id'] ) ) {
        wp_send_json_error( array(
            'message' => 'No ID provided.',
        ) );
        return;
    }
    global $wpdb;
    $table_name = $wpdb->prefix . 'aikct_rewrite_post';
    $id = intval( $_GET['id'] );
    $result = $wpdb->delete( $table_name, [
        'id' => $id,
    ], ['%d'] );
    if ( $result !== false ) {
        wp_send_json_success( array(
            'message' => 'Item deleted successfully.',
        ) );
    } else {
        wp_send_json_error( array(
            'message' => 'Failed to delete item.',
        ) );
    }
}

add_action( 'init', 'aikct_rewrite_post_check_and_create_table' );
function aikct_rewrite_post_check_and_create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aikct_rewrite_post';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                url VARCHAR(255) NOT NULL,
                prompt TEXT NOT NULL,
                status BOOLEAN NOT NULL DEFAULT 0,
                date_in DATETIME NOT NULL,
                UNIQUE KEY url (url)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
}
