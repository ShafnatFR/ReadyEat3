{{-- Dashboard/Reporting Tab --}}
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-xl shadow-lg text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium opacity-90">Total Revenue</h3>
                    <p class="text-3xl font-bold mt-2">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium opacity-90">Total Orders</h3>
                    <p class="text-3xl font-bold mt-2">{{ $stats['totalOrders'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl shadow-lg text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium opacity-90">Unique Customers</h3>
                    <p class="text-3xl font-bold mt-2">{{ $stats['uniqueCustomers'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-xl shadow-lg text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium opacity-90">Avg Order Value</h3>
                    <p class="text-3xl font-bold mt-2">Rp {{ number_format($stats['avgOrderValue'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Controls --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Filters
        </h3>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="hidden" name="tab" value="dashboard">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Period</label>
                <select name="filter_type"
                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="daily" {{ $stats['filterType'] === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $stats['filterType'] === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $stats['filterType'] === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ $stats['filterType'] === 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Date</label>
                @if($stats['filterType'] === 'yearly')
                    <input type="number" name="selected_date" value="{{ $stats['selectedDate'] }}" min="2020" max="2099"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                @elseif($stats['filterType'] === 'weekly')
                    <input type="week" name="selected_date" value="{{ $stats['selectedDate'] }}"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                @elseif($stats['filterType'] === 'monthly')
                    <input type="month" name="selected_date" value="{{ $stats['selectedDate'] }}"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                @else
                    <input type="date" name="selected_date" value="{{ $stats['selectedDate'] }}"
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                @endif
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    {{-- Revenue Trend Chart (Full Width) --}}
    @if(count($stats['revenueTrend']) > 1)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Revenue Trend
            </h3>
            <canvas id="revenueTrendChart" height="80"></canvas>
        </div>
    @endif

    {{-- Charts Row 1: Top Products & Category Distribution --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Products Chart --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                Top 5 Products
            </h3>
            @if(count($stats['topProducts']) > 0)
                <canvas id="topProductsChart" height="150"></canvas>
            @else
                <div class="flex items-center justify-center h-64 text-gray-400">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p>No data available</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Category Distribution Pie Chart --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Category Distribution
            </h3>
            @if(count($stats['categoryDistribution']) > 0)
                <div class="flex justify-center">
                    <div style="max-width: 300px; width: 100%;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-400">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        </svg>
                        <p>No data available</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Top Customers Table --}}
    @if(isset($stats['topCustomers']) && count($stats['topCustomers']) > 0)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Top Customers
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-gray-600 dark:text-gray-400 font-semibold">Rank</th>
                            <th class="text-left py-3 px-4 text-gray-600 dark:text-gray-400 font-semibold">Customer</th>
                            <th class="text-left py-3 px-4 text-gray-600 dark:text-gray-400 font-semibold">Phone</th>
                            <th class="text-right py-3 px-4 text-gray-600 dark:text-gray-400 font-semibold">Orders</th>
                            <th class="text-right py-3 px-4 text-gray-600 dark:text-gray-400 font-semibold">Total Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['topCustomers'] as $index => $customer)
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600')) }} font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-900 dark:text-white font-medium">{{ $customer['name'] }}</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $customer['phone'] }}</td>
                                <td class="py-3 px-4 text-right text-gray-900 dark:text-white">{{ $customer['orders'] }}</td>
                                <td class="py-3 px-4 text-right text-primary font-bold">Rp {{ number_format($customer['total'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart configuration
    const chartColors = {
        primary: '#f97316',
        blue: '#3b82f6',
        green: '#10b981',
        purple: '#8b5cf6',
        pink: '#ec4899',
        yellow: '#eab308',
        red: '#ef4444',
        indigo: '#6366f1',
        teal: '#14b8a6',
    };

    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#e5e7eb' : '#374151';
    const gridColor = isDarkMode ? '#374151' : '#e5e7eb';

    // Revenue Trend Chart
    @if(count($stats['revenueTrend']) > 1)
        const revenueTrendCtx = document.getElementById('revenueTrendChart');
        if (revenueTrendCtx) {
            new Chart(revenueTrendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($stats['revenueTrend'], 'label')) !!},
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: {!! json_encode(array_column($stats['revenueTrend'], 'value')) !!},
                        borderColor: chartColors.primary,
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: chartColors.primary,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: textColor,
                                font: { size: 12, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            },
                            grid: {
                                color: gridColor,
                                drawBorder: false,
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                display: false,
                                drawBorder: false,
                            }
                        }
                    }
                }
            });
        }
    @endif

    // Top Products Chart
    @if(count($stats['topProducts']) > 0)
        const topProductsCtx = document.getElementById('topProductsChart');
        if (topProductsCtx) {
            new Chart(topProductsCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($stats['topProducts'], 'name')) !!},
                    datasets: [{
                        label: 'Quantity Sold',
                        data: {!! json_encode(array_column($stats['topProducts'], 'quantity')) !!},
                        backgroundColor: [
                            chartColors.primary,
                            chartColors.blue,
                            chartColors.green,
                            chartColors.purple,
                            chartColors.pink,
                        ],
                        borderRadius: 8,
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                stepSize: 1
                            },
                            grid: {
                                color: gridColor,
                                drawBorder: false,
                            }
                        },
                        y: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                display: false,
                                drawBorder: false,
                            }
                        }
                    }
                }
            });
        }
    @endif

    // Category Distribution Chart
    @if(count($stats['categoryDistribution']) > 0)
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_column($stats['categoryDistribution'], 'label')) !!},
                    datasets: [{
                        data: {!! json_encode(array_column($stats['categoryDistribution'], 'value')) !!},
                        backgroundColor: [
                            chartColors.primary,
                            chartColors.blue,
                            chartColors.green,
                            chartColors.purple,
                            chartColors.pink,
                            chartColors.yellow,
                            chartColors.red,
                            chartColors.indigo,
                            chartColors.teal,
                        ],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 15,
                                font: { size: 12, weight: 'bold' },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    @endif
</script>