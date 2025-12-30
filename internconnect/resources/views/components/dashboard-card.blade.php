<div class="card">
    @if(isset($icon))
        <div style="font-size:20px; margin-bottom:8px">{!! $icon !!}</div>
    @endif
    <div style="font-size:22px; font-weight:700">{{ $count ?? 0 }}</div>
    <div style="color:#6b7280">{{ $label ?? '' }}</div>
    @if(isset($slot))
        <div style="margin-top:10px">{{ $slot }}</div>
    @endif
</div>
