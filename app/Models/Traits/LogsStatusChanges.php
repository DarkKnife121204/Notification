<?php

namespace App\Models\Traits;

trait LogsStatusChanges
{
    protected static function bootLogsStatusChanges(): void
    {
        static::created(function ($model) {
            if (! $model->status) {
                return;
            }

            $model->logs()->create([
                'to_status' => $model->status->value,
            ]);
        });

        static::updated(function ($model) {
            if (! $model->wasChanged('status')) {
                return;
            }

            $model->logs()->create([
                'from_status' => $model->getOriginal('status'),
                'to_status' => $model->status->value,
            ]);
        });
    }
}
