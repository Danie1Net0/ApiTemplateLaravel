<?php

namespace App\Traits\Telephones;

/**
 * Trait HasTelephones
 * @package App\Traits\Users
 */
trait HasTelephones
{
    /**
     * @param array $telephones
     */
    public function storeTelephones(array $telephones): void
    {
        foreach ($telephones as $telephone)
            $this->telephones()->create([
                'number' => $telephone['number'],
                'type' => $telephone['type']
            ]);
    }

    /**
     * @param array $telephones
     */
    public function updateTelephones(array $telephones): void
    {
        $this->telephones()->delete();
        $this->storeTelephones($telephones);
    }
}
