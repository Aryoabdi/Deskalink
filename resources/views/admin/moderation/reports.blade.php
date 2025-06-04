<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">User Reports</h2>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.moderation') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Pending Content
                    </a>
                    <a href="{{ route('admin.moderation.logs') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Moderation Logs
                    </a>
                    <a href="{{ route('admin.reports') }}" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Reports
                    </a>
                </nav>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form action="{{ route('admin.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in review" {{ request('status') === 'in review' ? 'selected' : '' }}>In Review</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search Users</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Search reporter or reported user">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Filter Results
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reports List -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <ul class="divide-y divide-gray-200">
                    @forelse($reports as $report)
                    <li class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full" src="{{ $report->reportedUser->profile_image }}" alt="">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $report->reportedUser->username }}
                                                <span class="text-gray-500">reported by</span>
                                                {{ $report->reporter->username }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $report->created_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $report->status === 'in review' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $report->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-900">{{ $report->reason }}</p>
                                </div>
                            </div>
                            @if($report->status !== 'resolved')
                            <div class="ml-4">
                                <button onclick="showReportModal('{{ $report->report_id }}')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Handle Report
                                </button>
                            </div>
                            @endif
                        </div>
                        @if($report->status === 'resolved')
                        <div class="mt-4 bg-gray-50 rounded-md p-4">
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">Resolution:</span>
                                {{ $report->resolution_note }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Resolved by {{ $report->resolvedBy->username }} on {{ $report->resolved_at->format('M d, Y H:i') }}
                            </p>
                        </div>
                        @endif
                    </li>
                    @empty
                    <li class="p-6 text-center text-gray-500">
                        No reports found.
                    </li>
                    @endforelse
                </ul>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $reports->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Report Handling Modal -->
    <div id="reportModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="reportForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Handle Report
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Action</label>
                                    <select name="action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="resolved">Mark as Resolved</option>
                                        <option value="dismissed">Dismiss Report</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">User Action</label>
                                    <select name="user_action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">No Action</option>
                                        <option value="suspended">Suspend User</option>
                                        <option value="banned">Ban User</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Note</label>
                                    <textarea name="note" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Submit
                        </button>
                        <button type="button" onclick="hideReportModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showReportModal(reportId) {
            const modal = document.getElementById('reportModal');
            const form = document.getElementById('reportForm');
            form.action = `/admin/reports/${reportId}`;
            modal.classList.remove('hidden');
        }

        function hideReportModal() {
            const modal = document.getElementById('reportModal');
            modal.classList.add('hidden');
        }
    </script>
</x-app-layout>
