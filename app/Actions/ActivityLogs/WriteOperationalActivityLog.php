<?php

namespace App\Actions\ActivityLogs;

use App\Models\ActivityLog;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class WriteOperationalActivityLog
{
    public function handle(
        ?int $actorId,
        Model $target,
        string $action,
        string $description,
        array $properties = [],
    ): ActivityLog {
        return ActivityLog::query()->create([
            'actor_id' => $actorId,
            'target_type' => $target::class,
            'target_id' => $target->getKey(),
            'action' => $action,
            'description' => $description,
            'properties' => $this->normalize($properties),
        ]);
    }

    public function snapshot(Model $model, array $attributes): array
    {
        $snapshot = [
            'id' => $model->getKey(),
        ];

        foreach ($attributes as $attribute) {
            $snapshot[$attribute] = $this->normalizeValue(data_get($model, $attribute));
        }

        return $snapshot;
    }

    protected function normalize(array $properties): array
    {
        $normalized = [];

        foreach ($properties as $key => $value) {
            $normalized[$key] = $this->normalizeValue($value);
        }

        return $normalized;
    }

    protected function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if ($value instanceof Model) {
            return $this->snapshot($value, ['id']);
        }

        if ($value instanceof Collection) {
            return $value->all();
        }

        if (is_array($value)) {
            $normalized = [];

            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalizeValue($item);
            }

            return $normalized;
        }

        return $value;
    }
}
