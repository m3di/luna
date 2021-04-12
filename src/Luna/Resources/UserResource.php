<?php

namespace App\Luna\Resources;

use App\Models\User;
use Luna\Resources\Resource;
use Luna\Types\Email;
use Luna\Types\ID;
use Luna\Types\Password;
use Luna\Types\Text;

class UserResource extends Resource
{
    public $model = User::class;

    public $title = 'name';

    protected $searchable = ['name', 'email'];

    protected $singular = 'کاربر';
    protected $plural = 'کاربران';

    function fields()
    {
        /** @var User $user */
        $user = auth()->user();

        return array_filter([
            ID::make(),
            Text::make('name')->title('نام')->sortable()->rules('required'),
            Email::make('email')->title('ایمیل')->sortable()->rules('required', 'unique'),
            Password::make('password')->title('کلمه عبور')->creationRules('required')->updateRules('nullable'),
        ]);
    }
}
