@extends('layouts.app')

@section('title', 'Facility Wallet')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Facility Wallet: {{ $wallet->medicalFacility->name ?? 'Unknown Facility' }}</h1>
<div class="card">
    <div class="card-body">
        <h4>Balance: {{ number_format($wallet->balance, 2) }}</h4>
    </div>
</div>

<div class="card">
    <div class="card-header">Transactions</div>
    <div class="card-body p-0">
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wallet->transactions as $txn)
                <tr>
                    <td>{{ $txn->id }}</td>
                    <td>{{ $txn->type }}</td>
                    <td>{{ number_format($txn->amount,2) }}</td>
                    <td>{{ $txn->reference_type }} #{{ $txn->reference_id }}</td>
                    <td>{{ $txn->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
