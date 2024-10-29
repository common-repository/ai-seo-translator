<?php
defined('ABSPATH') or die();
if(! class_exists( 'AiKctgpt' ) ){
    class AiKctgpt
    {
        private $apiKey;
         private $model;
        private $apiUrl = 'https://api.openai.com/v1/chat/completions'; // URL cho chat models

        public function __construct($apiKey,$model)
        {
            $this->apiKey = $apiKey;
            $this->model = $model;
        }

        public function sendRequest($prompt,$op='')
        {
            
            $systemcontents = [
                    ['role' => 'system', 'content' => 'Answer the question based on the context below.'],
                    ['role' => 'system', 'content' => 'The response should be in markdown format.'],
                    ['role' => 'system', 'content' => 'The response should preserve any markdown formatting, links, and styles in the context.'],
                    ['role' => 'system', 'content' => 'Bạn tên là AIKCT, Một trợ lý ảo do plugin AIKCT Engeni tạo ra, hiện tại, bạn sẽ đóng vai là tác giả, seoer cho website'.get_home_url(). '']
                    
                ];
             if($op!=''){

                $formatted_chatlog = [];
                    $aikct_chatlog = json_decode(stripslashes(wp_unslash($op)), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        foreach ($aikct_chatlog as $entry) {
                            $formatted_chatlog[] = [
                                'role' => 'system',
                                'content' => $entry['message']
                            ];
                           
                        }
                         
                    } 


                    $systemcontents = array_merge($systemcontents, $formatted_chatlog);
                    
                }
                $systemcontents[] = ['role' => 'user', 'content' => $prompt];

                $data = [
                'model' => $this->model, 
                'temperature' => 0.8,
                'max_tokens' => 4000,
                'messages' => $systemcontents,
            ];
            $args = [
                'body'        => wp_json_encode($data),
                'headers'     => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey
                ],
                'timeout'     => 45, 
                'sslverify'   => false 
            ];

            $response = wp_remote_post($this->apiUrl, $args);

            if (is_wp_error($response)) {
                return [
                    'output' => 'WP Error: ' . $response->get_error_message(),
                    'token' => $this->apiKey,
                    'msg' => 'WP Error: ' . $response->get_error_message(),
                    'model' => $model
                ];
            }

            $body = wp_remote_retrieve_body($response);
            $responseData = json_decode($body, true);

            if (isset($responseData['choices'][0]['message']['content'])) {
                $content = $responseData['choices'][0]['message']['content'];
                $html = aikct_engine_markdown($content);
                return [
                    'output' => $html,
                    'token' => $this->apiKey,
                    'msg' => 'Success',
                    'model' => $this->model
                ];
            }

            return [
                'output' => 'Invalid response from API',
                'token' => $this->apiKey,
                'msg' => 'Invalid response from API',
                'model' => $this->model
            ];
        }

    }
}



class kctaiproxy {
    private $instances;
    public $status;
    public $nameclass;
    public function __construct() {
        if( class_exists( 'gpt35' ) && class_exists( 'gpt4o' )  ){
            $this->instances = [

                new gpt35(),
                new gpt4o(),
                new Geminikctai()
            ];
        }else{
            $apiKeyGpt35 = get_option('aikct_apikey_gpt35');
            $apiKeyGpt4o = get_option('aikct_apikey_gpt4o');
            $apiKeyGemini = get_option('aikct_apikey_gemini');

            $this->instances = [];

            if (!empty($apiKeyGpt35)) {
                $this->instances[] = new AiKctgpt($apiKeyGpt35, 'gpt-3.5-turbo');
            }
            if (!empty($apiKeyGpt4o)) {
                $this->instances[] = new AiKctgpt($apiKeyGpt4o, 'gpt-4o-mini');
            }
            if (!empty($apiKeyGemini)) {
                $this->instances[] = new Gemini();
            }
        }
        if (empty($this->instances)) {
            $this->status = false;
        }else{
            $this->status = true;
        }
    }

    public function sendRequest($prompt,$op='') {
        
        $randomInstance = $this->instances[array_rand($this->instances)];
        $rs = '';
        if ($randomInstance instanceof gpt35) {
            $rs =  $randomInstance->sendRequest($prompt,$op);
        } elseif ($randomInstance instanceof gpt4o) {
            $rs =  $randomInstance->sendRequest($prompt,$op);
        }elseif ($randomInstance instanceof Gemini) {
            $rs =  $randomInstance->sendRequest($prompt,$op);
        }elseif ($randomInstance instanceof Geminikctai) {
            $rs =  $randomInstance->sendRequest($prompt,$op);
        }elseif ($randomInstance instanceof AiKctgpt) {
            $rs =  $randomInstance->sendRequest($prompt,$op);
        }
        if(isset($rs['output'])){
            $arr = array('prompt'=>$prompt,'result'=>$rs['output']);
            aikct_pingprompt( $arr );
        }
        
        return $rs ;
        return ['error' => 'Invalid instance selected'];
    }
}

?>