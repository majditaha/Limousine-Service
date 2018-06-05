<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\MessageReadRequest;
use \App\Http\Requests\MessageWriteRequest;
use \App\Http\Requests\MessageDeleteRequest;

class MessagesController extends RestController {

    public $model = 'Message';

    protected $indexRequest = MessageReadRequest::class;
    protected $showRequest = MessageReadRequest::class;
    protected $storeRequest = MessageWriteRequest::class;
    protected $updateRequest = MessageWriteRequest::class;
    protected $destroyRequest = MessageDeleteRequest::class;

    protected $allowedWith = ['ratingMessage'];

    protected function generateMetadata() {
        return [
            'users' => \App\User::get(),
            'types' => \App\Message::getTypes(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('from_user_id')) {
            $query = $query->where('from_user_id', request('from_user_id'));
        }

        if (request()->filled('to_user_id')) {
            $query = $query->where('to_user_id', request('to_user_id'));
        }

        if (request()->filled('content')) {
            $content = request('content');
            $query = $query->where('content', 'ilike', "%{$content}%");
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

        if (request()->has('read')) {
            $query = $query->whereNotNull('read_at');
        }

        if (request()->filled('rating')) {
            $query = $query->whereRating(request('rating'));
        }

        return $query;
    }

    public function getHistory(MessageReadRequest $request) {
        $history = $request->message->getHistory();
        return response()->json($history);
    }

    public function afterSave($object) {
        if (request()->filled('answer')) {
            $message = $object->getHistory()->last();
            $message->answer(auth()->user(), request('answer'));
        }
        return $object;
    }

    public function destroy($item) {
        app()->make($this->destroyRequest);

        $message = request()->message;

        return \DB::transaction(function() use ($message) {
            $messages = $message->getHistory()->each->delete();
            return response()->json();
        });
    }
}
