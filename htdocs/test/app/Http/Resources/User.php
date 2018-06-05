<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\DisciplineSubscription as DisciplineSubscriptionResource;
use App\Http\Resources\Discipline as DisciplineResource;

class User extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $attrs = array_except(parent::toArray($request), [
            'api_token',
            'created_at',
            'updated_at',
            'vkontakte_id',
            'deleted_at',
        ]);

        $attrs['registered_date'] = $this->created_at->format('Y-m-d');
        $attrs['has_password'] = $this->hasPassword();
        $attrs['discipline_ids'] = $this->disciplineIds;
        $attrs['is_valid'] = $this->isValid();

        if ($this->isTeacher()) {
            $attrs['rating'] = $this->getRating();
            $attrs['earliest_message_taken_at'] = $this->getEarliestMessageTakenAt();
        }
        else {
            $attrs['discipline_subscriptions'] = DisciplineSubscriptionResource::collection($this->disciplineSubscriptions);
            $attrs['need_to_ask_desired_minutes'] = $this->isNeededToAskDesiredMinutes();
            $attrs['timed_out'] = $this->isTimedOut();
            $attrs['is_subscription_active'] = $this->isSubscriptionActive();
            $attrs['can_create_requests'] = $this->canCreateRequests();
            $attrs['can_create_test_requests'] = $this->canCreateTestRequests();
        }

        return $attrs;
    }
}
