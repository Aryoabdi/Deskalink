<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Content Moderation</h2>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="#pending-designs" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Pending Designs ({{ $pending_designs->total() }})
                    </a>
                    <a href="#pending-services" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Pending Services ({{ $pending_services->total() }})
                    </a>
                    <a href="{{ route('admin.moderation.logs') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Moderation Logs
                    </a>
                </nav>
            </div>

            <!-- Pending Designs Section -->
            <div id="pending-designs" class="bg-white shadow-sm rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium text-gray-900">Pending Designs</h3>
                </div>
                <div class="border-t border-gray-200">
                    <ul role="list" class="divide-y divide-gray-200">
                        @forelse($pending_designs as $design)
                        <li class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <img class="h-16 w-16 rounded-lg object-cover" src="{{ $design->thumbnail }}" alt="{{ $design->title }}">
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900">{{ $design->title }}</h4>
                                        <p class="text-sm text-gray-500">By {{ $design->partner->username }}</p>
                                        <p class="mt-1 text-sm text-gray-500">Submitted {{ $design->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <button onclick="showModerationModal('design', '{{ $design->design_id }}')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Review
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">{{ Str::limit($design->description, 200) }}</p>
                            </div>
                        </li>
                        @empty
                        <li class="p-4 text-center text-gray-500">
                            No pending designs to review.
                        </li>
                        @endforelse
                    </ul>
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $pending_designs->links() }}
                    </div>
                </div>
            </div>

            <!-- Pending Services Section -->
            <div id="pending-services" class="bg-white shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium text-gray-900">Pending Services</h3>
                </div>
                <div class="border-t border-gray-200">
                    <ul role="list" class="divide-y divide-gray-200">
                        @forelse($pending_services as $service)
                        <li class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <img class="h-16 w-16 rounded-lg object-cover" src="{{ $service->thumbnail }}" alt="{{ $service->title }}">
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900">{{ $service->title }}</h4>
                                        <p class="text-sm text-gray-500">By {{ $service->partner->username }}</p>
                                        <p class="mt-1 text-sm text-gray-500">Submitted {{ $service->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <button onclick="showModerationModal('service', '{{ $service->service_id }}')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Review
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">{{ Str::limit($service->description, 200) }}</p>
                            </div>
                        </li>
                        @empty
                        <li class="p-4 text-center text-gray-500">
                            No pending services to review.
                        </li>
                        @endforelse
                    </ul>
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $pending_services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Moderation Modal -->
    <div id="moderationModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="moderationForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Moderate Content
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Action</label>
                                    <select name="action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                        <option value="banned">Ban</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Reason (required for reject/ban)</label>
                                    <textarea name="reason" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Submit
                        </button>
                        <button type="button" onclick="hideModerationModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showModerationModal(type, id) {
            const modal = document.getElementById('moderationModal');
            const form = document.getElementById('moderationForm');
            form.action = `/admin/moderation/${type}/${id}`;
            modal.classList.remove('hidden');
        }

        function hideModerationModal() {
            const modal = document.getElementById('moderationModal');
            modal.classList.add('hidden');
        }
    </script>
</x-app-layout>
