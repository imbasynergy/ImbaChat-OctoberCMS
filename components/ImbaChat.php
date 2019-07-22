<?php

namespace ImbaSynergy\integrationwidget\Components;

use ImbaSynergy\ImbaChat\Models\Settings;

use ImbaSynergy\ImbaChat\Controllers\ChatLocale;
use Auth;

class ImbaChat extends \Cms\Classes\ComponentBase {

    public function componentDetails() {
        return [
            'name' => 'ImbaChat',
            'description' => 'Add chat widget to page'
        ];
    }

    /**
     * Формирует строки настроек для инициализации виджета
     * @return string
     */
    public function getJsSettingsString($opt = [], $addAuth = true) {

        $user_id = 0;
        $token = "";
        if($addAuth)
        {
            $user = \Auth::getUser();
            if($user)
            {
                $user_id = $user->id;
                $token = \ImbaSynergy\Cppcomet\Controllers\CppComet::getJWT([
                    'user_id' => $user_id,
                    "exp" => date("U")+3600*24*12
                ]);
            }
        }
        $comet_settings = \ImbaSynergy\Cppcomet\Controllers\CppComet::settings();


        $extend_settings = array_merge(
        [
            // Предустановленные значения по умолчанию
            "language" => Settings::conf('language'),
            "user_id" => $user_id,
            "token" => $token,
            "imba_id" => 0,
            "debug" => Settings::conf('debug'),
            "file_dir" => Settings::conf('file_dir'),
            "noPassword" => Settings::conf('no_password', $opt['imba_id']),
            "api_url" => Settings::conf('api_url', \Request::getBaseUrl()."imbachat"),
            "resizable" => Settings::conf('resizable'),
            "draggable" => Settings::conf('draggable'),
            "upload_whitelist" => Settings::confToArray('upload_whitelist'),
            "upload_blacklist" => Settings::confToArray('upload_blacklist'),
            "standalone_mode" => Settings::conf('standalone_mode'),
            "theme" => Settings::conf('theme', true),
            "position" => Settings::conf('position'),
            "useFaviconBadge" => Settings::conf('useFaviconBadge'),
            "updateTitle" => Settings::conf('updateTitle'),
            "cppcomet" => [
                'dev_id' => $comet_settings['user'],
                'node' => $comet_settings['jsApiNode'],
            ],
        ],
        [
            // Берёт настройки компонента из функции defineProperties
            // Если чат включается на удалённый сайт как виджет а не на october то настройки по умолчанию подтянутся
            "theme" => Settings::conf('widget_theme'),
            "position" => Settings::conf('widget_position')
        ], $opt);

        // Событие для дополнения настроек из других плагинов
        \Event::fire('ImbaSynergy.ImbaChat.default.settings', [&$extend_settings]);

        // Берёт данные переводов на основе языка заданного в конфиге
        $imbaChatLocale = new ChatLocale;
        $imbaChatLangJson = json_encode($imbaChatLocale->getTranslations($extend_settings['language']));
        $js = "// Locale\n";
        $js .= "window.imbaChatLangJson = " . $imbaChatLangJson . ";\n\n";

        // Итоговые настройки виджета
        $js .= "// Settings\n";
        $js .= "window.imbaChatSettings = " . json_encode($extend_settings) . ";\n\n";

        return $js;
    }
    public function getJWT()
    {
        $data = array();
        $data['user_id'] = \Config::get('imbasynergy.integrationwidget::user_id');
        $pass = \Config::get('imbasynergy.integrationwidget::in_password');
// Create token header as a JSON string
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        if(isset($data['user_id']))
        {
            $data['user_id'] = (int)$data['user_id'];
        }

// Create token payload as a JSON string
        $payload = json_encode($data);

// Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

// Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

// Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $pass, true);

// Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

// Create JWT
        return trim($base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature);
    }
    public function renderJsSettingsString() {
        return self::getJsSettingsString();
    }

    public function onRun() {
        /*$this->addJs('/plugins/imbasynergy/cppcomet/assets/js/CometServerApi.js');

        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/lib/jquery-ui-1.12.1.custom/jquery-ui.min.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/lib/moment.min.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/just.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/justReactive.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/lib/iziModal-master/js/iziModal.min.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/lib/iziToast-master/dist/js/iziToast.min.js');

        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/lib/emojione.min.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/lib/emojionearea/dist/emojionearea.min.js');

        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/MediaStreamRecorder.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/WebmRecording.js');

        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/permissions.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/guiModal.js');


        \Event::fire('ImbaSynergy.ImbaChat.widget.dependencies', [$this]);

        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/roomVideoChat.js');
        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/dialogs-list.js');

        \Event::fire('ImbaSynergy.ImbaChat.widget.extensions', [$this]);

        $this->addJs('/plugins/imbasynergy/imbachat/assets/js/init.js');

        $this->addCss('/plugins/imbasynergy/imbachat/assets/js/lib/iziModal-master/css/iziModal.min.css');
        $this->addCss('/plugins/imbasynergy/imbachat/assets/js/lib/iziToast-master/dist/css/iziToast.min.css');
        $this->addCss('/plugins/imbasynergy/imbachat/assets/js/lib/emojionearea/dist/emojionearea.css');*/

    }

    // Настройки конфигурируемые в компоненте чата
    public function defineProperties() {
        return [
            'theme' => [
                'title' => 'Theme',
                'type' => 'dropdown',
                'default' => Settings::conf('widget_theme'),
                'placeholder' => 'Select theme',
                'options' => ['default' => 'Default theme', 'dark' => 'Dark theme'],
                'showExternalParam' => false
            ],
            'position' => [
                'title' => 'Messages window position',
                'type' => 'dropdown',
                'default' => Settings::conf('widget_position'),
                'placeholder' => 'Select position',
                'options' => ['right' => 'Right', 'left' => 'Left'],
                'showExternalParam' => false
            ],
            'user_id' => [
                'title' => 'User id',
                'default' => Auth::getUser()->id
            ],
            'dev_id' => [
                'title' => 'Developer id',
                'default' => \Config::get('imbasynergy.integrationwidget::dev_id')
            ]
        ];
    }
}
