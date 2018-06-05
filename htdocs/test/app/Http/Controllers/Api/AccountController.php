<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use App\Http\Requests\SetTheoryFinishedRequest;
use App\Http\Requests\SetPracticeFinishedRequest;
use App\Http\Requests\SetSectionFinishedRequest;

use App\Plan;

class AccountController extends Controller
{
    public function update() {
        $user = auth()->user();

        $attributes = [
            'name' => 'Полное имя',
        ];

        $messages = [
            'passport_file.required' => 'Скан паспорта должен быть загружен',
            'empl_history_file.required' => 'Скан трудовой книжки должен быть загружен',
            'subtype.required' => 'Необходимо указать кто вы',
            'accepted_agreement.accepted' => 'Вы должны принять пользовательское соглашение',
        ];

        $rules = [
            'accepted_agreement' => ['Accepted'],
            'name' => ['Required'],
            'email' => ['Required'],
            'gender' => ['Required', 'Integer'],
            'role' => [
                'Required',
            ],
            'birth_date' => ['Nullable', 'Date'],
            'city_id' => ['Nullable', 'Integer'],
            'school_id' => ['Nullable', 'Integer'],
            'grade' => [],
            'grade_name' => ['Nullable', 'String'],
            'photo' => ['Nullable', 'Url'],
            'discipline_ids' => 'Array',
        ];

        if (!auth()->user()->isAdmin()) {
            $rules['role'][] = Rule::in(['user', 'teacher']);
        }

        if (auth()->user()->isTeacher()) {
            $rules['phone'] = ['Required'];
            $rules['passport_file'] = ['Required', 'Url'];
            $rules['empl_history_file'] = ['Required', 'Url'];
        }

        if (auth()->user()->isUser()) {
            $rules['subtype'] = ['Required'];
        }

        if (!$user->hasPassword() || request()->filled('password')) {
            $rules['password'] = ['Confirmed', 'Min:6'];
        }

        $data = request()->validate($rules, $messages, $attributes);

        if (request('role') == 'user') {
            $data['passport_file'] = null;
            $data['empl_history_file'] = null;
            $data['active'] = true;
        }

        if ($user->email) {
            unset($data['email']);
        }

        if (request('city_id') == -1) {
            $city = new \App\City;
            $city->name = request('new_city');
            $city->save();

            $data['city_id'] = $city->id;
        }

        if (request('school_id') == -1) {
            $school = new \App\School;
            $school->name = request('new_school');
            $school->city_id = $data['city_id'];
            $school->save();

            $data['school_id'] = $school->id;
        }

        $addedEmail = request()->filled('email') && empty($user->email);

        $user->fill($data);

        if (request()->filled('password')) {
            $user->password = bcrypt(request('password'));
        }

        $user->save();

        $user->disciplines()->sync($data['discipline_ids']);

        if ($addedEmail) {
            $user->sendWelcomeEmail();
        }

        return response()->json($user);
    }

    public function setDesiredHours() {
        $user = auth()->user();

        if (!$user->isNeededToAskDesiredMinutes()) {
            return response()->json('ok');
        }
        $minutes = 0;

        if (request()->filled('hours')) {
            $minutes += request('hours') * 60;
        }

        if (request()->filled('minutes')) {
            $minutes += request('minutes');
        }

        $user->desired_minutes_to_spend = $minutes;
        $user->desired_minutes_set_at = \Carbon\Carbon::now();
        $user->presence_minutes = 0;
        $user->presence_updated_at = null;
        $user->save();

        return response()->json('ok');
    }

    public function setTheoryFinished(SetTheoryFinishedRequest $request) {
        $user = auth()->user();
        $theory = $request->theory;

        if ($request->is_training && $theory->theoryPractices->count() && !$theory->practicesFinished(auth()->user()->id)) {
            return response()->json([
                'errors' => [
                    'theory' => 'По данной теории пройдены не все задания',
                ],
            ], 403);
        }

        if (!$request->is_training && !$theory->isFinished($user)) {
            $user->finishedTheories()->create(['theory_id' => $theory->id, 'is_training' => false]);
        }
        else if ($request->is_training && !$theory->isInTrainingFinished($user)) {
            $user->finishedTrainingTheories()->create(['theory_id' => $theory->id, 'is_training' => true]);
        }

        return response()->json('ok');
    }

    public function setPracticeFinished(SetPracticeFinishedRequest $request) {
        $user = auth()->user();

        $request->practice->setFinished($user);

        $user->saveAnswers($request->practice, $request->answers, $request->is_training);

        return response()->json('ok');
    }

    public function setSectionFinished(SetSectionFinishedRequest $request) {
        $user = auth()->user();

        $section = $request->section;

        if (!$section->arePracticesFinished($section->practicesOfTheoryType, auth()->user())) {
            return response()->json([
                'errors' => [
                    'section' => 'В данном блоке выполнены не все задания',
                ],
            ], 403);
        }

        if (!$section->areTrainingsFinished(auth()->user())) {
            return response()->json([
                'errors' => [
                    'section' => 'В данном блоке не пройдены финальные задачи',
                ],
            ], 403);
        }

        $request->section->setFinished($user);

        return response()->json('ok');
    }

    public function logPresence() {
        $user = auth()->user();

        if (!$user->canUpdatePresence()) {
            return response()->json(['presence_minutes' => $user->presence_minutes]);
        }

        $user->presence_minutes += 1;
        $user->presence_updated_at = \Carbon\Carbon::now();

        $user->save();

        return response()->json(['presence_minutes' => $user->presence_minutes]);
    }

    public function getAvailableResources() {
        $money = auth()->user()->getAvailableMoney();
        $requests = auth()->user()->getAvailableRequests();
        $tests = auth()->user()->getAvailableTests();

        return response()->json([
            'money' => $money,
            'requests' => $requests,
            'tests' => $tests,
        ]);
    }

    public function addMoney() {
        request()->validate([
            'amount' => ['Required', 'Numeric', 'Min:0'],
        ]);

        $amount = request()->amount;

        auth()->user()->addMoney($amount);

        return response()->json('ok');
    }

    public function purchasePlan(Plan $plan) {
        $disciplineIds = request('discipline_ids');
        $selectedCount = request('selected_count');
        auth()->user()->purchasePlan($plan, $disciplineIds, $selectedCount);
        return response()->json('ok');
    }
}
