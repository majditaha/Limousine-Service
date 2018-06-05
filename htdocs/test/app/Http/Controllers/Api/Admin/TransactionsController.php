<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\TransactionReadRequest;

class TransactionsController extends RestController {

    public $model = 'Transaction';

    protected $indexRequest = TransactionReadRequest::class;

    protected function generateMetadata() {
        return [
            'users' => \App\User::get(),
            'types' => \App\Transaction::getTypes(),
            'totalBalance' => \App\Transaction::getTotalBalance(request('created_at_from'), request('created_at_to')),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('user_id')) {
            $query = $query->where('from_user_id', request('user_id'))
                ->orWhere('to_user_id', request('user_id'));
        }

        if (request()->filled('created_at_from')) {
            $query = $query->where('created_at', '>=', request('created_at_from'));
        }

        if (request()->filled('created_at_to')) {
            $query = $query->where('created_at', '<=', request('created_at_to'));
        }

        if (request()->filled('type')) {
            $query = $query->whereType(request('type'));
        }

        return $query;
    }
}
