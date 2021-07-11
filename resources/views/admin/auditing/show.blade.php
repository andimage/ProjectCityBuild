@extends('admin.layouts.admin')

@section('title', 'Audit Entry Metadata')

@section('body')
    <div class="row">
        <div class="col-md-6">
            @if(in_array($ledger->event, ['synced', 'attached', 'detached']))
                <div class="card mb-2">
                    <div class="card-header">
                        Pivot Data
                    </div>
                    <div class="card-body">
                        @include('admin.auditing._pivot', ['ledger' => $ledger])
                    </div>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    Full Model Data
                </div>
                <dl class="kv">
                    @php($modified = array_keys($ledger->getData()))
                    @foreach($ledger->getData(true) as $key => $value)
                    <div class="row g-0">
                        <dt class="col-md-3">
                            {{ $key }}
                            @if(in_array($key, $modified) )
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Modified">
                                    <i class="fas fa-asterisk text-primary"></i>
                                </span>
                            @endif
                        </dt>
                        <dd class="col-md-9">
                            @include('admin.auditing._property', ['value' => $value])
                        </dd>
                    </div>
                    @endforeach
                </dl>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Author Data
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Context
                        </dt>
                        <dd class="col-md-9">
                            @if($ledger->context == 1)
                                <span class="badge bg-warning">Testing</span>
                            @elseif($ledger->context == 2)
                                <span class="badge bg-info">CLI</span>
                            @elseif($ledger->context == 4)
                                <span class="badge bg-secondary">Web</span>
                            @endif
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Author
                        </dt>
                        <dd class="col-md-9">
                            @if(is_null($ledger->accountable_id))
                                @if($ledger->context == 2)
                                    <span class="text-muted">CLI/span>
                                @else
                                    <span class="text-danger">Not recorded</span>
                                @endif
                            @elseif($ledger->accountable_type == "account")
                                <a href="{{ route('front.panel.accounts.show', $ledger->accountable) }}">
                                    {{ $ledger->accountable->username ?: '(Unset)' }}
                                </a>
                            @else
                                <div class="fw-bold text-danger">Unexpected accountable entity</div>
                                {{ $ledger->accountable_type }}: {{ $ledger->accountable_id }}
                            @endif
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Timestamp
                        </dt>
                        <dd class="col-md-9">
                            {{ $ledger->created_at }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Event
                        </dt>
                        <dd class="col-md-9">
                            {{ $ledger->event }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            URL
                        </dt>
                        <dd class="col-md-9">
                            {{ $ledger->url }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            IP Address
                        </dt>
                        <dd class="col-md-9">
                            {{ $ledger->ip_address }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            User Agent
                        </dt>
                        <dd class="col-md-9">
                            {{ $ledger->user_agent }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection
