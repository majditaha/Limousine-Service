<?php

use Illuminate\Database\Seeder;
use App\MenuItem;
use App\Page;

class MenuAndPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // menu items

        $main = MenuItem::firstOrCreate([
            'name' => 'Главная',
            'parent_id' => null,
            'order' => 0,
            'user_role' => null,
            'alias' => '',
        ]);

        $about = MenuItem::firstOrCreate([
            'name' => 'О сайте',
            'parent_id' => $main->id,
            'order' => 1,
            'user_role' => null,
            'alias' => 'about',
        ]);

        $faq = MenuItem::firstOrCreate([
            'name' => 'FAQ',
            'parent_id' => $main->id,
            'order' => 2,
            'user_role' => null,
            'alias' => 'faq',
        ]);

        $reviews = MenuItem::firstOrCreate([
            'name' => 'Отзывы',
            'parent_id' => $main->id,
            'order' => 3,
            'user_role' => null,
            'alias' => 'reviews',
        ]);

        $contacts = MenuItem::firstOrCreate([
            'name' => 'Контакты',
            'parent_id' => $main->id,
            'order' => 4,
            'user_role' => null,
            'alias' => 'contacts',
        ]);

        // pages

        $mainPage1 = Page::firstOrCreate([
            'menu_item_id' => $main->id,
            'name' => 'Блок на главной 1',
            'content' => 'lorem ipsum',
            'order' => 1,
        ]);

        $mainPage2 = Page::firstOrCreate([
            'menu_item_id' => $main->id,
            'name' => 'Блок на главной 2',
            'content' => 'dolor sit amet',
            'order' => 2,
        ]);

        $aboutPage = Page::firstOrCreate([
            'menu_item_id' => $about->id,
            'name' => 'О сайте',
            'content' => 'Инфа о сайте тут',
            'order' => 1,
        ]);

        $faqPage = Page::firstOrCreate([
            'menu_item_id' => $faq->id,
            'name' => 'FAQ',
            'content' => 'Вопросы и ответы тут',
            'order' => 1,
        ]);

        $reviews = Page::firstOrCreate([
            'menu_item_id' => $reviews->id,
            'name' => 'Отзывы',
            'content' => 'Отзывы тут',
            'order' => 1,
        ]);

        $contacts = Page::firstOrCreate([
            'menu_item_id' => $contacts->id,
            'name' => 'Контакты',
            'content' => 'Контакты тут',
            'order' => 1,
        ]);

        $agreement = Page::firstOrCreate([
            'menu_item_id' => null,
            'name' => 'Пользовательское соглашение',
            'content' => 'Соглашение тут',
            'order' => 1,
        ]);
    }
}
