<?php namespace App\Http\Controllers\Api;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\MessageReadRequest;
use \App\Http\Requests\MessageWriteRequest;
use \App\Http\Requests\MessageMarkReadRequest;
use \App\Http\Requests\MessageSetRatingRequest;
use \App\Http\Resources\Message as MessageResource;
use \App\Http\Resources\MessageHistoryItem as MessageHistoryItemResource;
use \App\Message;

class MessagesController extends RestController {

    public $model = 'Message';

    protected $resource = MessageResource::class;

    protected $allowedWith = ['practice', 'practice.answers.userAnswers'];

    protected $indexRequest = MessageReadRequest::class;
    protected $showRequest = MessageReadRequest::class;
    protected $storeRequest = MessageWriteRequest::class;

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('active')) {
            $query = $query->whereIn('id', function ($subquery) {
                $subquery->from(with(new Message)->getTable())
                    ->select(
                        \DB::raw('(ARRAY_AGG(id ORDER BY created_at ASC))[1]')
                    )
                    ->where(function($subquery) {
                        $subquery->where('from_user_id', auth()->user()->id)
                            ->where('to_user_id', null);
                    })
                    ->orWhere('to_user_id', auth()->user()->id)
                    ->groupBy('uid');

                if (request()->bool('active')) {
                    $subquery->having(\DB::raw('COUNT(id)'), 1);
                }
                else {
                    $subquery->having(\DB::raw('COUNT(id)'), '>', 1);
                }
            });
        }
        else {
            $query = $query->whereIn('id', function ($subquery) {
                $subquery->from(with(new Message)->getTable())
                    ->select(
                        \DB::raw('(ARRAY_AGG(id ORDER BY created_at ASC))[1]')
                    )
                    ->where(function($subquery) {
                        $subquery->where('from_user_id', auth()->user()->id)
                            ->where('to_user_id', null);
                    })
                    ->orWhere('to_user_id', auth()->user()->id)
                    ->groupBy('uid');
            });
        }

        if (request()->filled('type')) {
            $type = request('type');
            if ($type == 'checks') {
                $type = ['check_request', 'check_test'];
                $query = $query->whereIn('type', $type);
            }
            else {
                $query = $query->whereType($type);
            }
        }

        if (request()->filled('uid')) {
            $query = $query->whereUid(request('uid'));
        }

        return $query;
    }

    public function store() {
        if (request('type') == Message::TYPE_CHECK_REQUEST && !auth()->user()->canCreateRequests()) {
            throw new \App\Exceptions\NotEnoughRequestsException;
        }
        if (request('type') == Message::TYPE_CHECK_TEST && !auth()->user()->canCreateTestRequests()) {
            throw new \App\Exceptions\NotEnoughTestRequestsException;
        }
        return parent::store();
    }

    protected function beforeSave($object) {
        if (empty($object->id)) {
            $object->from_user_id = auth()->user()->id;
        }

        return $object;
    }

    protected function afterSave($object) {
        if (request()->has('files')) {
            $object->syncFiles(request('files'));
        }

        if (request()->has('images')) {
            $object->syncImages(request('images'));
        }

        return $object;
    }

    public function markRead(MessageMarkReadRequest $request) {
        $request->message->markRead();

        return response()->json('ok');
    }

    public function getReviews() {
        $messages = Message::with('sender')
            ->reviews()
            ->whereOnMainPage(true)
            ->get();

        return response()->json(MessageHistoryItemResource::collection($messages));
    }

    public function setRating(MessageSetRatingRequest $request) {
        $request->message->setRating(request('rating'));

        return response()->json('ok');
    }
}
