@component('mail::message')
# {{ $details['title'] }}

{{ $details['body'] }}

@component('mail::button', ['url' => config('app.url')])
Dashboard
@endcomponent

Error ID: {{ $details['error'] }}
@endcomponent
