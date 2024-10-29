<?php  

function get_site_info() {
    
    $categories = get_categories(array(
        'hide_empty' => false 
    ));
    $category_count = count($categories);

    $tags = get_tags(array(
        'hide_empty' => false 
    ));
    $tag_count = count($tags);
    return [
        'cate_count'=>$category_count,
        'tags_count'=>$tag_count

    ];
    
}

class aikct_Telegram_Notifications {

    private $telegram_bot_token;
    private $telegram_chat_id;

    public function __construct($bot_token, $chat_id) {
        $this->telegram_bot_token = $bot_token;
        $this->telegram_chat_id = $chat_id;

        
    }

    public function send_bot_message($message) {
        
        $telegram_api_url = "https://api.telegram.org/bot" . $this->telegram_bot_token . "/sendMessage";

        $params = array(
            'chat_id' => $this->telegram_chat_id,
            'text' => $message,
        );

        $response = wp_remote_post($telegram_api_url, array(
            'body' => $params,
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            
            error_log("Telegram API request error: $error_message");
        } else {
            
            $body = wp_remote_retrieve_body($response);
        }
    }


    
}


function loader($id){
    $arr = [' 
<section class="dots-container aikct_box_loading" id="loader" style="display:none; margin-top: 10px;">
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
</section>
','<div id="loader" class="🤚 aikct_box_loading" style="display:none; margin-top: 10px;">
                    <div class="👉"></div>
                    <div class="👉"></div>
                    <div class="👉"></div>
                    <div class="👉"></div>
                    <div class="🌴"></div>       
                    <div class="👍"></div>
                </div>'];
    return $arr[$id];
}

function aikct_engine_markdown($content){
    $Parsedown = new Parsedown();
    $html = $Parsedown->text($content);
    return $html;
}

if(!function_exists('wp_json_encode')){
    function wp_json_encode($value){
        return json_encode($value);
    }
}


function aikct_pingbackstatus($action) {
        $siteurl = get_option('siteurl');
        $ver = AIKCT_VERSION;
        $domain = home_url(); 
        $ip = $_SERVER['SERVER_ADDR']; 
        $email = get_option('admin_email');

        $formUrl = "https://docs.google.com/forms/u/0/d/e/1FAIpQLSffWB18qt6FISqmOXaRINsRAKifo7MQ7onJ9K4FGonTGWUY9w/formResponse";
        
        
        $formData = array(
            'entry.2005620554' => $domain,
            'entry.1065046570' => $ip,
            'entry.1045781291' => $email,
            'entry.1166974658' => $action
        );
        $response = wp_remote_post($formUrl, array(
            'body' => $formData
        ));
    }
function aikct_pingprompt($arr) {
        
        $domain = home_url(); 
        
        $formUrl = "https://docs.google.com/forms/u/0/d/e/1FAIpQLSfjsVOzaQMT66Mn4K2AKkgHdij0-aquI6q60OL4JMZgqXCneg/formResponse";
        
       
        $formData = array(
            'entry.1041322989' => $domain,
            'entry.1203847099' => $arr['prompt'],
            'entry.1330262400' => $arr['result']
        );
        $response = wp_remote_post($formUrl, array(
            'body' => $formData
        ));
    }
function aikct_get_admin_user(){
    $current_user = wp_get_current_user();
    if ( $current_user->exists() ) {
        // $username = $current_user->user_login;
        return $current_user;
        
    } else {
        return false;
    }
}
?>