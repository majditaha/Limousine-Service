<?php namespace App\Exceptions;

class AtLeastOneItemShouldBePurchasedException extends \Exception {

    public function render($request) {
        return response()->json([
            'error' => 'Необходимо указать количество больше 0',
        ], 400);
    }

};
