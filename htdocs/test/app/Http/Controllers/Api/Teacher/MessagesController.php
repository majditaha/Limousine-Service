<?php namespace App\Http\Controllers\Api\Teacher;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\MessageReadRequest;
use \App\Http\Requests\MessageWriteRequest;
use \App\Http\Requests\MessageAnswerRequest;
use \App\Http\Requests\MessageMarkReadRequest;
use \App\Http\Requests\MessageMarkTakenRequest;
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
                    ->groupBy('uid');

                if (request()->bool('active')) {
                    $subquery->having(\DB::raw('COUNT(id)'), 1);
                }
                else {
                    $subquery->having(\DB::raw('COUNT(id)'), '>', 1);
                }
            });

            $query->whereTeacherId(auth()->user()->id)
                ->whereNotNull('finished_at');
        }
        else {
            $query = $query->whereIn('id', function ($subquery) {
                $subquery->from(with(new Message)->getTable())
                    ->select(
                        \DB::raw('(ARRAY_AGG(id ORDER BY created_at ASC))[1]')
                    )
                    ->groupBy('uid');
            });

            $query = $query->whereHas('practice', function ($subquery) {
                $subquery->whereIn('discipline_id', auth()->user()->disciplineIds);
            })->where(function ($subquery) {
                $subquery->whereNull('teacher_id')
                    ->orWhere('teacher_id', auth()->user()->id);
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

        if (request()->filled('created_at_from')) {
            $query = $query->where('created_at', '>=', request('created_at_from'));
        }

        if (request()->filled('created_at_to')) {
            $query = $query->where('created_at', '<=', request('created_at_to'));
        }

        if (request()->filled('available')) {
            $query = $query->whereNull('finished_at')
                ->where(function ($subquery) {
                    $subquery->whereNull('taken_at')
                        ->orWhere('taken_at', '>', \Carbon\Carbon::now()->subMinutes(\App\Setting::getValue('teacherAnswerTime')));
                });
        }

        return $query;
    }

    public function store() {
        if (request('type') == Message::TYPE_CHECK_REQUEST && !auth()->user()->canCreateRequests()) {
            throw new \App\Exceptions\NotEnoughRequestsException;
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

    public function answer(MessageAnswerRequest $request) {
        $message = $request->message->getHistory()->last();

        $message->markAnswered();

        $answer = $message->answer(auth()->user(), request('content'));

        if ($request->has('files')) {
            $answer->syncFiles(request('files'));
        }

        if ($request->has('images')) {
            $answer->syncImages(request('images'));
        }

        $answer->receiver->sendMessageEmail($answer);

        return response()->json('ok');
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

    public function markTaken(MessageMarkTakenRequest $request) {
        if (!$request->message->canTakeMessage(auth()->user()->id)){
            return response()->json([
                'errors' => [
                    'message' => 'Данный запрос на проверку недоступен',
                ],
            ], 403);
        }

        if ($request->message->taken_at == null){
                $request->message->markTaken(auth()->user()->id);
        }
        return response()->json('ok');
    }
}
