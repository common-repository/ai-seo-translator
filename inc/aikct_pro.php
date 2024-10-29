<?php

defined( 'ABSPATH' ) or die;
class aikct {
    public $languageCodes = [
        'vi'  => 'Vietnamese',
        'en'  => 'English',
        'th'  => 'Thai',
        'ja'  => 'Japanese',
        'fr'  => 'French',
        'pt'  => 'Portuguese',
        'af'  => 'Afrikaans',
        'sq'  => 'Albanian',
        'am'  => 'Amharic',
        'ar'  => 'Arabic',
        'hy'  => 'Armenian',
        'az'  => 'Azerbaijani',
        'eu'  => 'Basque',
        'be'  => 'Belarusian',
        'bn'  => 'Bengali',
        'bs'  => 'Bosnian',
        'bg'  => 'Bulgarian',
        'ca'  => 'Catalan',
        'ceb' => 'Cebuano',
        'ny'  => 'Chichewa',
        'zh'  => 'Chinese',
        'co'  => 'Corsican',
        'hr'  => 'Croatian',
        'cs'  => 'Czech',
        'da'  => 'Danish',
        'nl'  => 'Dutch',
        'eo'  => 'Esperanto',
        'et'  => 'Estonian',
        'tl'  => 'Filipino',
        'fi'  => 'Finnish',
        'fy'  => 'Frisian',
        'gl'  => 'Galician',
        'ka'  => 'Georgian',
        'de'  => 'German',
        'el'  => 'Greek',
        'gu'  => 'Gujarati',
        'ht'  => 'Haitian Creole',
        'ha'  => 'Hausa',
        'haw' => 'Hawaiian',
        'he'  => 'Hebrew',
        'hi'  => 'Hindi',
        'hmn' => 'Hmong',
        'hu'  => 'Hungarian',
        'is'  => 'Icelandic',
        'ig'  => 'Igbo',
        'id'  => 'Indonesian',
        'ga'  => 'Irish',
        'it'  => 'Italian',
        'jv'  => 'Javanese',
        'kn'  => 'Kannada',
        'kk'  => 'Kazakh',
        'km'  => 'Khmer',
        'ko'  => 'Korean',
        'ku'  => 'Kurdish',
        'ky'  => 'Kyrgyz',
        'lo'  => 'Lao',
        'la'  => 'Latin',
        'lv'  => 'Latvian',
        'lt'  => 'Lithuanian',
        'lb'  => 'Luxembourgish',
        'mk'  => 'Macedonian',
        'mg'  => 'Malagasy',
        'ms'  => 'Malay',
        'ml'  => 'Malayalam',
        'mt'  => 'Maltese',
        'mi'  => 'Maori',
        'mr'  => 'Marathi',
        'mn'  => 'Mongolian',
        'my'  => 'Myanmar',
        'ne'  => 'Nepali',
        'no'  => 'Norwegian',
        'ps'  => 'Pashto',
        'fa'  => 'Persian',
        'pl'  => 'Polish',
        'pa'  => 'Punjabi',
        'ro'  => 'Romanian',
        'ru'  => 'Russian',
        'sm'  => 'Samoan',
        'gd'  => 'Scots Gaelic',
        'sr'  => 'Serbian',
        'st'  => 'Sesotho',
        'sn'  => 'Shona',
        'sd'  => 'Sindhi',
        'si'  => 'Sinhala',
        'sk'  => 'Slovak',
        'sl'  => 'Slovenian',
        'so'  => 'Somali',
        'es'  => 'Spanish',
        'su'  => 'Sundanese',
        'sw'  => 'Swahili',
        'sv'  => 'Swedish',
        'tg'  => 'Tajik',
        'ta'  => 'Tamil',
        'te'  => 'Telugu',
        'tr'  => 'Turkish',
        'uk'  => 'Ukrainian',
        'ur'  => 'Urdu',
        'uz'  => 'Uzbek',
        'cy'  => 'Welsh',
        'xh'  => 'Xhosa',
        'yi'  => 'Yiddish',
        'yo'  => 'Yoruba',
        'zu'  => 'Zulu',
    ];

    public static $is_premium = false;

    public static function is_premium() {
        $fs = freemius( 16313 );
        return $fs;
    }

    public function render_plan() {
    }

    public function render_setting() {
    }

    public function render_tab_setting() {
    }

    public function render_feature( $settings ) {
        ?>
        <div class="integration-card">
            <div class="integration-header">
                <h2><i class="fa-regular fa-circle-check"></i> <?php 
        echo esc_html( $settings['title'] );
        ?></h2>
                <?php 
        ?>
                    <button class="upgrade-button" onclick="window.location.href='<?php 
        echo esc_url( ast_fs()->get_upgrade_url() );
        ?>';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="#2C3E50" stroke-width="2" fill="none"/>
                            <path d="M12 16.5L8.5 13.5H11V8H13V13.5H15.5L12 16.5Z" fill="#2C3E50"/>
                        </svg>
                        Upgrade to Pro
                    </button>
                <?php 
        ?>
            </div>
            <p><?php 
        echo esc_html( $settings['desc'] );
        ?>.</p>
        </div>
        <?php 
    }

}
