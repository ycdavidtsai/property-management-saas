<x-admin-layout>
    <x-slot name="header">System Health</x-slot>

    <div class="mb-6">
        <p class="text-gray-600">Monitor system health, view failed jobs, and check platform statistics.</p>
    </div>

    {{-- System Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">PHP Version</p>
            <p class="text-lg font-semibold text-gray-900">{{ $systemInfo['php_version'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Laravel Version</p>
            <p class="text-lg font-semibold text-gray-900">{{ $systemInfo['laravel_version'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Environment</p>
            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($systemInfo['environment']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Debug Mode</p>
            <p class="text-lg font-semibold {{ $systemInfo['debug_mode'] ? 'text-yellow-600' : 'text-green-600' }}">
                {{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}
            </p>
        </div>
    </div>

    {{-- Driver Info --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Cache Driver</p>
            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($systemInfo['cache_driver']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Queue Driver</p>
            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($systemInfo['queue_driver']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Mail Driver</p>
            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($systemInfo['mail_driver']) }}</p>
        </div>
    </div>

    {{-- Database Stats --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Database Statistics</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($dbStats['total_organizations']) }}</p>
                    <p class="text-sm text-gray-500">Organizations</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($dbStats['total_users']) }}</p>
                    <p class="text-sm text-gray-500">Users</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($dbStats['total_properties']) }}</p>
                    <p class="text-sm text-gray-500">Properties</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($dbStats['total_units']) }}</p>
                    <p class="text-sm text-gray-500">Units</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($dbStats['total_leases']) }}</p>
                    <p class="text-sm text-gray-500">Leases</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($dbStats['total_maintenance']) }}</p>
                    <p class="text-sm text-gray-500">Maintenance</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($dbStats['total_broadcasts']) }}</p>
                    <p class="text-sm text-gray-500">Broadcasts</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Failed Jobs --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center">
                <h3 class="text-lg font-semibold text-gray-800">Failed Jobs</h3>
                @if($failedJobs->count() > 0)
                    <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                        {{ $failedJobs->count() }}
                    </span>
                @endif
            </div>
            @if($failedJobs->count() > 0)
                <form action="{{ route('admin.system.flush-jobs') }}" method="POST" class="inline">
                    @csrf
                    <button
                        type="submit"
                        onclick="return confirm('Are you sure you want to delete ALL failed jobs? This cannot be undone.')"
                        class="text-sm text-red-600 hover:text-red-800"
                    >
                        Flush All
                    </button>
                </form>
            @endif
        </div>
        <div class="overflow-x-auto">
            @if($failedJobs->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Queue
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Job
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Failed At
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Exception
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($failedJobs as $job)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                                    {{ $job->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $job->queue }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $payload = json_decode($job->payload);
                                        $jobClass = $payload->displayName ?? 'Unknown';
                                        $shortName = class_basename($jobClass);
                                    @endphp
                                    <span title="{{ $jobClass }}">{{ $shortName }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($job->failed_at)->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-red-600 max-w-md">
                                    <div class="truncate" title="{{ $job->exception }}">
                                        {{ Str::limit($job->exception, 100) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <form action="{{ route('admin.system.retry-job', $job->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Retry"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.system.delete-job', $job->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                onclick="return confirm('Delete this failed job?')"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No failed jobs</h3>
                    <p class="mt-1 text-sm text-gray-500">All queued jobs are processing successfully.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Queued Jobs --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <h3 class="text-lg font-semibold text-gray-800">Pending Jobs</h3>
                @if($queuedJobs->count() > 0)
                    <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                        {{ $queuedJobs->count() }}
                    </span>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($queuedJobs->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Queue
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Job
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Attempts
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($queuedJobs as $job)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                                    {{ $job->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $job->queue }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $payload = json_decode($job->payload);
                                        $jobClass = $payload->displayName ?? 'Unknown';
                                        $shortName = class_basename($jobClass);
                                    @endphp
                                    <span title="{{ $jobClass }}">{{ $shortName }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $job->attempts }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($job->created_at)->format('M j, Y g:i A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending jobs</h3>
                    <p class="mt-1 text-sm text-gray-500">The job queue is empty.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Activity Log Placeholder --}}
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Activity Log</h3>
        </div>
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Activity Logging Coming Soon</h3>
            <p class="mt-1 text-sm text-gray-500">Detailed activity logging will be available in a future update.</p>
        </div>
    </div>
</x-admin-layout>
