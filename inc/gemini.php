<?php
defined('ABSPATH') or die();
if(! class_exists( 'Gemini' ) ){


    class Gemini
    {
        private $apiKey;
        private $apiUrl;
        private $headers;
        private $postData;

        public function __construct()
        {
            $this->apiKey = $this->getApiKey();
            $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $this->apiKey;

            $this->headers = [
                'Content-Type: application/json'
            ];
        }

        private function getApiKey()
        {
            $apiKey = get_option('aikct_apikey_gemini');

            if (!$apiKey) {
                return null;
            }

            return trim($apiKey);
        }

        private function refreshApiKey()
        {
            $apiKey = $this->getApiKey();
            if ($apiKey) {
                
                $this->apiKey = trim($apiKey);
                $this->headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }
            return $this->apiKey;
        }

        public function sendRequest($prompt,$op='')
        {   
            $cate = get_site_info();

            $systemcontents = [
                 [   'role'=>'user',
                        'parts' => [
                            [
                                'text' => 'Answer the question based on the context below.'
                            ]
                        ]
                    ],
                    [   'role'=>'user',
                        'parts' => [
                            [
                                'text' => 'The response should be in markdown format.'
                            ]
                        ]
                    ],
                    [   'role'=>'user',
                        'parts' => [
                            [
                                'text' => 'The response should preserve any markdown formatting, links, and styles in the context.'
                            ]
                        ]
                    ],
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => 'Bạn tên là AIKCT, Một trợ lý ảo do plugin AIKCT Engeni tạo ra, hiện tại, bạn sẽ đóng vai là tác giả, seoer cho website ' 
                                . get_home_url() . '. This website has ' 
                                . (string)$cate['tags_count'] . ' tags and ' 
                                . (string)$cate['cate_count'] . ' categories ' 
                            ]
                        ]
                    ],
                     [   'role'=>'user',
                        'parts' => [
                            [
                                'text' => ' website'.get_home_url(). ' '
                            ]
                        ]
                    ]

                ];
                if($op!=''){
                    $formatted_chatlog = [];
                    $aikct_chatlog = json_decode(stripslashes(wp_unslash($op)), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        foreach ($aikct_chatlog as $entry) {
                            $formatted_chatlog[] = [
                                'role' => 'user',
                                'parts' => [
                                    [
                                        'text' => $entry['message']
                                    ]
                                ]
                            ];
                           
                        }
                         
                    } 
                    $systemcontents = array_merge($systemcontents, $formatted_chatlog);
                    
                }

                $systemcontents[] = [   'role'=>'user',
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ];
                
            $data = [
                    'contents' => $systemcontents,
               
                'safetySettings' => [
                        [
                            'category'=>'HARM_CATEGORY_HARASSMENT',
                            'threshold'=> 'BLOCK_NONE',
                        ],
                        [
                            'category'=> 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold'=> 'BLOCK_NONE',
                        ],
                        [
                            'category'=> 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold'=> 'BLOCK_NONE',
                        ],
                        [
                            'category'=> 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold'=> 'BLOCK_NONE',
                        ],
                ]
            ];
            
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200) {
                $responseData = json_decode($response, true);
                if (is_array($responseData) || is_object($responseData)) {
                
                if (is_object($responseData)) {
                    $responseData = (array) $responseData;
                }
                
                $content = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;
                $msg = '';

                if ($content === null) {
                    $this->refreshApiKey();
                    $msg = 'Content was null, token refreshed';
                }
                
                $html = aikct_engine_markdown($content);
                return [
                    'output' => $html,
                    'token' => $this->apiKey,
                    'msg' => $msg,
                    'model' => 'Gemini'
                ];
            } else {
                return ['error' => 'Unexpected response format'];
            }
            } else {
                return ['error' => "Error $httpCode: $response"];
            }
        }

    }

}