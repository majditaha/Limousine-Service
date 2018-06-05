<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\UserReadRequest;
use \App\Http\Requests\UserWriteRequest;
use \App\Http\Requests\UserDeleteRequest;

class UsersController extends RestController {

    public $model = 'User';

    protected $indexRequest = UserReadRequest::class;
    protected $showRequest = UserReadRequest::class;
    protected $storeRequest = UserWriteRequest::class;
    protected $updateRequest = UserWriteRequest::class;
    protected $destroyRequest = UserDeleteRequest::class;

    protected function generateMetadata() {
        return [
            'cities' => \App\City::with('schools')->get(),
            'types' => \App\User::getTypes(),
            'inactiveCount' => \App\User::inactive()->count(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        if (request()->filled('email')) {
            $email = request('email');
            $query = $query->where('email', 'ilike', "%{$email}%");
        }

        if (request()->filled('gender')) {
            $query = $query->whereGender(request('gender'));
        }

        if (request()->has('active')) {
            $query = $query->whereActive(request('active'));
        }

        if (request()->filled('birth_date_from')) {
            $query = $query->where('birth_date', '>=', request('birth_date_from'));
        }

        if (request()->filled('birth_date_to')) {
            $query = $query->where('birth_date', '<=', request('birth_date_to'));
        }

        if (request()->filled('role')) {
            $query = $query->whereRole(request('role'));
        }

        if (request()->filled('city_id')) {
            $query = $query->whereCityId(request('city_id'));
        }

        if (request()->filled('school_id')) {
            $query = $query->whereSchoolId(request('school_id'));
        }

        if (request()->filled('grade')) {
            $query = $query->whereGrade(request('grade'));
        }

        if (request()->filled('grade_name')) {
            $query = $query->whereGradeName(request('grade_name'));
        }

        return $query;
    }

    public function beforeSave($object) {
        if (empty($object->id)) {
            $object->email_confirmed = true;
        }

        if (request()->has('password')) {
            $object->password = bcrypt(request('password'));
        }

        return $object;
    }
}
