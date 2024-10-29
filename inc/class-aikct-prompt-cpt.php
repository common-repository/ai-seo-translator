<?php
defined('ABSPATH') or die();
class AIKCT_Prompt_CPT {
    public static function register_prompt_cpt() {
        $labels = [
            'name'               => __('Prompts', 'ai-seo-translator'),
            'singular_name'      => __('Prompt', 'ai-seo-translator'),
            'menu_name'          => __('Prompts', 'ai-seo-translator'),
            'name_admin_bar'     => __('Prompt', 'ai-seo-translator'),
            'add_new'            => __('Add New', 'ai-seo-translator'),
            'add_new_item'       => __('Add New Prompt', 'ai-seo-translator'),
            'new_item'           => __('New Prompt', 'ai-seo-translator'),
            'edit_item'          => __('Edit Prompt', 'ai-seo-translator'),
            'view_item'          => __('View Prompt', 'ai-seo-translator'),
            'all_items'          => __('All Prompts', 'ai-seo-translator'),
            'search_items'       => __('Search Prompts', 'ai-seo-translator'),
            'not_found'          => __('No Prompts found.', 'ai-seo-translator'),
            'not_found_in_trash' => __('No Prompts found in Trash.', 'ai-seo-translator'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'kctai-settings1',
            'query_var'          => true,
            'rewrite'            => ['slug' => 'prompt'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 'kctai-settings',
            'supports'           => ['title', 'editor'],
        ];

        register_post_type('aikct_prompt', $args);
    }
    public static function add_prompt_to_menu() {
        add_submenu_page(
            'kctai-settings',             
            __('Prompts Manager', 'ai-seo-translator'), 
            __('Prompts Manager', 'ai-seo-translator'), 
            'manage_options',            
            'edit.php?post_type=aikct_prompt' 
        );
    }
}

// Khởi tạo custom post type
add_action('init', ['AIKCT_Prompt_CPT', 'register_prompt_cpt']);


add_action('admin_menu', ['AIKCT_Prompt_CPT', 'add_prompt_to_menu']);


class AIKCT_Prompt_Metabox {

    public static function add_metabox() {
        add_meta_box(
            'aikct_prompt_params',
            __('Prompt Parameters', 'ai-seo-translator'),
            [self::class, 'render_metabox'],
            'aikct_prompt',
            'normal',
            'high'
        );
    }

    public static function render_metabox($post) {
        wp_nonce_field('aikct_prompt_save_meta', 'aikct_prompt_meta_nonce');

        $params = get_post_meta($post->ID, '_aikct_prompt_params', true);
        
        echo '<div id="aikct-prompt-params-wrap">';
        if (!empty($params)) {
            foreach ($params as $param) {
                echo '<div class="aikct-prompt-param">';
                echo '<label>' . esc_html($param['label']) . '</label>';
                echo '<input type="text" name="aikct_prompt_params[]" value="' . esc_attr($param['value']) . '" />';
                echo '<select name="aikct_prompt_param_types[]">
                        <option value="input_text"' . selected($param['type'], 'input_text', false) . '>Input Text</option>
                        <option value="textarea"' . selected($param['type'], 'textarea', false) . '>Textarea</option>
                      </select><button type="button" class="aikct-remove-param button">Remove</button>';
                echo '</div>';
            }
        }
        echo '</div>';
        echo '<button type="button" id="aikct-add-param" class="button">' . esc_html(__('Add Parameter', 'ai-seo-translator')) . '</button>';
        ?>
        <script type="text/javascript">jQuery(document).ready(function ($) {
    let paramIndex = 0;

    $('#aikct-add-param').on('click', function () {
        paramIndex++;
        const newParam = `
            <div class="aikct-prompt-param">
                <label>Code : %code%</label>
                <input type="text" name="aikct_prompt_params[]" value="" placeholder="%title%" />
                <select name="aikct_prompt_param_types[]">
                    <option value="input_text">Input Text</option>
                    <option value="textarea">Textarea</option>
                </select>
                <button type="button" class="aikct-remove-param button">Remove</button>
            </div>
        `;
        $('#aikct-prompt-params-wrap').append(newParam);
    });

    $(document).on('click', '.aikct-remove-param', function () {
        $(this).closest('.aikct-prompt-param').remove();
    });
});
</script><?php
    }


    public static function save_metabox($post_id) {
       if (isset($_POST['aikct_prompt_meta_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['aikct_prompt_meta_nonce'])), 'aikct_prompt_save_meta')) {
            
           
            if (isset($_POST['post_type']) && 'aikct_prompt' !== sanitize_text_field(wp_unslash($_POST['post_type']))) {
               
                return;
            }
            

            if (isset($_POST['aikct_prompt_params']) && isset($_POST['aikct_prompt_param_types'])) {
                
                if (is_array($_POST['aikct_prompt_params']) && is_array($_POST['aikct_prompt_param_types'])) {
                    $params = array();
                    $param_values = array_map('sanitize_text_field', wp_unslash($_POST['aikct_prompt_params']));
                    $param_types = array_map('sanitize_text_field', wp_unslash($_POST['aikct_prompt_param_types']));

                    
                    if (count($param_values) === count($param_types)) {
                        foreach ($param_values as $index => $value) {
                            $params[] = array(
                                'label' => $value,
                                'value' => $value,
                                'type' => isset($param_types[$index]) ? $param_types[$index] : '',
                            );
                        }

                        update_post_meta($post_id, '_aikct_prompt_params', $params);
                    } else {
                        
                    }
                } else {
                   
                }
            } else {
                
                delete_post_meta($post_id, '_aikct_prompt_params');
            }

                
            
        } else {
           
            return;
        }
    }

}

// Khởi tạo Meta Box
add_action('add_meta_boxes', ['AIKCT_Prompt_Metabox', 'add_metabox']);
add_action('save_post', ['AIKCT_Prompt_Metabox', 'save_metabox']);


class AIKCT_Prompt_Render {

    public static function render_prompt_form($post_id) {
        $params = get_post_meta($post_id, '_aikct_prompt_params', true);

        if (!empty($params)) {
            echo '<form id="aikct-prompt-form">';
            foreach ($params as $param) {
                echo '<label>' . esc_html($param['label']) . '</label>';
                switch ($param['type']) {
                    case 'input_text':
                        echo '<input type="text" name="' . esc_attr($param['label']) . '" />';
                        break;
                    case 'selectbox':
                        echo '<select name="' . esc_attr($param['label']) . '">';
                        echo '<option value="">' . esc_html(__('Select an option', 'ai-seo-translator')) . '</option>';
                        echo '</select>';
                        break;
                    case 'combobox':
                        echo '<input type="text" name="' . esc_attr($param['label']) . '" list="aikct-' . esc_attr($param['label']) . '-options" />';
                        echo '<datalist id="aikct-' . esc_attr($param['label']) . '-options">';
                        echo '</datalist>';
                        break;
                    case 'textarea':
                        echo '<textarea name="' . esc_attr($param['label']) . '"></textarea>';
                        break;
                }
            }
            echo '<button type="submit">' . esc_html(__('Submit', 'ai-seo-translator')) . '</button>';
            echo '</form>';
        }
    }
}

