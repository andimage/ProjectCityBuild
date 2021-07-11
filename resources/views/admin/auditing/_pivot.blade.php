<div>
    <strong>Set {{ Str::title($ledger->pivot["relation"]) }}:</strong>
    @foreach($ledger->pivot["properties"] as $related)
        @foreach($related as $key => $id)
            @if($key != "account_id")
                <span data-pcb-pivot-entry data-pivot-type="{{ $ledger->pivot["relation"] }}" data-pivot-pk="{{ $id }}">{{ $id }}</span>@unless($loop->parent->last), @endunless
            @endif
        @endforeach
    @endforeach
</div>
