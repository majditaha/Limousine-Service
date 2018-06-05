<?php

use Illuminate\Database\Seeder;
use App\Plan;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        \DB::statement('TRUNCATE TABLE plans RESTART IDENTITY');
        $data = [
            [
                'name' => 'Экспресс',
                'main' => true,
                'disciplines' => 0,
                'requests' => 0,
                'tests' => 0,
                'months' => 3,
                'price' => 99900,
            ],
            [
                'name' => 'Оптимальный',
                'main' => true,
                'disciplines' => 0,
                'requests' => 0,
                'tests' => 0,
                'months' => 6,
                'price' => 149900,
            ],
            [
                'name' => 'Максимальный',
                'main' => true,
                'disciplines' => 0,
                'requests' => 0,
                'tests' => 0,
                'months' => 9,
                'price' => 199900,
            ],
            [
                'name' => 'Консультации',
                'main' => false,
                'disciplines' => 0,
                'requests' => 1,
                'tests' => 0,
                'months' => 0,
                'price' => 10000,
            ],
            [
                'name' => 'Проверки тестов',
                'main' => false,
                'disciplines' => 0,
                'requests' => 0,
                'tests' => 1,
                'months' => 0,
                'price' => 50000,
            ],
            [
                'name' => 'Месяцы',
                'main' => false,
                'disciplines' => 0,
                'requests' => 0,
                'tests' => 0,
                'months' => 1,
                'price' => 20000,
            ],
        ];

        foreach ($data as $item) {
            $plan = Plan::whereName($item['name'])->first();

            if (!$plan) {
                $plan = new Plan;
                $plan->name = $item['name'];
                $plan->main = $item['main'];
                $plan->disciplines = $item['disciplines'];
                $plan->requests = $item['requests'];
                $plan->tests = $item['tests'];
                $plan->months = $item['months'];
                $plan->price = $item['price'];
                $plan->save();
            }
        }
    }
}
