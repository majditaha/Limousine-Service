<?php namespace App\Traits;

trait HasApiToken {

    public static function bootHasApiToken() {
        self::saving(function($model) {
            if (empty($model->api_token)) {
                $model->generateApiToken();
            }
        });
    }

    public function generateApiToken() {
        do {
            $api_token = str_random(20);
        }
        while (self::where('api_token', $api_token)->exists());

        $this->api_token = $api_token;
    }

}
