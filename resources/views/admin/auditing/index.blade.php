@extends('admin.layouts.admin')

@section('title', $humanLabel . ' Audit Logs')

@section('toolbar')
    <a href="{{ $auditingModel->getPanelShowUrl() }}" class="btn btn-outline-secondary btn-sm"><i
            class="fas fa-arrow-left"></i> Back</a>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Date &amp; Time</th>
                <th>Changed By</th>
                <th>Change Data</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($ledgers as $ledger)
                <tr id="ledger-{{ $ledger->id }}">
                    <td>{{ $ledger->created_at }}</td>
                    <td>
                        @if(is_null($ledger->accountable))
                            @if($ledger->context == Altek\Accountant\Context::CLI)
                                <span class="text-muted fst-italic">Console</span>
                            @else
                                <span class="badge bg-danger">Unknown</span>
                            @endif
                        @else
                            <a href="{{ route('front.panel.accounts.show', $ledger->accountable) }}">
                                {{ $ledger->accountable->username ?: '(Unset)' }}
                            </a>
                        @endif
                    </td>
                    @if(in_array($ledger->event, ['synced', 'attached', 'detached']))
                        <td>
                            @include('admin.auditing._pivot', ['ledger' => $ledger])
                        </td>
                    @else
                        <td class="p-0">
                            @include('admin.auditing._diff', ['ledger' => $ledger, 'prevLedger' => $loop->last ? null : $ledgers[$loop->index + 1]])
                        </td>
                    @endif
                    <td>
                        <a href="{{ route('front.panel.audits.show', $ledger) }}">Metadata</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No audited events have been recorded yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
