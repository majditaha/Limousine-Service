<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public $timestamps = false;

    public static function getDiscounts() {
        return [0, 15, 25, 30, 35];
    }

    public function getPrice($itemsAmount) {
        if ($itemsAmount == 0) {
            return 0;
        }

        if ($this->main) {
            $discounts = self::getDiscounts();
            if ($itemsAmount > count($discounts)) {
                $discount = end($discounts);
            }
            else {
                $discount = $discounts[$itemsAmount - 1];
            }

            $price = $this->price * $itemsAmount;
            $price -= ($price * $discount) / 100;
            return ceil($price / 100) * 100;
        }
        else {
            return ceil($itemsAmount * $this->price / 100) * 100;
        }
    }
}
