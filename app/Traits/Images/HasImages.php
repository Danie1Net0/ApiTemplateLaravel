<?php

namespace App\Traits\Images;

use Illuminate\Support\Facades\Storage;

/**
 * Trait HasImages
 * @package App\Traits\Images
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
        if ($this->{$imageableName}->path != 'public/images/default.png')
            Storage::delete($this->{$imageableName}->path);

        $pathImage = Storage::put('public/images', $path);

        $this->{$imageableName}()->update(['path' => $pathImage]);
    }
}
