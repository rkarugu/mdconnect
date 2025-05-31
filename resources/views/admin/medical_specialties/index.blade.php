@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-900">Medical Specialties</h1>
        <a href="{{ route('admin.medical_specialties.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i>
            Create Specialty
        </a>
    </div>

    <!-- Specialties List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workers</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($specialties as $specialty)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $specialty->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ Str::limit($specialty->description, 100) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $specialty->medical_workers_count ?? 0 }} worker(s)</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" onclick="showEditModal({{ $specialty->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(($specialty->medical_workers_count ?? 0) === 0)
                                <form action="{{ route('admin.medical_specialties.destroy', $specialty) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this specialty?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            No medical specialties found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $specialties->links() }}
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="specialtyModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <form id="specialtyForm" method="POST">
            @csrf
            <div class="px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Medical Specialty</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                <button type="button" onclick="hideModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('specialtyModal');
    const form = document.getElementById('specialtyForm');
    const modalTitle = document.getElementById('modalTitle');
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');

    function showCreateModal() {
        modalTitle.textContent = 'Add Medical Specialty';
        form.action = '{{ route('admin.medical_specialties.store') }}';
        form.method = 'POST';
        nameInput.value = '';
        descriptionInput.value = '';
        modal.classList.remove('hidden');
    }

    function showEditModal(id) {
        modalTitle.textContent = 'Edit Medical Specialty';
        form.action = `/admin/medical_specialties/${id}`;
        form.method = 'POST';
        
        // Add method override for PUT request
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';

        // Fetch specialty data
        fetch(`/admin/medical_specialties/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                nameInput.value = data.name;
                descriptionInput.value = data.description;
                modal.classList.remove('hidden');
            });
    }

    function hideModal() {
        modal.classList.add('hidden');
        form.reset();
    }

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideModal();
        }
    });
</script>
@endpush
