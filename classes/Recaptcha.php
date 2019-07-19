<?php

class Recaptcha{

    const RECAPTCHA_API_URL = 'https://www.google.com/recaptcha/api/siteverify';
    const RECAPTCHA_SCRIPT_URL = 'https://www.google.com/recaptcha/api.js?hl=en';

    private static $public_key = '6Ldqc64UAAAAAAsjIZl5iOxIUe3_U4ZF4ewEEHfK';
    private static $secret_key = '6Ldqc64UAAAAAHSMmlF3fnAYo7APZMqww8NQzt5n';


    public static function get_recaptcha_form_block(){
        return '<div class="g-recaptcha" data-sitekey="'.self::$public_key.'"></div>';
    }

    public static function get_recaptcha_form_script(){
        return '<script src="'.self::RECAPTCHA_SCRIPT_URL.'"></script>';
    }

    public static function recaptcha_verification($token){
        $secret = self::$secret_key;
        $verify = file_get_contents(self::RECAPTCHA_API_URL ."?secret={$secret}&response={$token}");
        $captcha_success=json_decode($verify);
        if ($captcha_success->success==false) {
            return false;
        }
        else if ($captcha_success->success==true) {
            return true;
        }
    }


}

