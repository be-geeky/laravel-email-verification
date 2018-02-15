@component('mail::message')

Click the button below to verify your email address and finish setting up your profile.

@component('mail::button', ['url' => url('/verifyemail/'.$email_token) ])
Verify Email Address
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent