@extends('admin.layouts.admin')

@section('title', 'Donation #' . $donation->donation_id)

@section('toolbar')
    <a href="{{ $donation->getAuditListUrl() }}" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-list"></i> Audit</a>
@endsection

@section('body')
    <div class="row row-cols-md-2">
        <div class="col">
            <div class="card card-default">
                <div class="card-header d-flex justify-content-between">
                    <span>Details</span>
                    <a href="{{ route('front.panel.donations.edit', $donation) }}" class="btn btn-outline-primary btn-sm py-0">
                        <i class="fas fa-pencil-alt"></i> Edit
                    </a>
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            ID
                        </dt>
                        <dd class="col-md-9">
                            {{ $donation->donation_id }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Amount
                        </dt>
                        <dd class="col-md-9">
                            ${{ number_format($donation->amount, 2) }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Donator
                        </dt>
                        <dd class="col-md-9">
                            @isset($donation->account)
                                <a href="{{ route('front.panel.accounts.show', $donation->account->account_id) }}">
                                    {{ $donation->account->username ?? '(Unset)' }}
                                </a>
                            @else
                                Guest
                            @endif
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Date
                        </dt>
                        <dd class="col-md-9">
                            {{ $donation->created_at }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="col">
            <div class="card mt-2 mt-md-0 card-default">
                <div class="card-header d-flex justify-content-between">
                    <span>Assigned Perks</span>
                    <a href="{{ route('front.panel.donation-perks.create') }}?donation_id={{$donation->getKey()}}" class="btn btn-outline-primary btn-sm py-0">
                        <i class="fas fa-plus"></i> Add
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table highlight-linked mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Recipient</th>
                            <th>Starts On</th>
                            <th>Ends On</th>
                            <th>Active?</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($donation->perks as $perk)
                            <tr id="perk-{{ $perk->donation_perks_id }}">
                                <td>
                                    {{ $perk->donation_perks_id }}
                                </td>
                                <td>
                                    <a href="{{ route('front.panel.accounts.show', $perk->account->account_id) }}">
                                        {{ $perk->account->username ?: '(Unset)' }}
                                    </a>
                                    @if($perk->account->account_id != $donation->account->account_id)
                                        <span class="badge bg-light text-dark ms-2"><i class="fas fa-gift"></i> Gifted</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $perk->created_at }}
                                </td>
                                <td>
                                    @if($perk->is_lifetime_perks)
                                        <strong>Lifetime</strong>
                                    @else
                                        {{ $perk->expires_at }}
                                    @endif
                                </td>
                                <td>
                                    <i class="{{ $perk->is_active ? 'text-success fas fa-check' : 'text-danger fas fa-times' }}"></i>
                                </td>
                                <td class="actions align-middle">
                                    <a href="{{ route('front.panel.donation-perks.edit', $perk) }}" class="btn btn-link btn-sm p-0">Edit</a>
                                    <form method="post" class="d-inline" action="{{ route('front.panel.donation-perks.destroy', $perk) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center">No associated perks</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
