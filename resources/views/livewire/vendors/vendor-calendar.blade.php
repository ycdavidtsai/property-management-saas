<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Work Calendar</h1>
                <p class="text-sm text-gray-600 mt-1">View and manage your scheduled jobs</p>
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap gap-3 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                    <span class="text-gray-600">Pending</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-400"></span>
                    <span class="text-gray-600">Assigned</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    <span class="text-gray-600">In Progress</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-green-400"></span>
                    <span class="text-gray-600">Completed</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar Card --}}
    <div class="bg-white rounded-xl shadow-sm border">
        {{-- Month Navigation --}}
        <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b bg-gray-50 rounded-t-xl">
            <button
                wire:click="previousMonth"
                class="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                title="Previous month"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div class="flex items-center gap-3">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">{{ $monthName }}</h2>
                <button
                    wire:click="goToToday"
                    class="text-xs px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors"
                >
                    Today
                </button>
            </div>

            <button
                wire:click="nextMonth"
                class="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                title="Next month"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Calendar Grid --}}
        <div class="p-2 sm:p-4">
            {{-- Day Headers --}}
            <div class="grid grid-cols-7 mb-2">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center text-xs font-medium text-gray-500 py-2">
                        <span class="hidden sm:inline">{{ $day }}</span>
                        <span class="sm:hidden">{{ substr($day, 0, 1) }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Weeks --}}
            @foreach($weeks as $week)
                <div class="grid grid-cols-7 border-t">
                    @foreach($week as $day)
                        <button
                            wire:click="selectDate('{{ $day['date'] }}')"
                            class="relative min-h-[60px] sm:min-h-[80px] p-1 sm:p-2 border-r last:border-r-0 transition-colors
                                {{ $day['isCurrentMonth'] ? 'bg-white hover:bg-gray-50' : 'bg-gray-50 text-gray-400' }}
                                {{ $day['isToday'] ? 'ring-2 ring-inset ring-blue-500' : '' }}
                                {{ $selectedDate === $day['date'] ? 'bg-blue-50' : '' }}"
                        >
                            {{-- Day Number --}}
                            <span class="text-xs sm:text-sm font-medium
                                {{ $day['isToday'] ? 'text-blue-600 font-bold' : '' }}
                                {{ $day['isPast'] && !$day['isToday'] ? 'text-gray-400' : '' }}">
                                {{ $day['day'] }}
                            </span>

                            {{-- Job Indicators --}}
                            @if($day['jobCount'] > 0)
                                <div class="mt-1 flex flex-wrap gap-0.5">
                                    @foreach(array_slice($day['jobs'], 0, 3) as $job)
                                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full {{ $this->getStatusClasses($job['status']) }}"
                                              title="{{ ucfirst($job['status']) }}"></span>
                                    @endforeach
                                    @if($day['jobCount'] > 3)
                                        <span class="text-[10px] text-gray-500">+{{ $day['jobCount'] - 3 }}</span>
                                    @endif
                                </div>

                                {{-- Job Count Badge (Desktop) --}}
                                <div class="hidden sm:block absolute bottom-1 right-1">
                                    <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">
                                        {{ $day['jobCount'] }} {{ $day['jobCount'] === 1 ? 'job' : 'jobs' }}
                                    </span>
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- Selected Date Detail Panel --}}
    @if($selectedDate)
        <div class="mt-6 bg-white rounded-xl shadow-sm border" wire:key="detail-{{ $selectedDate }}">
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b bg-gray-50 rounded-t-xl">
                <h3 class="font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}
                </h3>
                <button
                    wire:click="closeDetail"
                    class="p-1.5 hover:bg-gray-200 rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-6">
                @if(count($selectedDateJobs) > 0)
                    <div class="space-y-3">
                        @foreach($selectedDateJobs as $job)
                            <a
                                href="{{ route('vendor.requests.show', $job['id']) }}"
                                class="block p-4 border rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors group"
                            >
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="flex-1">
                                        {{-- Title & Category --}}
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-medium text-gray-900 group-hover:text-blue-700">
                                                {{ $job['title'] }}
                                            </h4>
                                            <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded">
                                                {{ ucfirst($job['category']) }}
                                            </span>
                                        </div>

                                        {{-- Property & Unit --}}
                                        <p class="text-sm text-gray-600">
                                            {{ $job['property'] }}
                                            @if($job['unit'])
                                                <span class="text-gray-400">•</span>
                                                Unit {{ $job['unit'] }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        {{-- Time --}}
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-700">
                                                {{ $job['time'] }}
                                            </div>
                                        </div>

                                        {{-- Status Badge --}}
                                        <span class="text-xs px-2.5 py-1 rounded-full
                                            @switch($job['status_color'])
                                                @case('yellow') bg-yellow-100 text-yellow-800 @break
                                                @case('blue') bg-blue-100 text-blue-800 @break
                                                @case('indigo') bg-indigo-100 text-indigo-800 @break
                                                @case('green') bg-green-100 text-green-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ $job['status_label'] }}
                                        </span>

                                        {{-- Arrow --}}
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Priority indicator --}}
                                @if($job['priority'] === 'emergency' || $job['priority'] === 'high')
                                    <div class="mt-2 flex items-center gap-1.5 text-xs">
                                        <span class="w-2 h-2 rounded-full
                                            {{ $job['priority'] === 'emergency' ? 'bg-red-500' : 'bg-orange-500' }}"></span>
                                        <span class="{{ $job['priority'] === 'emergency' ? 'text-red-600' : 'text-orange-600' }} font-medium">
                                            {{ ucfirst($job['priority']) }} Priority
                                        </span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-500">No jobs scheduled for this day</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Quick Stats Footer --}}
    @if($vendor)
        @php
            $todayJobs = \App\Models\MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
                ->whereDate('scheduled_date', today())
                ->whereIn('status', ['pending_acceptance', 'assigned', 'in_progress'])
                ->count();

            $weekJobs = \App\Models\MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
                ->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->whereIn('status', ['pending_acceptance', 'assigned', 'in_progress'])
                ->count();

            $pendingCount = \App\Models\MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
                ->where('status', 'pending_acceptance')
                ->count();
        @endphp

        <div class="mt-6 grid grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $todayJobs }}</div>
                <div class="text-xs text-gray-500 mt-1">Today's Jobs</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
                <div class="text-2xl font-bold text-indigo-600">{{ $weekJobs }}</div>
                <div class="text-xs text-gray-500 mt-1">This Week</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
                <div class="text-2xl font-bold {{ $pendingCount > 0 ? 'text-yellow-600' : 'text-gray-400' }}">{{ $pendingCount }}</div>
                <div class="text-xs text-gray-500 mt-1">Pending Accept</div>
            </div>
        </div>
    @endif
</div>
