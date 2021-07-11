@extends('admin.layouts.admin')

@section('title')
    {{ $account->username ?? $account->email }}'s Audit Logs
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Model</th>
                <th>Fields</th>
                <th>Datetime</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($ledgers as $ledger)
                <tr>
                    <td><a href="{{ $ledger->recordable->getPanelShowUrl() }}">{{ $ledger->recordable->getHumanRecordName() }}</a></td>
                    @if(in_array($ledger->event, ['synced', 'attached', 'detached']))
                        <td>Changed {{ $ledger->pivot["relation"] }}</td>
                    @else
                        <td>{{ implode(', ', array_keys($ledger->getData())) }}</td>
                    @endif
                    <td>{{ $ledger->created_at }}</td>
                    <td><a href="{{ route('front.panel.audits.show', $ledger) }}">Metadata</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
