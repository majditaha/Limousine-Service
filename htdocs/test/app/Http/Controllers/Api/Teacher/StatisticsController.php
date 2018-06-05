<?php namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use \App\Message;

class StatisticsController extends Controller {

    public function get() {
        switch (request()->period_type) {
            case 'week': default:
                $dateGroup = "DATE_PART('dow', transactions.created_at)";
                break;
            case 'month':
                $dateGroup = "DATE_PART('day', transactions.created_at)";
                break;
            case 'year':
                $dateGroup = "DATE_PART('month', transactions.created_at)";
                break;
        }

        $query = \DB::table('messages')
            ->selectRaw(
                "{$dateGroup},
                SUM(CASE WHEN messages.type = 'check_request' THEN transactions.amount ELSE 0 END) AS check_request_amount,
                SUM(CASE WHEN messages.type = 'check_test' THEN transactions.amount ELSE 0 END) AS check_test_amount"
            )
            ->leftJoin('transactions', 'transactions.id', '=', 'messages.teacher_transaction_id')
            ->whereIn('messages.id', function ($subquery) {
                $subquery->from(with(new Message)->getTable())
                    ->select(
                        \DB::raw('(ARRAY_AGG(id ORDER BY created_at ASC))[1]')
                    )
                    ->having(\DB::raw('COUNT(id)'), '>', 1)
                    ->groupBy('uid');
            })
            ->whereIn('messages.type', ['check_request', 'check_test'])
            ->whereTeacherId(auth()->user()->id)
            ->whereNotNull('messages.teacher_transaction_id')
            ->whereNotNull('messages.finished_at')
            ->orderBy(\DB::raw($dateGroup))
            ->groupBy(\DB::raw($dateGroup));

        if (request()->filled('date_start')) {
            $query = $query->where('transactions.created_at', '>=', request()->date_start);
        }

        if (request()->filled('date_end')) {
            $query = $query->where('transactions.created_at', '<=', request()->date_end);
        }

        $data = $query->get();

        return response()->json($data);
    }

}
