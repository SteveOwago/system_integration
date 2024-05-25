@component('mail::message')
#Course Payment Confirmation Status
{{ $message }}
<br>
Thank You,<br>
{{ env('APP_NAME') }}
@endcomponent
