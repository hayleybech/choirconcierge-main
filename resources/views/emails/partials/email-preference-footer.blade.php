---

@if($emailPreferenceLinks['mandatory'])
<small style="font-size: 11px; line-height: 1.4;">You are receiving this email because **{{ $emailPreferenceLinks['category'] }}** emails for {{ $emailPreferenceLinks['context'] }} are mandatory.</small>
@else
<small style="font-size: 11px; line-height: 1.4;">You are receiving this email because you are subscribed to **{{ $emailPreferenceLinks['category'] }}** emails for {{ $emailPreferenceLinks['context'] }}.</small>
@endif

<small style="font-size: 11px; line-height: 1.4;">[Manage your email preferences]({{ $emailPreferenceLinks['manage'] }})@if(isset($emailPreferenceLinks['unsubscribe'])) · [Unsubscribe from {{ $emailPreferenceLinks['category'] }}]({{ $emailPreferenceLinks['unsubscribe'] }})@endif</small>
