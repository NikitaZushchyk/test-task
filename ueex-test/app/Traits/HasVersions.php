<?php

namespace App\Traits;

use App\Models\Version;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVersions
{
    public static function bootHasVersions(): void
    {
        static::created(function ($model) {
            $model->createVersion();
        });

        static::updated(function ($model) {
            $model->createVersion();
        });
    }

    public function versions(): MorphMany
    {
        return $this->morphMany(Version::class, 'versionable')->orderBy('version');
    }

    public function currentVersionNumber(): int
    {
        return $this->versions()->max('version') ?? 0;
    }

    protected function createVersion(): void
    {
        $lastVersion = $this->currentVersionNumber();

        $this->versions()->create([
            'version' => $lastVersion + 1,
            'data' => $this->getAttributes(),
        ]);
    }
}
