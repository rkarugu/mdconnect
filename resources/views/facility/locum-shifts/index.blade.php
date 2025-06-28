@extends('layouts.app')

@section('title', 'Manage Locum Shifts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Locum Shifts</h1>
        <a href="{{ route('facility.locum-shifts.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
            <i class="fas fa-plus mr-2"></i> Create New Shift
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-center">Shift ID</th>
                        <th class="py-3 px-6 text-left">Start Date & Time</th>
                        <th class="py-3 px-6 text-left">End Date & Time</th>
                        <th class="py-3 px-6 text-left">Created</th>
                        <th class="py-3 px-6 text-left">Role</th>
                        <th class="py-3 px-6 text-center">Slots</th>
                        <th class="py-3 px-6 text-center">Rate/hr</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                    @forelse ($shifts as $shift)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-center font-semibold">#{{ $shift->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $shift->start_datetime->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $shift->end_datetime->format('d/m/Y H:i') }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $shift->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-6 text-left">{{ $shift->worker_type }}</td>
                            <td class="py-3 px-6 text-center">{{ $shift->slots_available }}</td>
                            <td class="py-3 px-6 text-center">${{ number_format($shift->pay_rate, 2) }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full @php
                                        $statusClasses = [
                                            'open' => 'text-green-700 bg-green-100',
                                            'filled' => 'text-yellow-700 bg-yellow-100',
                                            'confirmed' => 'text-blue-700 bg-blue-100',
                                            'in_progress' => 'text-indigo-700 bg-indigo-100',
                                            'expired' => 'text-red-700 bg-red-100',
                                        ];
                                    @endphp
                                    {{ $statusClasses[$shift->status] ?? 'text-gray-700 bg-gray-100' }}">
                                    {{ ucfirst($shift->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <a href="{{ route('facility.locum-shifts.show', $shift) }}" class="w-8 h-8 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center mr-2 transform hover:scale-110 transition-transform duration-200" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('facility.locum-shifts.edit', $shift) }}" class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-500 flex items-center justify-center mr-2 transform hover:scale-110 transition-transform duration-200" title="Edit Shift">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('facility.locum-shifts.destroy', $shift) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shift?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-full bg-red-100 text-red-500 flex items-center justify-center transform hover:scale-110 transition-transform duration-200" title="Delete Shift">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500">No locum shifts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $shifts->links() }}
        </div>
    </div>
</div>
@endsection
