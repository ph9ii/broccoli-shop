@component('mail::message')
Hello {{$user->name}},

Please confirm your email using this link: 

@component('mail::button', ['url' => route('verify', ['token' => $user->verification_token]) ])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent