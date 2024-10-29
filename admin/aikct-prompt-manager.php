<?php 
function aikct_get_prompt_params() {
    // Verify nonce and check user capabilities
    if (!isset($_POST['aikct_nonce']) || !wp_verify_nonce(sanitize_key($_POST['aikct_nonce']), 'aikct_get_prompt_params_nonce') || !current_user_can('edit_posts')) {
        wp_send_json_error('Invalid request');
    }

    // Sanitize and validate the post ID
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        wp_send_json_error('Invalid post ID');
    }
    
    $post_id = intval($_POST['post_id']);

    // Get post meta and content
    $params = get_post_meta($post_id, '_aikct_prompt_params', true);
    $content = get_post_field('post_content', $post_id);

    if (!$params || !$content) {
        $params = [];
    }

    // Add options property if parameter is selectbox or combobox
    foreach ($params as &$param) {
        if ($param['type'] === 'selectbox' || $param['type'] === 'combobox') {
            $param['options'] = isset($param['options']) ? $param['options'] : [];
        }
    }

    wp_send_json_success(array('params' => $params, 'content' => $content));
}

add_action('wp_ajax_aikct_get_prompt_params', 'aikct_get_prompt_params');



 ?>