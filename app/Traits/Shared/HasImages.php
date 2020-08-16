<?php

namespace App\Traits\Shared;

use Illuminate\Support\Facades\Storage;

/**
 * Trait HasImages
 * @package App\Traits\Shared
 */
trait HasImages
{
    /**
     * @param string $imageableName
     * @param object $path
     * @return void
     */
    public function updateImage(string $imageableName, object $path): void
    {
        if (isset($this->{$imageableName}->path) && $this->{$imageableName}->path != 'public/images/default.png')
            Storage::delete($this->{$imageableName}->path);

        $pathImage = Storage::put('public/images', $path);

        $this->{$imageableName}()->{$this->{$imageableName} ? 'update' : 'create'}(['path' => $pathImage]);
    }

    /**
     * @param string $imageableName
     */
    public function deleteImage(string $imageableName): void
    {
        if (isset($this->{$imageableName}->path) && $this->{$imageableName}->path != 'public/images/default.png')
            Storage::delete($this->{$imageableName}->path);

        $this->{$imageableName}()->delete();
    }
}
