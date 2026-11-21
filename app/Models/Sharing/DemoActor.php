<?php

namespace App\Models\Sharing;

use Illuminate\Database\Eloquent\Model;

class DemoActor extends Model
{
    protected $table = 'circlo_sharing_actors';

    protected $fillable = ['slug', 'name', 'email', 'student_number'];

    public function initials(): string
    {
        return collect(explode(' ', $this->name))->take(2)
            ->map(fn (string $part): string => mb_substr($part, 0, 1))->implode('');
    }
}
