<?php
if ( ast_fs()->is_plan( 'aikct_pro' ) ) {
    if (!class_exists('WP_List_Table')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
    }


    if (!class_exists('Aikct_Rewrite_post_List_Table')) {
        class Aikct_Rewrite_post_List_Table extends WP_List_Table {
            public function __construct() {
                parent::__construct(array(
                    'singular' => __('Rewrite Post', 'ai-seo-tranlators'),
                    'plural'   => __('Rewrite Posts', 'ai-seo-tranlators'),
                    'ajax'     => false
                ));
            }
            public function prepare_items() {
                $per_page = 10; 
                $current_page = $this->get_pagenum(); 

                $all_items = $this->get_items(); 
                $total_items = count($all_items); 

                $this->set_pagination_args(array(
                    'total_items' => $total_items, 
                    'per_page'    => $per_page,    
                    'total_pages' => ceil($total_items / $per_page) 
                ));

                $this->items = array_slice($all_items, (($current_page - 1) * $per_page), $per_page);
               
            }

            public function get_column_info() {
                $columns = [
                    'stt'       => 'STT',
                    'url'       => 'URL',
                    'prompt'    => 'Prompt',
                    'status'    => 'Status',
                    'date_in'   => 'Date',
                    'actions'   => 'Actions'
                ];
                
                $hidden = [];
                $sortable = [];

                return [$columns, $hidden, $sortable];
            }

            public function get_items() {
                global $wpdb;
                $table_name = $wpdb->prefix . 'aikct_rewrite_post';

                // Retrieve data from the custom table
                $query = "SELECT * FROM $table_name";
                $results = $wpdb->get_results($query, ARRAY_A);

                $data = [];
                foreach ($results as $index => $item) {
                    $data[] = [
                        'stt'       => $index + 1,
                        'url'       => isset($item['url']) ? sprintf('<a href="%s" target="_blank">%s</a>', esc_url($item['url']), esc_url($item['url'])) : '',
                        'prompt'    => isset($item['prompt']) ? esc_html($item['prompt']) : '',
                        'status'    => isset($item['status']) ? 
                                        ($item['status'] == 0 ? 
                                            '<i class="fas fa-clock" style="color: orange;"></i> Pending' : 
                                            '<i class="fas fa-check-circle" style="color: green;"></i> Completed') : 
                                        'Unknown',
                        'date_in'   => isset($item['date_in']) ? esc_html($item['date_in']) : '',
                        'actions'   => sprintf(
                            '<a href="#" class="delete-item" data-id="%s" data-nonce="%s">Delete</a>',
                            esc_attr($item['id']),
                            wp_create_nonce('delete_url_prompt_nonce')
                        )
                    ];
                }

                return $data;
            }

            public function column_default($item, $column_name) {
                switch ($column_name) {
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
                        return print_r($item, true);
                }
            }
        }
    }


    class aikct_rewrite_post {

        private $option_name = 'aikct_rewrite_post_options';
        private $cron_hook = 'aikct_rewrite_post_cron_hook';

        public function __construct() {
            // Hook to register settings page
            add_action('admin_menu', array($this, 'register_settings_page'));

            add_action('admin_init', array($this, 'register_settings'));
           

            add_action('init', array($this, 'schedule_cron'));
            add_action($this->cron_hook, array($this, 'run_cron_job'));
            add_filter('cron_schedules', array($this, 'add_custom_cron_interval'));


        }

        public function add_custom_cron_interval($schedules) {
            // Lấy giá trị đã lưu từ wp_options (mặc định là 60 phút nếu chưa có giá trị)
            $cron_time = get_option('aikct_rewrite_post_cron_time', 60);
            
            // Tính toán thời gian cron theo giây
            $interval_seconds = intval($cron_time) * 60;

            // Thêm vào cron schedule
            $schedules['aikct_rewrite_post_interval'] = array(
                'interval' => $interval_seconds,
                'display' => sprintf(__('Every %d Minutes', 'aikct_rewrite_post'), $cron_time)
            );
            return $schedules;
        }

        public function schedule_cron() {
            // Lấy giá trị thời gian từ wp_options (mặc định 60 phút)
            $cron_time = get_option('aikct_rewrite_post_cron_time', 60);

            // Kiểm tra xem cron job đã được lên lịch hay chưa
            if (!wp_next_scheduled($this->cron_hook)) {
                wp_schedule_event(time(), 'aikct_rewrite_post_interval', $this->cron_hook);
            }
        }

        public function run_cron_job() {
            global $wpdb;

            // Tên bảng của bạn
            $table_name = $wpdb->prefix . 'aikct_rewrite_post';

            // Lấy item đầu tiên có status = 0 từ bảng cơ sở dữ liệu
            $item = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table_name WHERE status = %d ORDER BY id ASC LIMIT 1", 0),
                ARRAY_A
            );

            // Kiểm tra nếu không có item nào
            if (empty($item)) {
                return; // Kết thúc nếu không có item để xử lý
            }

           

            // Lấy URL từ item
            $random_url = esc_url_raw($item['url']);
            $post_id = url_to_postid($random_url);

            // Kiểm tra post ID có hợp lệ không
            if (!$post_id) {
                return; // Kết thúc nếu không tìm thấy bài viết với URL này
            }

            // Lấy thông tin bài viết
            $post = get_post($post_id);
            if (!$post || $post->post_status !== 'publish') {
                return; // Kết thúc nếu bài viết không hợp lệ hoặc không được xuất bản
            }

            // Lấy categories và tags của bài viết
            $categories = get_the_category($post_id);
            $category_names = wp_list_pluck($categories, 'name');
            $tags = get_the_tags($post_id);
            $tag_names = $tags ? wp_list_pluck($tags, 'name') : [];

            // Tạo mảng thay thế cho các placeholder
            $arr_replace_prompt = [
                '%TITLE%',
                '%CONTENT%',
                '%AUTHOR%',
                '%CATEGORIES_NAME%',
                '%TAGS_NAME%'
            ];

            // Các giá trị cần thay thế trong prompt
            $replace_values = [
                $post->post_title,                               // %TITLE%
                $post->post_content,                             // %CONTENT%
                get_the_author_meta('display_name', $post->post_author), // %AUTHOR%
                implode(', ', $category_names),                 // %CATEGORIES_NAME%
                implode(', ', $tag_names)                       // %TAGS_NAME%
            ];

            // Lấy prompt từ item
            $prompt_content = isset($item['prompt']) ? sanitize_textarea_field($item['prompt']) : '';

            // Thay thế các placeholder trong prompt
            $replaced_prompt_content = str_replace($arr_replace_prompt, $replace_values, $prompt_content);

            // Gửi nội dung đã thay thế tới API qua proxy
            $proxy = new kctaiproxy();
            if ($proxy->status) {
                $result = $proxy->sendRequest($replaced_prompt_content);
                $content_new = $result['output'];
            } else {
                $content_new = 'Please set API KEY';
            }

            if ($content_new !== 'Please set API KEY') {
                // Cập nhật nội dung bài viết và trạng thái item
                update_post_meta($post_id, '_old_content', $post->post_content);

                wp_update_post([
                    'ID'           => $post_id,
                    'post_content' => wp_kses_post($content_new), 
                ]);
                 print_r($item);
                // Cập nhật trạng thái và ngày vào cơ sở dữ liệu
                $wpdb->update(
                    $table_name,
                    [
                        'status'  => 1,
                        'date_in' => current_time('mysql')
                    ],
                    ['id' => $item['id']], // Điều kiện để xác định bản ghi cần cập nhật
                    ['%s', '%s'],
                    ['%d']
                );
            }
        }



        
        public function register_settings_page() {
            add_submenu_page(
                'kctai-settings',
                'Rewrite Post Settings',
                'Rewrite Post Settings',
                'manage_options',
                'aikct_rewrite_post',
                array($this, 'render_settings_page')
            );
        }

        public function register_settings() {
            register_setting($this->option_name, 'aikct_rewrite_post_cron_time');

            
            
        }
       
        public function render_settings_page() {
            if (isset($_POST['save_rewrite_keys_nonce']) && wp_verify_nonce($_POST['save_rewrite_keys_nonce'], 'save_rewrite_keys_action')) {
                if (isset($_POST['timerun'])) {
                    update_option('aikct_rewrite_post_cron_time', sanitize_text_field($_POST['timerun']));
                }
            }

            $selected_timerun = get_option('aikct_rewrite_post_cron_time', '1');
            ?>
            <div class="wrap">
                <h2></h2>
                <div class="aikct_box_container">
                    <div class="aikct_box_header">
                        
                        <div class="aikct_box_header">
                        <h2 class="icon-text"><svg width="24" height="24" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H5Zm6.8 11.5.5 1.2a68.3 68.3 0 0 0 .7 1.1l.4.1c.3 0 .5 0 .7-.3.2-.1.3-.3.3-.6l-.3-1-2.6-6.2a20.4 20.4 0 0 0-.5-1.3l-.5-.4-.7-.2c-.2 0-.5 0-.6.2-.2 0-.4.2-.5.4l-.3.6-.3.7L5.7 15l-.2.6-.1.4c0 .3 0 .5.3.7l.6.2c.3 0 .5 0 .7-.2l.4-1 .5-1.2h3.9ZM9.8 9l1.5 4h-3l1.5-4Zm5.6-.9v7.6c0 .4 0 .7.2 1l.7.2c.3 0 .6 0 .8-.3l.2-.9V8.1c0-.4 0-.7-.2-.9a1 1 0 0 0-.8-.3c-.2 0-.5.1-.7.3l-.2 1Z"></path></svg> Rewrite Post Settings</h2>
                    </div>
                         
                    </div>
                    <div class="aikct_box_tab">
                        <button class="tablink active" onclick="aikcttab(event, 'process')"><i class="fa-solid fa-key"></i> View Rewrite Post Proccess</button>
                        
                        <button class="tablink" onclick="aikcttab(event, 'rewrite')"><i class="fa-solid fa-unlock-keyhole"></i> Set Rewrite Post</button>
                        <button class="tablink " onclick="aikcttab(event, 'setting')"><i class="fa-solid fa-key"></i> Config Crontab Rewrite Post</button>
                    </div>
                      <div id="process" class="aikct_box_tabcontent active">

                    <?php 
                    $list_table = new Aikct_Rewrite_post_List_Table();
        $list_table->prepare_items();
        $list_table->display();
     ?>
     <script type="text/javascript">
         jQuery(document).ready(function($) {
        // Xử lý sự kiện nhấp vào nút Delete
        $(document).on('click', '.delete-item', function(e) {
            e.preventDefault();
            
            var $this = $(this);
            var itemId = $this.data('id');
            var nonce = $this.data('nonce');

            // Xác nhận trước khi xóa
            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

            $.ajax({
                url: ajaxurl, // URL cho Ajax (WordPress tự động cung cấp biến này)
                type: 'GET',
                data: {
                    action: 'delete_url_prompt',
                    id: itemId,
                    _wpnonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Xóa hàng khỏi bảng
                        $this.closest('tr').remove();
                        alert('Item deleted successfully.');
                    } else {
                        alert('Failed to delete item: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('An error occurred while processing the request.');
                }
            });
        });
    });

     </script>
        </div>
                    <div id="setting" class="aikct_box_tabcontent ">
                        <form method="post" action="">
                            <?php settings_fields($this->option_name); ?>
                            <?php wp_nonce_field('save_rewrite_keys_action', 'save_rewrite_keys_nonce'); ?>
                            <div class="aikct_box">
                                <h3>Config time run crontab</h3>
                                <label for="timerun"><i class="fa-regular fa-circle-check"></i> Time run crontab</label>
                                <select id="timerun" name="timerun">
                                    <option value="1" <?php selected($selected_timerun, '1'); ?>>1 Minute</option>
                                    <option value="2" <?php selected($selected_timerun, '2'); ?>>2 Minute</option>
                                    <option value="3" <?php selected($selected_timerun, '3'); ?>>3 Minute</option>
                                    <option value="5" <?php selected($selected_timerun, '5'); ?>>5 Minute</option>
                                    <option value="15" <?php selected($selected_timerun, '15'); ?>>15 Minute</option>
                                    <option value="30" <?php selected($selected_timerun, '30'); ?>>30 Minute</option>
                                    <option value="60" <?php selected($selected_timerun, '60'); ?>>60 Minute</option>
                                    <option value="120" <?php selected($selected_timerun, '120'); ?>>120 Minute</option>
                                    <option value="720" <?php selected($selected_timerun, '720'); ?>>12 Hour</option>
                                    <option value="1440" <?php selected($selected_timerun, '1440'); ?>>24 Hour</option>
                                </select>
                            </div>

                            <button class="aikct_box_save-btn" id="save">
                                <i class="aikct_box_icon"><img draggable="false" role="img" class="emoji" alt="💾" src="https://s.w.org/images/core/emoji/15.0.3/svg/1f4be.svg"></i> 
                                Save 
                            </button>
                        </form>
                          
                     </div>
                     <div id="rewrite" class="aikct_box_tabcontent ">
                         <form method="post" action="">
                                 <div class="aikct_box">
                                     <!-- Textarea chứa danh sách URL -->
                                        <label for="url_list"><i class="fa-regular fa-list-alt"></i> List of URLs need rewrite content:</label>
                                        <textarea id="url_list" name="url_list" class="aikct_input" rows="5" placeholder="Enter URLs separated by a new line"></textarea>


                                        <label for="aikct_prompt"><i class="fa-regular fa-list-alt"></i> Prompt :</label>
                                        <input type="hidden" id="original_prompt_content" value="">

                                        <textarea id="aikct_prompt" name="aikct_prompt" class="aikct_input" rows="5" placeholder="Enter Prompt"></textarea>

                                        <label for="customer_prompt"><i class="fa-solid fa-key"></i> Select Your Prompts in List:</label>
                                        <select id="customer_prompt" name="customer_prompt">
                                            <option value="">Select Prompt</option>
                                            <?php
                                            $args = array(
                                                'post_type' => 'aikct_prompt',
                                                'posts_per_page' => -1,
                                                'post_status' => 'publish',
                                            );
                                            $prompts = new WP_Query($args);
                                            if ($prompts->have_posts()) {
                                                while ($prompts->have_posts()) {
                                                    $prompts->the_post();
                                                    ?>
                                                    <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
                                                    <?php
                                                }
                                                wp_reset_postdata();
                                            }
                                            ?>
                                        </select>
                                        <?php  
                                         echo '<a href="' . esc_url(admin_url('post-new.php?post_type=aikct_prompt')) . '" class="aikct_box_test-btn">' . esc_html(__('Add Prompt','ai-seo-translator')) . '</a>';
                                         ?>
                                         <div id="prompt_properties"></div>
                                        
                                        <button type="button" id="test_prompt" class="aikct_box_test-btn">
                                            <i class="fa-solid fa-play"></i> Test Prompt
                                        </button>

                                        <script type="text/javascript">
                                            jQuery(document).ready(function($) {
        
        $('#customer_prompt').change(function() {
            var selectedPrompt = $(this).val(); 

            
            if (selectedPrompt) {
                $.ajax({
                    url: ajaxurl, 
                    type: 'POST',
                    data: {
                        action: 'load_customer_prompt_properties',
                        prompt_id: selectedPrompt
                    },
                    success: function(response) {
                        if(response.success){
                            var data = response.data;
                            console.log(data.prompt_content);
                            $('#aikct_prompt').val(data.prompt_content);
                            $('#original_prompt_content').val(data.prompt_content);
                             $('#prompt_properties').empty();
                             if (data.prompt_params.length > 0) {
                                // Add header
                                $('#prompt_properties').append('<h3>Config Parameter replace with prompt</h3>');

                                // Loop through each parameter and create form elements
                                data.prompt_params.forEach(function(param) {
                                    var paramLabel = $('<label></label>').text(param.label + ' replace by');
                                    $('#prompt_properties').append(paramLabel);

                                    // Create the select element
                                    var selectElement = $('<select></select>')
                                        .addClass('aikct_rewrite_post_pro')
                                        .attr('data-name', param.label)
                                        .attr('name', param.label);

                                    // Populate the options with prompt_arr_replace values
                                    data.prompt_arr_replace.forEach(function(replaceOption) {
                                        var optionElement = $('<option></option>')
                                            .attr('value', replaceOption)
                                            .text(replaceOption);
                                        selectElement.append(optionElement);
                                    });

                                    // Append the select element to the container
                                    $('#prompt_properties').append(selectElement);
                                });
                            }
                        }
                        // $('#prompt_properties').html(response); 
                    },
                    error: function() {
                        aikct_mess_error('Có lỗi xảy ra khi tải thuộc tính.');
                    }
                });
            }
        });

    });

    jQuery(document).ready(function($) {
        $("body").on("click", ".aikct_rewrite_post_pro", function(event) {
            // Retrieve the original prompt content from the hidden field
            var promptContent = $('#original_prompt_content').val();

            if (promptContent) {
                // Iterate over all .aikct_rewrite_post_pro elements and apply replacements
                $('.aikct_rewrite_post_pro').each(function() {
                    let id = $(this).val(); // Get the selected value
                    var dataname = $(this).attr("data-name"); // Get the data-name attribute

                    // Create a regex to match the placeholder
                    let regex = new RegExp(dataname, 'g');

                    promptContent = promptContent.replace(regex, id);
                });

                // Update the prompt content in #aikct_prompt after applying all replacements
                $('#aikct_prompt').val(promptContent);
            } else {
                aikct_mess_error('Prompt content is undefined or empty.');
            }
        });
    });

                                        </script>
                                        <style type="text/css">
             
                                   
    .aikct_box label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .aikct_box textarea,
    .aikct_box select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .aikct_box textarea:focus,
    .aikct_box select:focus {
        border-color: #0073aa;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 115, 170, 0.5);
    }

    /* Định dạng cho nút lưu */
    .aikct_box_save-btn {
        background-color: #0073aa;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .aikct_box_save-btn:hover {
        background-color: #005b8a;
    }

    .aikct_box_save-btn i {
        margin-right: 8px;
    }

    /* Định dạng cho nút Test Prompt */
    .aikct_box_test-btn {
        background-color: #00a0d2;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
        margin-left: 10px;
        transition: background-color 0.3s ease;
    }

    .aikct_box_test-btn:hover {
        background-color: #0085ba;
    }

    .aikct_box_test-btn i {
        margin-right: 6px;
    }

    /* Định dạng popup (nếu muốn style thêm popup trong tương lai) */
    .popup_content {
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        width: 600px;
        height: 400px;
        overflow-y: auto;
    }

    .popup_content h2 {
        font-size: 20px;
        color: #333;
        margin-bottom: 15px;
    }

    .popup_content p {
        font-size: 14px;
        color: #555;
        line-height: 1.6;
    }

    /* Thêm một chút không gian cho form */
    .aikct_box_tabcontent {
        margin-top: 20px;
    }


    /* Ensure the modal and overlay are properly styled */
    #modalOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    #promptModal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        max-width: 1000px;
        max-height: 80%;
        background: #fff;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        overflow: hidden;
    }

    /* Flexbox layout for the content sections */
    #promptModalContent {
        display: flex;
        justify-content: space-between;
    }

    #promptModalContent > div {
        flex: 1;
        margin: 0 10px;
        overflow-y: auto;
        max-height: 500px; /* Adjust height as needed */
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    #promptModalContent h3 {
        margin-top: 0;
    }

    /* Style for the close button */
    #closeModal {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #f1f1f1;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        border-radius: 4px;
    }

    #closeModal:hover {
        background: #ddd;
    }

    /* CSS for the loading spinner */
    #loadingSpinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.5);
        padding: 20px;
        border-radius: 8px;
        z-index: 1001;
    }

    .spinner {
        border: 8px solid rgba(0, 0, 0, 0.1);
        border-left-color: #ffffff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* CSS for the progress bar */
    #progressBar {
        display: none;
        width: 100%;
        background-color: #f3f3f3;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-top: 10px;
    }

    #progress {
        width: 0%;
        height: 30px;
        background-color: #4caf50;
        text-align: center;
        line-height: 30px;
        color: white;
        border-radius: 4px;
    }

                                        </style>
                                        <!-- Add this to your HTML -->

        <section class="dots-container aikct_box_loading" id="loadingSpinner" style="display:none; margin-top: 10px;">
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
    </section>


                                        <div id="modalOverlay" style="display:none;"></div>
    <div id="promptModal" style="display:none;">
        <button id="closeModal">Close</button>
        <div id="promptModalContent">
            <div id="originalContent">
                <h3>Original Content</h3>
                <pre id="original_prompt_content"></pre>
            </div>
            <div id="newContent">
                <h3>New Content</h3>
                <pre id="new_prompt_content"></pre>
            </div>
        </div>
    </div>

                                        <script>
                                        jQuery(document).ready(function($) {
                    $('#test_prompt').on('click', function(e) {
                        e.preventDefault();
                        var url_list = $('#url_list').val().trim();
                        var promptSelect = $('#aikct_prompt');

                        var prompt_content = promptSelect.val();

                        if (!prompt_content) {
                            aikct_mess_error('Please select a prompt.','');
                            return;
                        }

                        if (!url_list) {
                            
                            aikct_mess_error('Please enter at least one URL.','');
                            return;
                        }

                        var urls = url_list.split(/\s*,\s*|\s*\n\s*/).filter(Boolean);
                        var randomUrl = urls[Math.floor(Math.random() * urls.length)];

                        var currentHostname = window.location.hostname;

                        var urlAnchor = document.createElement('a');
                        urlAnchor.href = randomUrl;
                        var randomUrlHostname = urlAnchor.hostname;

                        if (randomUrlHostname !== currentHostname) {
                            aikct_mess_error('The selected URL does not belong to the current site.');
                            return;
                        }
                           
                        var data = {
                                    'action': 'get_prompt_content',
                                    'prompt_content': prompt_content,
                                    'random_url': randomUrl // Send the random URL
                                };
                                 $('#loadingSpinner').show();
                            $.post(ajaxurl, data, function(response) {
                                if (response.success) {
                                    // Set the content of the modal with the response content
                                     $('#loadingSpinner').hide();
                                    var oldContent = response.data.post_content;
                                    var newContent = response.data.post_content_new;

                                    $('#promptModalContent').html(`
                                        <div id="originalContent">
                                            <h3>Original Content</h3>
                                            <pre>${oldContent}</pre>
                                        </div>
                                        <div id="newContent">
                                            <h3>New Content</h3>
                                            <pre>${newContent}</pre>
                                        </div>
                                    `);


                                    
                                    $('#promptModal').fadeIn();
                                    $('#modalOverlay').fadeIn();
                                } else {
                                     $('#loadingSpinner').hide();
                                    aikct_mess_error('Failed to retrieve prompt content.');
                                }
                            });
                        
                    });

                    // Close modal when the "Close" button is clicked
                    $('#closeModal').on('click', function(e) {
                        e.preventDefault();
                        $('#promptModal').fadeOut();
                        $('#modalOverlay').fadeOut();
                    });

                    // Close modal when clicking outside the modal
                    $('#modalOverlay').on('click', function(e) {
                        e.preventDefault();
                        $('#promptModal').fadeOut();
                        $('#modalOverlay').fadeOut();
                    });
    });

                                        jQuery(document).ready(function($) {
                                            $('#saveas').on('click', function(e) {
                                                e.preventDefault();

                                                // Get the URL list and prompt content
                                                var url_list = $('#url_list').val().trim();
                                                var prompt_content = $('#aikct_prompt').val(); // Changed to .val() for textarea

                                                if (!url_list) {
                                                    aikct_mess_error('Please enter a URL list.');
                                                    return;
                                                }

                                                if (!prompt_content) {
                                                    aikct_mess_error('Prompt content is empty.');
                                                    return;
                                                }

                                                var urls = url_list.split(/\r?\n/).filter(function(url) {
                                                    return url.trim() !== '';
                                                });

                                                if (urls.length === 0) {
                                                    aikct_mess_error('No valid URLs found.');
                                                    return;
                                                }
                                                var items = [];
                                                
                                                var urlPromptMap = {};
                                                urls.forEach(function(url) {
                                                    var item = {
                                                        url: url,
                                                        prompt: prompt_content,
                                                        status: false,
                                                        date_in: new Date().toISOString() 
                                                    };
                                                    items.push(item);
                                                });

                                                var data = {
                                                    action: 'save_url_prompts',
                                                    items: items
                                                };

                                                $('#progressBar').show();
                                                $('#progress').css('width', '0%').text('0%');

                                                $.post(ajaxurl, data, function(response) {
                                                    if (response.success) {
                                                        // Update the progress bar
                                                        $('#progressBar').hide();
                                                        $('#progress').text('Complete');
                                                        aikct_mess_success('URL prompts have been saved successfully.');
                                                    } else {
                                                        $('#progressBar').hide();
                                                        aikct_mess_error('Failed to save URL prompts.');
                                                    }
                                                }).fail(function() {
                                                    $('#progressBar').hide();
                                                    aikct_mess_error('An error occurred while saving URL prompts.');
                                                });

                                                var progress = 0;
                                                var interval = setInterval(function() {
                                                    progress += 10;
                                                    if (progress > 100) {
                                                        clearInterval(interval);
                                                    }
                                                    $('#progress').css('width', progress + '%').text(progress + '%');
                                                }, 500);
                                            });
                                        });

                                    </script>

                                 </div>
                                 <!-- Progress Bar Container -->
    <div id="progressBarContainer" style="display: none;">
        <div id="progressBar">
            <div id="progress">0%</div>
        </div>
    </div>

                             <button class="aikct_box_save-btn" id="saveas">
                                <i class="aikct_box_icon"><img draggable="false" role="img" class="emoji" alt="💾" src="https://s.w.org/images/core/emoji/15.0.3/svg/1f4be.svg"></i> 
                                Save 
                            </button>
                         </form>
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

    // Initialize the class
    new aikct_rewrite_post();

    add_action('wp_ajax_get_prompt_content', 'get_prompt_content');
    function get_prompt_content() {
        // Define the array of placeholders
        $arr_replace_prompt = [
            '%TITLE%',
            '%CONTENT%',
            '%AUTHOR%',
            '%CATEGORIES_NAME%',
            '%TAGS_NAME%'
        ];

        // Check if the required data is passed
        if (!isset($_POST['random_url']) || empty($_POST['random_url'])) {
            wp_send_json_error(array('message' => 'URL not provided.'));
            return;
        }

        // Sanitize the input URL
        $random_url = esc_url_raw($_POST['random_url']);

        // Get the post ID from the URL
        $post_id = url_to_postid($random_url);

        // Check if the post ID is valid
        if (!$post_id) {
            wp_send_json_error(array('message' => 'Post ID does not exist for the provided URL.'));
            return;
        }

        // Retrieve the post based on the post ID
        $post = get_post($post_id);

        // Check if the post exists and is valid
        if (!$post || $post->post_status !== 'publish') {
            wp_send_json_error(array('message' => 'Post not found or is not published.'));
            return;
        }

        // Retrieve post categories and tags
        $categories = get_the_category($post_id);
        $category_names = wp_list_pluck($categories, 'name');
        $tags = get_the_tags($post_id);
        $tag_names = $tags ? wp_list_pluck($tags, 'name') : [];

        // Create a replacement array with post values
        $replace_values = [
            $post->post_title,                               // %TITLE%
            $post->post_content,                             // %CONTENT%
            get_the_author_meta('display_name', $post->post_author), // %AUTHOR%
            implode(', ', $category_names),                 // %CATEGORIES_NAME%
            implode(', ', $tag_names)                       // %TAGS_NAME%
        ];

        // Get the prompt content to be replaced
        $prompt_content = isset($_POST['prompt_content']) ? sanitize_textarea_field($_POST['prompt_content']) : '';

        // Replace the placeholders in the prompt content
        $replaced_prompt_content = str_replace($arr_replace_prompt, $replace_values, $prompt_content);


        $proxy = new kctaiproxy();
        if($proxy->status){
            
            $result = $proxy->sendRequest($replaced_prompt_content);

            $content_new = $result['output'];
            
        }else{

            $content_new =  'Please set API KEY';
           
             
           
        }
        wp_send_json_success(array(
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'post_content' => $post->post_content,
            'post_content_new' => $content_new,
            'replaced_prompt_content' => $replaced_prompt_content
        ));
    }


    // Đăng ký action cho AJAX request
    add_action('wp_ajax_load_customer_prompt_properties', 'load_customer_prompt_properties');

    function load_customer_prompt_properties() {
        
        $arr_replace_prompt = ['%TITLE%','%CONTENT%','%AUTHOR%','%CATEGORIES_NAME%','%TAGS_NAME%'];
        $prompt_id = isset($_POST['prompt_id']) ? sanitize_text_field($_POST['prompt_id']) : '';
        $postprompt = get_post($prompt_id);
        if (!empty($prompt_id)) {
           $params = get_post_meta($prompt_id, '_aikct_prompt_params', true);
           $arr = array();
           $arr['prompt_content'] = $postprompt->post_content;
           $arr['prompt_params'] = $params;
           $arr['prompt_arr_replace'] = $arr_replace_prompt;
           echo wp_send_json_success($arr);
           

           

            wp_die(); 
        }

        wp_die('Invalid prompt ID'); 
    }

    add_action('wp_ajax_save_url_prompts', 'save_url_prompts');
    function save_url_prompts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aikct_rewrite_post';

        if (!isset($_POST['items']) || !is_array($_POST['items'])) {
            wp_send_json_error(array('message' => 'Invalid data.'));
            return;
        }

        // Prepare data for insertion
        $url_prompt_map = $_POST['items'];

        // Process new items
        foreach ($url_prompt_map as $new_item) {
            if (isset($new_item['url'])) {
                $url = $new_item['url'];
                $data = array(
                    'url'      => $url,
                    'prompt'   => isset($new_item['prompt']) ? $new_item['prompt'] : '',
                    'status'   => isset($new_item['status']) ? ($new_item['status'] == 'true' ? 1 : 0) : 0,
                    'date_in'  => isset($new_item['date_in']) ? $new_item['date_in'] : current_time('mysql')
                );
                $format = array('%s', '%s', '%d', '%s');

                // Update or insert data
                $existing_row = $wpdb->get_row($wpdb->prepare("SELECT id FROM $table_name WHERE url = %s", $url));
                if ($existing_row) {
                    // Update existing row
                    $wpdb->update($table_name, $data, array('url' => $url), $format);
                } else {
                    // Insert new row
                    $wpdb->insert($table_name, $data, $format);
                }
            }
        }

        wp_send_json_success(array('message' => 'Data saved successfully.'));
    }



    add_action('wp_ajax_delete_url_prompt', 'delete_url_prompt');
    function delete_url_prompt() {
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_url_prompt_nonce')) {
            wp_send_json_error(array('message' => 'Nonce verification failed.'));
            return;
        }

        if (!isset($_GET['id'])) {
            wp_send_json_error(array('message' => 'No ID provided.'));
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'aikct_rewrite_post';
        $id = intval($_GET['id']);

        $result = $wpdb->delete($table_name, ['id' => $id], ['%d']);

        if ($result !== false) {
            wp_send_json_success(array('message' => 'Item deleted successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to delete item.'));
        }
    }


    // Hook vào khi plugin được kích hoạt
    add_action('init', 'aikct_rewrite_post_check_and_create_table');

    function aikct_rewrite_post_check_and_create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aikct_rewrite_post';

        // Kiểm tra xem bảng đã tồn tại chưa
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Nếu bảng chưa tồn tại, tạo bảng
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


}else{
    
}