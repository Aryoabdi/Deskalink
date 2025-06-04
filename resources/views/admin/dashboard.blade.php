<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Admin Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Users Statistics -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Users Overview</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Total Users</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistics['total_users'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Partners</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistics['total_partners'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Clients</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistics['total_clients'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Statistics -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Pending Content</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Designs</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistics['pending_designs'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Services</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistics['pending_services'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Reports</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistics['pending_reports'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Statistics -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Monthly Revenue</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Total Revenue</p>
                                <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($monthly_revenue, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Platform Fee</p>
                                <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($platform_revenue, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Users -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Users</h3>
                        <div class="space-y-4">
                            @foreach($recent_users as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $user->profile_image }}" alt="{{ $user->username }}" class="h-10 w-10 rounded-full">
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $user->full_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->role }}</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Reports</h3>
                        <div class="space-y-4">
                            @foreach($recent_reports as $report)
                            <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                                <div class="flex justify-between">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $report->reporter->username }} reported {{ $report->reportedUser->username }}
                                    </p>
                                    <span class="text-sm text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">{{ Str::limit($report->reason, 100) }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Pending Content -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Content</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pending_designs as $design)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Design</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $design->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $design->partner->username }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $design->created_at->diffForHumans() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.moderate', ['type' => 'design', 'id' => $design->design_id]) }}" class="text-indigo-600 hover:text-indigo-900">Review</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @foreach($pending_services as $service)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Service</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $service->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->partner->username }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->created_at->diffForHumans() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.moderate', ['type' => 'service', 'id' => $service->service_id]) }}" class="text-indigo-600 hover:text-indigo-900">Review</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
