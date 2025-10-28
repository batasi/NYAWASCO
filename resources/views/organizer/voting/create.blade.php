@extends('layouts.app')

@section('title', $title ?? 'Create Voting Contest')

@section('content')
<div class="min-h-screen modal-header flex items-center justify-center py-10">
    <div class="modal-bg shadow-lg rounded-lg w-full max-w-3xl p-6">
        <h1 class="text-2xl font-bold mb-2" style="color: rgba(198, 0, 238, 1);">Create Voting Contest</h1>
        <p class="text-gray-200 mb-5 text-sm">Create a contest under a category and set voting rules (start/end dates, limits).</p>

    

        <form method="POST" action="{{ route('voting.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Contest Info --}}
            <div class="space-y-3">
                <div>
                    <label for="title" class="text-sm font-medium text-purple-500">Contest Title</label>
                    <input id="title" name="title" value="{{ old('title') }}" required
                        class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm"
                        placeholder="Best Male Artist 2025">
                </div>

                {{-- Voting Category --}}
                <div>
                    <label for="category_id" class="text-sm font-medium text-purple-500">Voting Category</label>
                    <div class="flex gap-2">
                        <select id="category_id" name="category_id" required
                            class="flex-1 border-gray-300 rounded-md px-3 py-1.5 text-sm">
                            <option value="">-- Select Voting Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="addVotingCategoryBtn"
                            class="px-3 py-1.5 bg-purple-500 text-white rounded-md text-sm hover:bg-indigo-700">+</button>
                    </div>
                </div>

                <div>
                    <label for="description" class="text-sm font-medium text-purple-500">Description (optional)</label>
                    <textarea id="description" name="description" rows="2"
                        class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm">{{ old('description') }}</textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="start_date" class="text-sm font-medium text-purple-500">Start Date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}"
                            class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="text-sm font-medium text-purple-500">End Date</label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}"
                            class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm">
                    </div>
                </div>

                {{-- NEW: Contest Photo/Banner --}}
                <div>
                    <label for="featured_image" class="text-sm font-medium text-purple-500">Contest Photo / Banner (optional)</label>
                    <input id="featured_image" name="featured_image" type="file" accept="image/*"
                        class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm">

                    <!-- Preview -->
                    <img id="featuredImagePreview" src="#" alt="Image Preview"
                        class="mt-3 rounded-md w-48 h-32 object-cover hidden border border-gray-300">
                </div>

                {{-- NEW: Contest Amount / Fee --}}
                <div>
                    <label for="amount" class="text-sm font-medium text-purple-500">Amount (optional)</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0"
                        value="{{ old('amount') }}"
                        class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm"
                        placeholder="Enter amount (e.g. 5.00)">
                    <p class="text-xs text-gray-200 mt-1">Leave empty if there's no fee for this contest.</p>
                </div>

                {{-- Max votes: keep existing input but add toggle for unlimited vs limited --}}
                <div class="grid grid-cols-1 gap-2 mt-2">
                    <label class="text-sm font-medium text-purple-500">Voting Limit</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center text-sm">
                            <input type="radio" name="votes_limit_type" value="limited" id="votes_limited" {{ old('votes_limit_type', 'limited') == 'limited' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <span class="ml-2">Limited</span>
                        </label>
                        <label class="flex items-center text-sm">
                            <input type="radio" name="votes_limit_type" value="unlimited" id="votes_unlimited" {{ old('votes_limit_type') == 'unlimited' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <span class="ml-2">Unlimited</span>
                        </label>
                    </div>

                    <div id="maxVotesWrapper" class="mt-2">
                        <label for="max_votes_per_user" class="text-sm font-medium text-purple-500">Max Votes Per User</label>
                        <input id="max_votes_per_user" name="max_votes_per_user" type="number" min="1"
                            value="{{ old('max_votes_per_user', 1) }}"
                            class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm">
                        <p class="text-xs text-gray-200 mt-1">If 'Unlimited' is selected above, this value will be ignored.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="flex items-center gap-2 mt-6">
                        <input id="is_featured" name="is_featured" type="checkbox" value="1"
                            {{ old('is_featured') ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="is_featured" class="text-purple-500 text-sm">Feature this contest</label>
                    </div>

                    <div class="flex items-center gap-2 mt-6">
                        <label class="flex items-center text-sm">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <span class="ml-2 text-purple-500">Activate contest</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 mt-2">
                    <label class="flex items-center text-sm">
                        <input type="checkbox" id="requires_approval" name="requires_approval" value="1"
                            {{ old('requires_approval') ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <span class="ml-2 text-purple-500">Require admin approval</span>
                    </label>
                </div>
            </div>

            {{-- Nominees Section --}}
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-semibold mb-3" style="color: rgba(198, 0, 238, 1);">Add Nominees</h3>
                <div id="nomineesWrapper" class="modal-header">
                    <div class="nominee-item flex flex-wrap gap-3 items-end mb-3 bg-gray-50 p-3 rounded-md border border-gray-200">
                        <div class="flex flex-col w-1/5 min-w-[120px]">
                            <label class="text-purple-500 text-sm font-medium mb-1">Name</label>
                            <input type="text" name="nominees[0][name]" required
                                class="border-gray-300 rounded-md px-2 py-1 text-sm" placeholder="Nominee Name">
                        </div>
                        <div class="flex flex-col w-1/5 min-w-[120px]">
                            <label class="text-purple-500 text-sm font-medium mb-1">Photo</label>
                            <input type="file" name="nominees[0][photo]" accept="image/*" class="border-gray-300 rounded-md px-2 py-1 text-sm">
                        </div>
                        <div class="flex flex-col w-1/4 min-w-[150px]">
                            <label class="text-purple-500 text-sm font-medium mb-1">Description</label>
                            <input type="text" name="nominees[0][description]"
                                class="border-gray-300 rounded-md px-2 py-1 text-sm" placeholder="Short description">
                        </div>
                        <div class="flex flex-col w-1/5 min-w-[130px]">
                            <label class="text-purple-500 text-sm font-medium mb-1">Category</label>
                            <div class="flex gap-1">
                                <select name="nominees[0][category_id]" required
                                    class="border-gray-300 rounded-md px-2 py-1 text-sm flex-1">
                                    <option value="">-- Select --</option>
                                    @foreach($nomineeCategories as $ncat)
                                        <option value="{{ $ncat->id }}">{{ $ncat->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="px-2 py-1 bg-purple-500 text-white text-sm rounded-md hover:bg-indigo-700 openNomineeCatModal">+</button>
                            </div>
                        </div>
                        <button type="button" class="removeNomineeBtn text-red-600 font-bold text-lg ml-auto">×</button>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="addNomineeBtn"
                        class="mt-2 px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                        + Add Nominee
                    </button>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end mt-6">
                <a href="{{ route('voting.index') }}" class="mr-2 px-4 py-2 border rounded-md text-black bg-gray-100 text-sm hover:bg-gray-200">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded-md text-sm hover:bg-purple-700">
                    Create Contest
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Voting Category Modal --}}
<div id="votingCategoryModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="modal-header rounded-lg shadow-lg w-80 p-5">
        <h2 class="text-lg font-bold text-white mb-3">Add Voting Category</h2>
        <form id="votingCategoryForm">
            @csrf
            <div class="mb-3">
                <label class="block text-purple-500 font-medium mb-1">Category Name</label>
                <input type="text" id="votingCategoryName" name="name" placeholder="Enter category name"
                    class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm" required>
            </div>
            <div class="mb-3">
                <label class="block text-purple-500 font-medium mb-1">Color (optional)</label>
                <input type="color" id="votingCategoryColor" name="color" value="#8B5CF6" class="w-14 h-8 border rounded-md">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="cancelVotingCategoryBtn"
                    class="px-3 py-1 bg-white rounded-md text-sm" style="color: black;">Cancel</button>
                <button type="submit" class="px-3 py-1 bg-purple-500 text-white rounded-md text-sm hover:bg-indigo-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Nominee Category Modal --}}
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="modal-header rounded-lg shadow-lg w-80 p-5">
        <h2 class="text-lg font-bold text-white mb-3">Add New Nominee Category</h2>
        <form id="categoryForm">
            @csrf
            <div class="mb-3">
                <label class="block text-purple-500 font-medium mb-1">Category Name</label>
                <input type="text" id="categoryName" name="name" placeholder="Enter category name"
                    class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm" required>
            </div>
            <div class="mb-4">
                <label class="block text-purple-500 font-medium mb-1">Color (optional)</label>
                <input type="color" id="categoryColor" name="color" value="#8B5CF6" class="w-14 h-8 border rounded-md">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="cancelCategoryBtn"
                    class="px-3 py-1 bg-gray-200 rounded-md text-sm" style="color: black">Cancel</button>
                <button type="submit" class="px-3 py-1 bg-purple-500 text-white rounded-md text-sm hover:bg-indigo-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // handle votes limit toggle (unlimited vs limited)
    const votesLimitedRadio = document.getElementById('votes_limited');
    const votesUnlimitedRadio = document.getElementById('votes_unlimited');
    const maxVotesWrapper = document.getElementById('maxVotesWrapper');
    const maxVotesInput = document.getElementById('max_votes_per_user');

    function updateMaxVotesVisibility() {
        if (votesUnlimitedRadio && votesUnlimitedRadio.checked) {
            // hide/disable the numeric input
            maxVotesWrapper.style.display = 'none';
            if (maxVotesInput) {
                maxVotesInput.disabled = true;
            }
        } else {
            maxVotesWrapper.style.display = 'block';
            if (maxVotesInput) {
                maxVotesInput.disabled = false;
            }
        }
    }

    if (votesLimitedRadio && votesUnlimitedRadio) {
        votesLimitedRadio.addEventListener('change', updateMaxVotesVisibility);
        votesUnlimitedRadio.addEventListener('change', updateMaxVotesVisibility);
        // initial state
        updateMaxVotesVisibility();
    }

    function previewFeaturedImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('featured_image').addEventListener('change', previewFeaturedImage);


        if (file && preview) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else if (preview) {
            preview.src = '#';
            preview.classList.add('hidden');
        }
    }


    // Nominees add/remove logic (preserved original, with file accept added)
    let nomineeIndex = 1;
    const addNomineeBtn = document.getElementById('addNomineeBtn');
    const nomineesWrapper = document.getElementById('nomineesWrapper');

    // Add new nominee
    addNomineeBtn.addEventListener('click', function() {
        const div = document.createElement('div');
        div.classList.add('nominee-item','flex','flex-wrap','gap-3','items-end','mb-3','bg-gray-50','p-3','rounded-md','border','border-gray-200');
        div.innerHTML = `
            <div class="flex flex-col w-1/5 min-w-[120px]">
                <label class="text-purple-500 text-sm font-medium mb-1">Name</label>
                <input type="text" name="nominees[${nomineeIndex}][name]" required class="border-gray-300 rounded-md px-2 py-1 text-sm" placeholder="Nominee Name">
            </div>
            <div class="flex flex-col w-1/5 min-w-[120px]">
                <label class="text-purple-500 text-sm font-medium mb-1">Photo</label>
                <input type="file" name="nominees[${nomineeIndex}][photo]" accept="image/*" class="border-gray-300 rounded-md px-2 py-1 text-sm">
            </div>
            <div class="flex flex-col w-1/4 min-w-[150px]">
                <label class="text-purple-500 text-sm font-medium mb-1">Description</label>
                <input type="text" name="nominees[${nomineeIndex}][description]" class="border-gray-300 rounded-md px-2 py-1 text-sm" placeholder="Short description">
            </div>
            <div class="flex flex-col w-1/5 min-w-[130px]">
                <label class="text-purple-500 text-sm font-medium mb-1">Category</label>
                <div class="flex gap-1">
                    <select name="nominees[${nomineeIndex}][category_id]" required class="border-gray-300 rounded-md px-2 py-1 text-sm flex-1">
                        <option value="">-- Select --</option>
                        @foreach($nomineeCategories as $ncat)
                        <option value="{{ $ncat->id }}">{{ $ncat->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="px-2 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 openNomineeCatModal">+</button>
                </div>
            </div>
            <button type="button" class="removeNomineeBtn text-red-600 font-bold text-lg ml-auto">×</button>
        `;
        nomineesWrapper.appendChild(div);
        nomineeIndex++;
    });

    // Remove nominee
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeNomineeBtn')) {
            e.target.closest('.nominee-item').remove();
        }
    });

    // Nominee Category Modal open/close
    const nomineeModal = document.getElementById('categoryModal');
    const cancelNomineeBtn = document.getElementById('cancelCategoryBtn');
    const nomineeForm = document.getElementById('categoryForm');

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('openNomineeCatModal')) {
            nomineeModal.classList.remove('hidden');
            nomineeModal.classList.add('flex');
        }
    });

    if (cancelNomineeBtn) {
        cancelNomineeBtn.addEventListener('click', () => {
            nomineeModal.classList.add('hidden');
            nomineeModal.classList.remove('flex');
            if (nomineeForm) nomineeForm.reset();
        });
    }

    // AJAX nominee category submit
    if (nomineeForm) {
        nomineeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('categoryName').value.trim();
            const color = document.getElementById('categoryColor').value;
            if(!name) return alert('Category name is required');

            fetch("{{ route('nominee-categories.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, color })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const selects = document.querySelectorAll('select[name^="nominees"]');
                    selects.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = data.category.id;
                        opt.text = data.category.name;
                        opt.selected = true;
                        s.appendChild(opt);
                    });
                    nomineeModal.classList.add('hidden');
                    nomineeModal.classList.remove('flex');
                    nomineeForm.reset();
                } else {
                    alert(data.message || 'Error adding category');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong');
            });
        });
    }

    // Voting Category Modal open/close
    const votingModal = document.getElementById('votingCategoryModal');
    const cancelVotingBtn = document.getElementById('cancelVotingCategoryBtn');
    const votingForm = document.getElementById('votingCategoryForm');

    const addVotingBtn = document.getElementById('addVotingCategoryBtn');
    if (addVotingBtn) {
        addVotingBtn.addEventListener('click', () => {
            votingModal.classList.remove('hidden');
            votingModal.classList.add('flex');
        });
    }

    if (cancelVotingBtn) {
        cancelVotingBtn.addEventListener('click', () => {
            votingModal.classList.add('hidden');
            votingModal.classList.remove('flex');
            if (votingForm) votingForm.reset();
        });
    }

    // AJAX voting category submit
    if (votingForm) {
        votingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('votingCategoryName').value.trim();
            const color = document.getElementById('votingCategoryColor').value;
            if(!name) return alert('Category name is required');

            fetch("{{ route('voting-category.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, color })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const select = document.getElementById('category_id');
                    const opt = document.createElement('option');
                    opt.value = data.category.id;
                    opt.text = data.category.name;
                    opt.selected = true;
                    select.appendChild(opt);

                    votingModal.classList.add('hidden');
                    votingModal.classList.remove('flex');
                    votingForm.reset();
                } else {
                    alert(data.message || 'Error adding voting category');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong');
            });
        });
    }
});

</script>
@endsection
