@extends('admin.layouts.admin')

@section('title', 'Edit Donation Perk #' . $perk->donation_perks_id)

@section('toolbar')
    <a href="{{ $perk->getAuditListUrl() }}" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-list"></i> Audit</a>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('front.panel.donation-perks.update', $perk) }}" method="post">
                @csrf
                @method('PUT')

                @include('admin.donation-perk._form', ['buttonText' => 'Edit'])
            </form>
        </div>
    </div>
@endsection
