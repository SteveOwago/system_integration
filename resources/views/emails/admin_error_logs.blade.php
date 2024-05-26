@component('mail::message')
#Error Log
{{ $data['message'] }}
<br>
Thank You,<br>
{{ env('APP_NAME') }}
@endcomponent
