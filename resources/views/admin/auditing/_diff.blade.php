<table class="table mb-0 {{ $extraClasses ?? '' }}">
    <tbody>
    @foreach($ledger->modified as $modifiedProp)
        <tr>
            <td style="width:33%;">{{ $modifiedProp }}</td>
            <td style="width:33%;" class="table-danger">
                <i class="fas fa-minus me-1"></i>
                @if($prevLedger == null)
                    <span class="text-muted fst-italic">Unknown</span>
                @else
                    @include('admin.auditing._property', ['value' => $prevLedger['properties'][$modifiedProp]])
                @endif
            </td>
            <td style="width:33%;" class="table-success">
                <i class="fas fa-plus me-1"></i>
                @include('admin.auditing._property', ['value' => $ledger['properties'][$modifiedProp]])
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

