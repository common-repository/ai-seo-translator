<?php 
defined('ABSPATH') or die();
class aikct_chatbot {

    public function render_meta_box($post) {
        
        wp_nonce_field('my_prompt_meta_box', 'my_prompt_meta_box_nonce');
        ?>
        
        <div class="aiautotool" style=" position: relative;">
            <div id="kctai" class="ai-assistant">
                <div class="header">
                    <h2 class="icon-text">
                        <svg width="24" height="24" focusable="false">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H5Zm6.8 11.5.5 1.2a68.3 68.3 0 0 0 .7 1.1l.4.1c.3 0 .5 0 .7-.3.2-.1.3-.3.3-.6l-.3-1-2.6-6.2a20.4 20.4 0 0 0-.5-1.3l-.5-.4-.7-.2c-.2 0-.5 0-.6.2-.2 0-.4.2-.5.4l-.3.6-.3.7L5.7 15l-.2.6-.1.4c0 .3 0 .5.3.7l.6.2c.3 0 .5 0 .7-.2l.4-1 .5-1.2h3.9ZM9.8 9l1.5 4h-3l1.5-4Zm5.6-.9v7.6c0 .4 0 .7.2 1l.7.2c.3 0 .6 0 .8-.3l.2-.9V8.1c0-.4 0-.7-.2-.9a1 1 0 0 0-.8-.3c-.2 0-.5.1-.7.3l-.2 1Z"></path>
                        </svg> KCT
                    </h2>

                    <button id="close-btn" class="close-btn">✖</button>
                </div>
                
                <?php echo $this->loader('0'); ?>
                <div id="aikct-overlay"></div>
                <div id="aikct-prompt-popup">
                    <div class="aikct-prompt-popup-container">
                        <div class="aikct-prompt-popup-content">
                            <h2>Prompt Parameters</h2>
                            <div id="aikct-prompt-params">
                                <!-- Parameters will be added here -->
                            </div>

                            <div class="form-group aikct-prompt-param">
                                <label for="aikct_audience"><?php esc_html_e('Audience', 'ai-seo-translator'); ?></label>
                                <input name="aikct_audience" id="aikct_audience" value="<?php esc_html_e('General Audience', 'ai-seo-translator'); ?>">
                            </div>

                            <div class="form-group aikct-prompt-param">
                                <label for="aikct_tone"><?php esc_html_e('Tone', 'ai-seo-translator'); ?></label>
                                <input name="aikct_tone" id="aikct_tone" value="<?php esc_html_e('Formal', 'ai-seo-translator'); ?>">
                            </div>

                            <div class="form-group aikct-prompt-param">
                                <label for="aikct_style"><?php esc_html_e('Style', 'ai-seo-translator'); ?></label>
                                <input name="aikct_style" id="aikct_style" value="<?php esc_html_e('Business', 'ai-seo-translator'); ?>">
                            </div>

                            <div class="form-group aikct-prompt-param">
                                <label for="aikct_language"><?php esc_html_e('Output Language', 'ai-seo-translator'); ?></label>
                                <input name="aikct_language" id="aikct_language" value="<?php esc_html_e('US English', 'ai-seo-translator'); ?>">
                            </div>
                        </div>
                        <div class="aikct-prompt-popup-footer">
                            <button type="button" id="aikct-submit-prompt">Using Prompt</button>
                            <button type="button" id="aikct-close-popup">Close</button>
                        </div>
                    </div>
                </div>

                <div class="prompt-toggle-container">
                    <button class="prompt-toggle-btn" title="Click to see Prompt list....">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-caret-up" viewBox="0 0 16 16"><path d="M3.204 11h9.592L8 5.519zm-.753-.659 4.796-5.48a1 1 0 0 1 1.506 0l4.796 5.48c.566.647.106 1.659-.753 1.659H3.204a1 1 0 0 1-.753-1.659"></path></svg>
                        Show/Hide Prompt
                    </button>

                   <?php 
                    

                    if (get_option('aikct_images') == 1) { ?>
                        <button class="create_img_for_post" title="AI-generated images....">
                            <i class="fa-regular fa-image"></i> Images
                        </button>
                    <?php }

                    if (get_option('aikct_tags') == 1) { ?>
                        <button class="aikct_tags_generated" title="AI-generated Tags....">
                            <i class="fa-solid fa-tag"></i> Tags
                        </button>
                    <?php }

                    if (get_option('aikct_comment') == 1) { ?>
                        <button class="aikct_comment_generated" title="AI-generated Comments....">
                            <i class="fa-solid fa-comment"></i> Comments
                        </button>
                    <?php }

                    if (get_option('aikct_idea_title') == 1) { ?>
                        <button class="aikct_idea_generated" title="AI Suggest Post title....">
                            <i class="fa-solid fa-bolt"></i> Idea Title
                        </button>
                    <?php }

                    // Check if the Youtube button option is enabled and if the class exists
                    if (get_option('aikct_blogfromyoutube') == 1 && class_exists('aikct_button_youtube')) {
                        $aikct_button_youtube = new aikct_button_youtube();
                        $aikct_button_youtube->render();
                    }

                    // Check if the Rewrite URL button option is enabled and if the class exists
                    if (get_option('aikct_rewrite_from_url') == 1 && class_exists('aikct_button_rewrite_url')) {
                        $aikct_button_rewrite_url = new aikct_button_rewrite_url();
                        $aikct_button_rewrite_url->render();
                    }
                    ?>

                    
                </div>

                <div class="aikct_nav" style="display:none; margin-top: 10px;">
                    <?php
                    $prompts = get_posts(array('post_type' => 'aikct_prompt', 'posts_per_page' => -1));
                    if (empty($prompts)) {
                        echo '<a href="' . esc_url(admin_url('post-new.php?post_type=aikct_prompt')) . '" class="button button-primary">' . esc_html(__('Add Prompt','ai-seo-translator')) . '</a>';
                    } else {
                        foreach ($prompts as $prompt) {
                            ?>
                            <button class="aikct-open-prompt" data-id="<?php echo esc_attr($prompt->ID)?>" id="aikct_suggest_prompt1" type="button">
                                <?php echo esc_html($prompt->post_title); ?>
                                <span class="icon">&#9881;</span> 
                            </button>
                            <?php
                        }
                        echo '<a href="' . esc_url(admin_url('post-new.php?post_type=aikct_prompt')) . '" class="button button-primary">' . esc_html(__('Add Prompt','ai-seo-translator')) . '</a>';
                    }
                    ?>
                </div>

                <div class="aikct_box">
                    <div id="outputId"></div>
                   
                    <div class="modelai" id="modelai" style="display:none; margin-bottom: 10px;color:#"></div>
                    <div class="content" id="my_prompt_result"></div>
                    
                </div>

                <div class="input-area">

                 <div class="textarea-container">
                      <label for="textarea">Enter your text:</label>
                      <div class="textarea-wrapper">
                         <textarea type="text" class="aikct_input" name="ask_ai" id="ask_ai" placeholder="Ask AI to edit or generate..."></textarea>
                        <div class="button-container">
                         
                          
                          <button id="send-btn" type="button"><svg width="24" height="24" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" d="m13.3 22 7-18.3-18.3 7L9 15l4.3 7ZM18 6.8l-.7-.7L9.4 14l.7.7L18 6.8Z"></path></svg></button>
                        </div>
                      </div>
                    </div>
                   
                    

                    <script type="text/javascript">
                     function aikct_input(id) {
    
    var textarea = document.getElementById(id);
    
    textarea.id = id;

    
    textarea.style.height = '60px'; 
    textarea.style.resize = 'both'; 
    textarea.style.overflow = 'auto'; 

    function adjustHeight() {
        textarea.style.height = 'auto'; 
        var scrollHeight = textarea.scrollHeight;
        
        if (scrollHeight > 300) {
            textarea.style.height = '300px'; 
        } else if (scrollHeight > 50) {
            textarea.style.height = scrollHeight + 'px'; 
        } else {
            textarea.style.height = '60px'; 
        }
    } 

    textarea.addEventListener('input', adjustHeight);
    
    textarea.addEventListener('blur', function() {
       
         textarea.style.height = '60px'; 
    });
}



aikct_input('ask_ai');

                    </script>
                </div>
            </div>
        </div>

        <style type="text/css">
            .message-box.right::before {
                content: "<?php echo esc_html($this->get_admin_user()->user_login); ?> : ";
                font-weight: bold;
            }
        </style>
        <script type="text/javascript">
        	
        	  jQuery(document).ready(function ($) {
    
    var input = document.querySelector('#aikct_audience');
    var aikct_audience = inittagfy(input, '<?php  esc_html_e('Marketing Professionals, Content Creators, Developers, Designers, Business Executives, Sales Representatives, Educators, Students, Healthcare Professionals, Financial Analysts','ai-seo-translator');?>');

    var tone = document.querySelector('#aikct_tone');
    var tones = inittagfy(tone, '<?php  esc_html_e('Formal, Informal, Persuasive, Neutral, Optimistic, Pessimistic, Enthusiastic, Sarcastic, Empathetic, Professional, Casual, Humorous, Serious, Inspirational, Direct','ai-seo-translator');?>');

    var style = inittagfy(document.querySelector('#aikct_style'), '<?php  esc_html_e('Academic, Business, Casual, Creative, Technical, Descriptive, Narrative, Analytical, Expository, Persuasive, Reflective, Conversational, Formal, Informal, Journalistic, Poetic, Scientific, Legal, Instructional, Critical','ai-seo-translator');?>');
    var aikct_language = inittagfy(document.querySelector('#aikct_language'), '<?php  esc_html_e('US English, UK English, French, German, Spanish, Italian, Portuguese, Dutch, Russian, Chinese, Japanese, Korean, Arabic, Hindi, Vietnamese, Thai, Turkish, Polish, Swedish, Norwegian, Danish, Finnish, Greek, Czech, Hungarian, Romanian, Hebrew','ai-seo-translator');?>', 1);

    function getTagifyValues() {
        var audienceValues = aikct_audience.getValue();  
        var tonesValues = tones.getValue();       
        var styleValues = style.getValue();       
        var languageValues = aikct_language.getValue(); 

        return {
            audience: audienceValues,
            tone: tonesValues,
            style: styleValues,
            language: languageValues
        };
    }
    var aikct_chatlog = [];
    $('#send-btn').on('click', function (event) {
            event.preventDefault();
            var prompt = $('#ask_ai').val();
            aikct_chatlog.push({ role: 'user', message: prompt });
            $('#my_prompt_result').append('<div class="message-box right">' + prompt + '</div>');
            $('#ask_ai').val('');
            $('#loader').show();

            var data = new FormData();
            data.append('action', 'kct_ai_q');
            data.append('prompt', prompt);
   
            data.append('aikct_chatlog',JSON.stringify(aikct_chatlog));
            data.append('security', '<?php echo esc_attr(wp_create_nonce("kct_ai_q_nonce")); ?>');

            // Function to perform AJAX request with retry logic
            function performAjaxRequest(retries) {
                $.ajax({
                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                    method: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            var output = JSON.stringify(response.data.output);
                            if (!output) {
                                alert('Error: Output is undefined or empty.');
                                clearAndHidePrompt();
                                $('#loader').hide();
                                return;
                            }

                            output = output.replace(/\\\"/g, '\"');
                            var html = aikctconvertentity(output).replace(/\\n/g, '');
                            aikct_Chat(html, 'my_prompt_result', 5);
                            aikct_chatlog.push({ role: 'model', message: html });
                            $('#my_prompt_result').addClass('has-content');
                            
                            $('#loader').hide();
                            $('#modelai').text('Model: ' + response.data.model).show();


                        } else {
                            $('#modelai').text('Model: ' + response.data.model).show();
                            $('#my_prompt_result').text('Error: ' + response.data);
                        }
                    },
                    error: function (error) {
                        console.error('Error:', error);
                        if (retries > 0) {
                            console.log('Retrying... (' + retries + ' attempts left)');
                            performAjaxRequest(retries - 1);
                        } else {
                            alert('Failed after 3 attempts. Please try again later.');
                            $('#loader').hide();
                        }
                    }
                });
            }
            console.log(aikct_chatlog);
            performAjaxRequest(3);
        });


    $('.aikct-open-prompt').on('click', function () {
        const postId = $(this).data('id');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'aikct_get_prompt_params',
                post_id: postId,
                aikct_nonce: '<?php echo esc_attr(wp_create_nonce('aikct_get_prompt_params_nonce'));?>'
            },
            success: function (response) {
                if (response.success) {
                    const { params, content } = response.data;

                    window.promptContent = content;
                    window.promptParams = params;

                    if (params == '') {
                        $('#ask_ai').val(content);
                        console.log(content);
                    } else {
                        let paramsHtml = '';
                        paramsHtml += `<p class="message">Prompt: <code>${content}</code></p>`;
                        $.each(params, function (index, param) {
                            const paramId = `param${index + 1}`;
                            switch (param.type) {
                                case 'input_text':
                                    paramsHtml += `
                                        <div class="aikct-prompt-param" data-id="${paramId}">
                                            <label>${param.label}</label>
                                            <input type="text" name="${param.value}" data-name="${param.label}" />
                                        </div>
                                    `;
                                    break;
                                case 'selectbox':
                                    paramsHtml += `
                                        <div class="aikct-prompt-param" data-id="${paramId}">
                                            <label>${param.label}</label>
                                            <select name="${param.value}" data-name="${param.label}">
                                                ${param.options.map(option => `<option value="${option}">${option}</option>`).join('')}
                                            </select>
                                        </div>
                                    `;
                                    break;
                                case 'textarea':
                                    paramsHtml += `
                                        <div class="aikct-prompt-param" data-id="${param.value}">
                                            <label>${param.label}</label>
                                            <textarea name="${param.value}" data-name="${param.label}"></textarea>
                                        </div>
                                    `;
                                    break;
                                default:
                                    break;
                            }
                        });
                        $('#aikct-prompt-params').html(paramsHtml);
                        $('#aikct-prompt-popup').show();
                    }
                } else {
                    alert('Failed to load prompt data.');
                }
            }
        });
    });

    $('#aikct-close-popup').on('click', function () {
        $('#aikct-prompt-popup').hide();
    });

    $('#aikct-submit-prompt').on('click', function () {
        const params = {};
        $('#aikct-prompt-params .aikct-prompt-param').each(function () {
            const dataName = $(this).find('input, select, textarea').data('name');
            const value = $(this).find('input, select, textarea').val();
            params[dataName] = value;
        });

        let promptText = window.promptContent;
        console.log(params);

        $.each(params, function (code, value) {
            const regex = new RegExp(code, 'g');
            promptText = promptText.replace(regex, value);
        });

        var tagifyValues = getTagifyValues();
        
        if (tagifyValues.audience.length > 0) {
            promptText += '\nAudience: ' + tagifyValues.audience.join(', ');
        }

        if (tagifyValues.tone.length > 0) {
            promptText += '\nTone: ' + tagifyValues.tone.join(', ');
        }

        if (tagifyValues.style.length > 0) {
            promptText += '\nStyle: ' + tagifyValues.style.join(', ');
        }

        if (tagifyValues.language.length > 0) {
            promptText += '\nLanguage: ' + tagifyValues.language.join(', ');
        }
        console.log(promptText);

        $('#ask_ai').val(aikctconvertentity(promptText));

        $('#aikct-prompt-popup').hide();
        const toggleBtn = document.querySelector('.prompt-toggle-btn');
        $('#send-btn').prop('disabled', true);
        toggleBtn.click();
        $('#send-btn').click();
    });

    $('#ask_ai').on('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            $('#send-btn').click();
        }
    });




                    function detectEditorAndInsertContent(html) {
                        
                        if (typeof wpseEditor !== 'undefined') {
                            const editorType = wpseEditor.editorType;

                            if (editorType === 'gutenberg' && typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined') {
                                var blockEditor = wp.data.select('core/block-editor');
                                if (blockEditor) {
                                    var content = blockEditor.getBlocks();
                                    var newBlock = wp.blocks.createBlock('core/paragraph', {
                                        content: html
                                    });
                                    wp.data.dispatch('core/block-editor').insertBlocks(newBlock, content.length);
                                    clearAndHidePrompt();
                                    return 'gutenberg';
                                }
                            } else if (editorType === 'classic') {
                                if (typeof tinymce !== 'undefined') {
                                    tinymce.get('content').execCommand('mceInsertContent', false, html);
                                    clearAndHidePrompt();
                                    return 'tinymce';
                                }

                                var editor = jQuery('#content');
                                if (editor.length) {
                                    editor.val(editor.val() + html);
                                    clearAndHidePrompt();
                                    return 'classic';
                                }
                            }
                        }

                        console.log('No recognized editor is active');
                        return 'none';
                    }

                    function clearAndHidePrompt() {
                        $('#my_prompt_result').empty().removeClass('has-content');
                        $('#modelai').empty();
                       
                        $('#close-btn').click();
                    }

                    const askAiInput = $('#ask_ai');
                    const insertBtn = $('#send-btn');
                    insertBtn.prop('disabled', true);
                    askAiInput.on('input', function() {
                        insertBtn.prop('disabled', askAiInput.val().trim() === '');
                    });

                });



