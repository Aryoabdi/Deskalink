<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Moderation Logs</h2>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.moderation') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Pending Content
                    </a>
                    <a href="{{ route('admin.moderation.logs') }}" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Moderation Logs
                    </a>
                    <a href="{{ route('admin.reports') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Reports
                    </a>
                </nav>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form action="{{ route('admin.moderation.logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="content_type" class="block text-sm font-medium text-gray-700">Content Type</label>
                        <select name="content_type" id="content_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Types</option>
                            <option value="design" {{ request('content_type') === 'design' ? 'selected' : '' }}>Design</option>
                            <option value="service" {{ request('content_type') === 'service' ? 'selected' : '' }}>Service</option>
                        </select>
                    </div>

                    <div>
                        <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                        <select name="action" id="action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Actions</option>
                            <option value="approved" {{ request('action') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('action') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="banned" {{ request('action') === 'banned' ? 'selected' : '' }}>Banned</option>
                        </select>
                    </div>

                    <div>
                        <label for="moderator" class="block text-sm font-medium text-gray-700">Moderator</label>
                        <input type="text" name="moderator" id="moderator" value="{{ request('moderator') }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Search by moderator">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Filter Results
                        </button>
                    </div>
                </form>
            </div>

            <!-- Logs Table -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moderator</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at->format('M d, Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full" src="{{ $log->moderator->profile_image }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $log->moderator->username }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $log->content_type === 'design' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $log->content_type === 'service' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($log->content_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $log->action === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action === 'rejected' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $log->action === 'banned' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $log->reason ?: '-' }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No moderation logs found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $logs->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
