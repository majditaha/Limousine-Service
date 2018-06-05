<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItem as MenuItemResource;

class ClientConfigController extends Controller
{
    public function get() {
        $config = [
            'userTypes' => \App\User::getTypes(),
            'userSubTypes' => \App\User::getSubTypes(),
            'discounts' => \App\Plan::getDiscounts(),
            'transactionTypes' => \App\Transaction::getTypes(),
            'messageTypes' => \App\Message::getTypes(),
            'practiceTypes' => \App\Practice::getTypes(),
            'answerTypes' => \App\Practice::getAnswerTypes(),
            'grades' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            'gradeNames' => ['а', 'б', 'в', 'г'],
            'itemsPerPage' => \App\Setting::getValue('itemsPerPage'),
            'contactEmail' => \App\Setting::getValue('contactEmail'),
            'menu' => $this->buildMenu(),
            'uploadcare' => [
                'public_key' => config('services.uploadcare.public_key'),
            ],
            'answerLength' => \App\Setting::getValue('answerLength'),
            'teacherAnswerTime' => \App\Setting::getValue('teacherAnswerTime'),
            'cloudinary' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'upload_preset' => config('services.cloudinary.upload_preset'),
            ],
        ];

        return response()->json($config);
    }

    // TODO: add caching to this, invalidate cache when menu is changed
    private function buildMenu() {
        $rootId = \App\MenuItem::whereNull('parent_id')->first()->id;

        $query = \App\MenuItem::with('children')
            ->whereUserRole(null)
            ->whereParentId($rootId)
            ->whereHas('pages')
            ->orderBy('order', 'ASC')
            ->orderBy('id', 'ASC');

        if (auth()->check()) {
            $query = $query->orWhere('user_role', auth()->user()->role);
        }

        $items = $query->get();

        return MenuItemResource::collection($items);
    }
}