jQuery(document).ready(function ($) {
    const $toggleBtn = $('.prompt-toggle-btn');
    const $promptNav = $('.aikct_nav');
    
    function updateButtonText() {
        if ($promptNav.css('display') === 'none') {
            $toggleBtn.html('Show Prompt <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-caret-down" viewBox="0 0 16 16"><path d="M3.204 11h9.592L8 5.519zm-.753-.659 4.796-5.48a1 1 0 0 1 1.506 0l4.796 5.48c.566.647.106 1.659-.753 1.659H3.204a1 1 0 0 1-.753-1.659"></path></svg>');
        } else {
            $toggleBtn.html('Hide Prompt <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-caret-up" viewBox="0 0 16 16"><path d="M3.204 11h9.592L8 5.519zm-.753-.659 4.796-5.48a1 1 0 0 1 1.506 0l4.796 5.48c.566.647.106 1.659-.753 1.659H3.204a1 1 0 0 1-.753-1.659"></path></svg>');
        }
    }

    updateButtonText();

    $toggleBtn.on('click', function (e) {
        e.preventDefault(); 
        if ($promptNav.css('display') === 'none') {
            $promptNav.css('display', 'block');
        } else {
            $promptNav.css('display', 'none');
        }
        
        updateButtonText();
    });
});


