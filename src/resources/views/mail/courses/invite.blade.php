@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.website_url')])
Facilittei
@endcomponent
@endslot

# {{ trans('messages.register_greeting') }}, {{ $invite->name }}!

{{ trans('messages.register_thanks') }}

{{ trans('messages.register_confirm') }}

@slot('subcopy')
@component('mail::subcopy')
<span class="break-all">
    <a href="{{ config('app.client_url') . '/invites/' . $invite->token  }}">
        {{ config('app.client_url') . '/invites/' . $invite->token }}
    </a>
</span>
@endcomponent
@endslot

@component('mail::button', ['url' => config('app.client_url') . '/invites/' . $invite->token])
{{ trans('ui.confirm') }}
@endcomponent

@slot('footer')
@component('mail::footer')
{{ trans('ui.thanks') }}, {{ config('app.name') }}
@endcomponent
@endslot

@endcomponent
