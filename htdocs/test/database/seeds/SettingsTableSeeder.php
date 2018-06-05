<?php

use Illuminate\Database\Seeder;
use App\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'itemsPerPage',
                'description' => 'Количество объектов на страницу',
                'value' => '25',
            ],
            [
                'name' => 'daysFree',
                'description' => 'Тестовый период, дней',
                'value' => '7',
            ],
            [
                'name' => 'consultPrice',
                'description' => 'Цена консультации, копеек.',
                'value' => '10000',
            ],
            [
                'name' => 'answerLength',
                'description' => 'Минимальная длина ответа преподавателя',
                'value' => '1000',
            ],
            [
                'name' => 'teacherAnswerTime',
                'description' => 'Максимальное время ответа преподавателя, мин.',
                'value' => '240',
            ],
            [
                'name' => 'teacherDelayPenalty',
                'description' => 'Снижать рейтинг преподавателя при просрочке ответа, сотые балла',
                'value' => '100',
            ],
            [
                'name' => 'commission',
                'description' => 'Комиссия сервиса, %',
                'value' => '50',
            ],
            [
                'name' => 'contactEmail',
                'description' => 'Email для связи',
                'value' => 'contact@expass.ru',
            ],
        ];

        foreach ($data as $item) {
            $setting = Setting::whereName($item['name'])->first();

            if (!$setting) {
                $setting = new Setting;
                $setting->name = $item['name'];
                $setting->description = $item['description'];
                $setting->value = $item['value'];
                $setting->save();
            }
        }
    }
}
