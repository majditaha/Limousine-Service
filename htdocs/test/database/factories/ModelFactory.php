<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'active' => true,
        'role' => 'user',
    ];
});

$factory->state(App\User::class, 'admin', [
    'role' => 'admin',
]);

$factory->state(App\User::class, 'teacher', [
    'role' => 'teacher',
]);

$factory->define(App\Discipline::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\Section::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'order' => $faker->randomDigit,
    ];
});

$factory->define(App\Subtype::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\Theory::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'order' => $faker->randomDigit,
        'text' => $faker->text,
    ];
});

$practiceAnswerTypes = App\Practice::getAnswerTypes();
$practiceAnswerTypesNoText = App\Practice::getAnswerTypes();
unset($practiceAnswerTypesNoText[array_search(App\Practice::ANSWER_TYPE_TEXT, $practiceAnswerTypesNoText)]);

$factory->define(App\Practice::class, function (Faker\Generator $faker) use ($practiceAnswerTypes) {
    $types = App\Practice::getTypes();

    return [
        'name' => $faker->word,
        'type' => $types[array_rand($types)],
        'order' => $faker->randomDigit,
        'text' => $faker->text,
        'hint' => $faker->text,
        'solution' => $faker->text,
        'answer_type' => $practiceAnswerTypes[array_rand($practiceAnswerTypes)],
    ];
});

$factory->state(App\Practice::class, 'no_text', [
    'answer_type' => $practiceAnswerTypesNoText[array_rand($practiceAnswerTypesNoText)],
]);

$factory->state(App\Practice::class, 'test', [
    'type' => App\Practice::TYPE_TESTING,
]);

$factory->define(App\Message::class, function (Faker\Generator $faker) {
    return [
        'content' => $faker->text,
    ];
});

$factory->state(App\Message::class, 'review', [
    'type' => 'review',
]);

$factory->state(App\Message::class, 'faq', [
    'type' => 'faq',
]);

$factory->state(App\Message::class, 'check_request', [
    'type' => 'check_request',
]);

$factory->state(App\Message::class, 'check_test', [
    'type' => 'check_test',
]);

$factory->state(App\Message::class, 'message', [
    'type' => 'message',
]);

$factory->define(App\Transaction::class, function (Faker\Generator $faker) {
    return [
    ];
});

$factory->define(App\Plan::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'price' => $faker->randomNumber(5),
        'disciplines' => 2,
        'months' => 3,
        'tests' => 0,
    ];
});
