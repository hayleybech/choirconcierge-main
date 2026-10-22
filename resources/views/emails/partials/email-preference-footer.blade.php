---

@if($emailPreferenceLinks['mandatory'])
You are receiving this email because **{{ $emailPreferenceLinks['category'] }}** emails for {{ $emailPreferenceLinks['context'] }} are mandatory.
@else
You are receiving this email because you are subscribed to **{{ $emailPreferenceLinks['category'] }}** emails for {{ $emailPreferenceLinks['context'] }}.
@endif

[Manage your email preferences]({{ $emailPreferenceLinks['manage'] }})@if(isset($emailPreferenceLinks['unsubscribe'])) · [Unsubscribe from {{ $emailPreferenceLinks['category'] }}]({{ $emailPreferenceLinks['unsubscribe'] }})@endif
