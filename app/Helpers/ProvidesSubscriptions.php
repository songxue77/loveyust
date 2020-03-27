<?php

namespace App\Helpers;

use App\Models\Subscription;
use App\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ProvidesSubscriptions
{
    /**
     * @return \App\Models\Subscription[]
     */
    public function subscriptions()
    {
        return $this->subscriptionsRelation;
    }

    /**
     * It's important to name the relationship the same as the method because otherwise
     * eager loading of the polymorphic relationship will fail on queued jobs.
     *
     * @see https://github.com/laravelio/laravel.io/issues/350
     */
    public function subscriptionsRelation(): MorphMany
    {
        return $this->morphMany(
            Subscription::class,
            'subscriptionsRelation',
            'subscriptionable_type',
            'subscriptionable_id'
        );
    }

    public function hasSubscriber(User $user): bool
    {
        return $this->subscriptionsRelation()
            ->where('user_id', $user->id())
            ->exists();
    }
}