jQuery(document).ready(function($) {
                             $('.aikct_idea_generated').on('click', function(e) {
                            e.preventDefault();
                            console.log('Idea click');
                            // aikct_idea_title_post

                            function suggestIdea(retries) {
                                $('#my_prompt_result').append('<div class="message-box right">Begin requiere Ideal Post for Website</div>');
                            $('#my_prompt_result').addClass('has-content');
                            $('#loader').show();

                                $.ajax({
                                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>', 
                                    type: 'POST',
                                    data: {
                                        action: 'aikct_idea_title_post',
                                        security: aikct_js.nonce
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            console.log(response);
                                            $('#loader').hide();
                                            var output = response.data.output;
                                            if (Array.isArray(output)) {
                                                var itemDivtong = $('<div class="message-box left"></div>');
                                                output.forEach(function(item) {
                                                    var title = item.title;
                                                    var categories = item.categories;
                                                    var tags = item.tags;
                                                    
                                                    var itemDivcon = $('<div class="item-post"></div>');
                                                    var content = `

                                                        <h3>${title}</h3>
                                                        <p><strong>Category:</strong> ${categories}</p>
                                                        <p><strong>Tags:</strong> ${tags}</p>
                                                        <div class="button-group"><button class="use-item-btn add-to-content">Using post</button></div>
                                                    `;

                                                    itemDivcon.html(content); 

                                                    itemDivcon.find('.use-item-btn').on('click', function(e) {
                                                        e.preventDefault();
                                                         $('#loader').show();
                                                         $('#my_prompt_result').append('<div class="message-box right">Start create Post draft : '+title+'</div>');
                                                        $.ajax({
                                                            url:aikct_js.ajax_url,
                                                            type: 'POST',
                                                            data: {
                                                                action: 'aikct_create_draft_post',
                                                                title: title,
                                                                categories: categories,
                                                                tags: tags,
                                                                security: aikct_js.nonce
                                                            },
                                                            success: function(response) {
                                                                if (response.success) {

                                                                   var editLink = decodeURIComponent(response.data.edit_link);
                                                                   editLink = editLink.replace(/&amp;/g, '&');
                                                                   
                                                                     window.location.href = editLink;
                                                                } else {
                                                                    console.log('Failed to create draft.');
                                                                }
                                                            },
                                                            error: function() {
                                                                console.log('Error creating draft.');
                                                            }
                                                        });
                                                        
                                                    });
                                                    itemDivtong.append(itemDivcon);
                                                });

                                                $('#my_prompt_result').append(itemDivtong);
                                            }else{
                                                retryRequestidea(retries);
                                            }
                                            
                                        } else {
                                            retryRequestidea(retries);
                                        }
                                    },
                                    error: function() {
                                        retryRequestidea(retries);
                                    }
                                });
                            }
                            
                            function retryRequestidea(retries) {
                                if (retries > 0) {
                                    console.log('Retrying request... Attempts left: ' + retries);
                                    suggestIdea(retries - 1);
                                } else {
                                    $('#loader').hide();
                                    aikct_mess_error('Failed to Idea!', '');
                                }
                            }

                            suggestIdea(2);
                        });
                            $('.aikct_tags_generated').on('click', function(e) {
                            e.preventDefault();
                            console.log('create tag');
                            
                            var postTitle = aikct_get_title();
                            var postContent = aikct_get_content();
                            
                            if (!postTitle) {
                                aikct_mess_error('Title is missing!', 'Please input title post');
                                return;
                            }
                            
                            $('#my_prompt_result').append('<div class="message-box right">Begin creating prompt tags for post: ' + postTitle + '</div>');
                            $('#my_prompt_result').addClass('has-content');
                            $('#loader').show();
                            $('#my_prompt_result').scrollTop($('#my_prompt_result')[0].scrollHeight);
                            
                            function suggestTag(retries) {
                                $.ajax({
                                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>', 
                                    type: 'POST',
                                    data: {
                                        action: 'aikct_suggest_tag',
                                        title: postTitle,
                                        content: postContent,
                                        security: aikct_js.nonce
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            console.log(response);
                                            $('#loader').hide();
                                            var output = response.data.output;
                                            if (Array.isArray(output)) {
                                                var tags = output.map(function(item) {
                                                    return item.tag;
                                                });
                                                var tagString = tags.join(', ');
                                                console.log(tagString);
                                                var messageBox = $('<div/>', {
                                                    class: 'message-box left',
                                                    text: 'Tags suggest: ' + tagString + ''
                                                });
                                                var buttonGroup = $('<div/>', {
                                                    class: 'button-group'
                                                });

                                                var addButton = $('<button/>', {
                                                    text: 'Add Tags to Post',
                                                    class: 'add-to-content',
                                                    click: function(e) {
                                                         e.preventDefault();
                                                        // $('#new-tag-post_tag').val(tagString);
                                                        // $('.tagadd').click();
                                                        var tagInput = $('#new-tag-post_tag');
                                                        var tagAddButton = $('.tagadd');
                                                        if (tagInput.length && tagAddButton.length) {
                                                            tagInput.val(tagString);
                                                            tagAddButton.click();
                                                            addButton.remove();
                                                            aikct_Chat('Tags add to Post: '+postTitle+' Success!', 'my_prompt_result', 5);
                                                        } else {
                                                            console.log('Tag input field or add button not found.');
                                                        }
                                                    }
                                                });
                                                buttonGroup.append(addButton);
                                                messageBox.append(buttonGroup);
                                                $('#my_prompt_result').append(messageBox);
                                            } else {
                                                retryRequest(retries);
                                            }
                                        } else {
                                            retryRequest(retries);
                                        }
                                    },
                                    error: function() {
                                        retryRequest(retries);
                                    }
                                });
                            }
                            
                            function retryRequest(retries) {
                                if (retries > 0) {
                                    console.log('Retrying request... Attempts left: ' + retries);
                                    suggestTag(retries - 1);
                                } else {
                                    $('#loader').hide();
                                    aikct_mess_error('Failed to generate tags!', '');
                                }
                            }

                            suggestTag(2);
                        });
                           
                            $('.aikct_comment_generated').on('click', function(e) {
                                    e.preventDefault();
                                    console.log('create comment');
                            
                            var postTitle = aikct_get_title();
                            var postContent = aikct_get_content();
                            
                            if (!postTitle) {
                                aikct_mess_error('Title is missing!', 'Please input title post');
                                return;
                            }
                            
                            $('#my_prompt_result').append('<div class="message-box right">Begin creating prompt Comment for post: ' + postTitle + '</div>');
                            $('#my_prompt_result').addClass('has-content');
                            $('#loader').show();
                            $('#my_prompt_result').scrollTop($('#my_prompt_result')[0].scrollHeight);
                            
                            function suggestComments(retries) {
                                $.ajax({
                                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>', 
                                    type: 'POST',
                                    data: {
                                        action: 'aikct_suggest_comments',
                                        title: postTitle,
                                        content: postContent,
                                        security: aikct_js.nonce
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            console.log(response);
                                            $('#loader').hide();
                                            var output = response.data.output;
                                            if (Array.isArray(output)) {

                                                var messageBox = $('<div/>', {
                                                    class: 'message-box left'
                                                });

                                                output.forEach(function(item) {
                                                    var nickname = $('<strong/>', {
                                                        text: item.nickname,
                                                        css: {
                                                            color: 'blue' // Change color as needed
                                                        }
                                                    });
                                                    
                                                    var comment = $('<span/>', {
                                                        text: ': ' + item.comment
                                                    });
                                                    
                                                    var commentLine = $('<div/>', {
                                                        class: 'comment-line'
                                                    }).append(nickname).append(comment);
                                                    
                                                    messageBox.append(commentLine);
                                                });

                                                var buttonGroup = $('<div/>', {
                                                    class: 'button-group'
                                                });

                                                var addButton = $('<button/>', {
                                                    text: 'Add Comments to Post',
                                                    class: 'add-to-content',
                                                    click: function(e) {
                                                        e.preventDefault();
                                                        aikct_Chat('Begin insert Comment to Post: '+postTitle, 'my_prompt_result', 5);
                                                        
                                                        $('#my_prompt_result').addClass('has-content');
                                                        $('#loader').show();
                                                        var postID = $('#post_ID').val(); 
                                                        
                                                        if (!postID) {
                                                            aikct_mess_error('Post ID not found. Please create a post first.','Please Save draft post and try again.');
                                                            
                                                        } else {
                                                           $.ajax({
                                                                    url: aikct_js.ajax_url, 
                                                                    method: 'POST',
                                                                    data: {
                                                                        action: 'aikct_add_comments_to_post',
                                                                        post_id: postID,
                                                                        comments: output,
                                                                        security: aikct_js.nonce
                                                                    },
                                                                    success: function(response) {
                                                                         $('#loader').hide();
                                                                        if (response.success) {
                                                                            console.log('Comments added:', response.data);

                                                                                addButton.remove();
                                                                                aikct_Chat('Comments have been successfully added to the post! '+postTitle, 'my_prompt_result', 5);
                                                        
                                                                               
                                                                        } else {
                                                                            console.error('Error adding comments:', response.data);
                                                                        }
                                                                    },
                                                                    error: function(error) {
                                                                        console.error('AJAX error:', error);
                                                                    }
                                                                });
                                                        }

                                                    }
                                                });
                                                buttonGroup.append(addButton);
                                                messageBox.append(buttonGroup);
                                                $('#my_prompt_result').append(messageBox);
                                            } else {
                                                retryRequestComment(retries);
                                            }
                                        } else {
                                            retryRequestComment(retries);
                                        }
                                    },
                                    error: function() {
                                        retryRequestComment(retries);
                                    }
                                });
                            }
                            
                            function retryRequestComment(retries) {
                                if (retries > 0) {
                                    console.log('Retrying request... Attempts left: ' + retries);
                                    suggestComments(retries - 1);
                                } else {
                                    $('#loader').hide();
                                    aikct_mess_error('Failed to generate Comments!', '');
                                }
                            }

                            suggestComments(2);
                        });
                            
                                $('.create_img_for_post').on('click', function(e) {
                                    e.preventDefault();
                                    
                                    var postTitle = aikct_get_title();
                                    var postContent = aikct_get_content();



                                    

                                    if (!postTitle ) {
                                        
                                        aikct_mess_error('Title is missing!','Pls input title post');
                                                         
                                        return;
                                    }
                                    $('#my_prompt_result').append('<div class="message-box right"> Begin create prompt image for post:'+postTitle+' </div>');
                                    $('#my_prompt_result').addClass('has-content');
                                    $('#loader').show();
                                    $('#my_prompt_result').scrollTop($('#my_prompt_result')[0].scrollHeight);
                                    $.ajax({
                                        url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                                        type: 'POST',
                                        data: {
                                            action: 'suggest_prompt_img',
                                            title: postTitle,
                                            content: postContent,
                                            security: aikct_js.nonce
                                        },
                                        success: function(response) {
                                            if(response.success){
                                                $('#my_prompt_result').append('<div class="message-box right">' + response.data + '</div>');
                                                $('#loader').hide();
                                                $('#my_prompt_result').addClass('has-content');
                                                $('#loader').show();
                                                $('#my_prompt_result').append('<div class="message-box left img">Image begin create...</div>');
                                                $('#my_prompt_result').scrollTop($('#my_prompt_result')[0].scrollHeight);
                                                $.ajax({
                                                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                                                    type: 'POST',
                                                    data: {
                                                        action: 'create_image_for_post',
                                                        title: postTitle,
                                                        content: postContent,
                                                        prompt:response.data,
                                                        security: aikct_js.nonce
                                                    },
                                                    success: function(response) {
                                                       if (response.success) {
                                                            console.log(response);

                                                            $('#loader').hide();
                                                            let img = response.data.url;
                                                            if(img){
                                                                $('.message-box.left.img').html(
                                                                    '<img src="' + img + '" />' +
                                                                    '<div class="button-group">' +
                                                                    '<button class="add-to-content">Add to Content</button>' +
                                                                    '<button class="set-thumb">Set as Thumb</button>' +
                                                                    '</div>'
                                                                );
                                                            }else{
                                                                $('.message-box.left.img').html(
                                                                    '' + response.data.prompt + '' 
                                                                );
                                                            }
                                                            
                                                            $('#my_prompt_result').scrollTop($('#my_prompt_result')[0].scrollHeight);

                                                            $('.add-to-content').on('click', function(e) {
                                                                e.preventDefault();
                                                                if (typeof wpseEditor !== 'undefined') {
                                                                    const editorType = wpseEditor.editorType;

                                                                    if (editorType === 'gutenberg') {
                                                                        postContent = wp.data.select('core/editor').getEditedPostAttribute('content');

                                                                        wp.data.dispatch('core/editor').insertBlocks(
                                                                            wp.blocks.createBlock('core/image', { url: img })
                                                                        );

                                                                    } else if (editorType === 'classic') {
                                                                        postContent = tinyMCE.activeEditor.getContent();

                                                                        tinyMCE.activeEditor.insertContent('<img src="' + img + '" />');
                                                                    } 
                                                                }

                                                                
                                                            });
                                                            $('.set-thumb').on('click', function(e) {
                                                                 var postID = $('#post_ID').val(); 
                                                        
                                                                if (!postID) {
                                                                    aikct_mess_error('Post ID not found. Please create a post first.','Please Save draft post and try again.');
                                                                    return;
                                                                } 
                                                                e.preventDefault();
                                                                $.ajax({
                                                                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                                                                    type: 'POST',
                                                                    data: {
                                                                        action: 'set_post_thumbnail',
                                                                        post_id: '<?php echo get_the_ID(); ?>', 
                                                                        thumbnail_url: img,
                                                                        security: aikct_js.nonce
                                                                    },
                                                                    success: function(response) {
                                                                        if (response.success) {
                                                                            aikct_mess_success('Thumbnail set successfully!','');
                                                                            
                                                                        } else {
                                                                            aikct_mess_error('Failed to set thumbnail.!','');
                                                                            
                                                                        }
                                                                    },
                                                                    error: function(xhr, status, error) {
                                                                         $('#loader').hide();
                                                                        aikct_mess_error('An error occurred:',error);
                                                                            
                                                                        
                                                                    }
                                                                });
                                                            });

                                                             $('#loader').hide();

                                                        } else {
                                                            $('#loader').hide();
                                                        }

                                                        
                                                    },
                                                    error: function(xhr, status, error) {
                                                         $('#loader').hide();
                                                         aikct_mess_error('An error occurred:',error);
                                                                            
                                                                        
                                                    }
                                                });
                                                
                                            }
                                            
                                        },
                                        error: function(xhr, status, error) {
                                            aikct_mess_error('An error occurred:',error);
                                            $('#loader').hide();
                                                         
                                        }
                                    });
                                });


    // aikct_input('ask_ai');
                            });
        </script>
        <?php
    }

    private function loader($value) {
        
        return loader(0);
    }

    private function get_admin_user() {
        return wp_get_current_user();
    }
}



 ?>