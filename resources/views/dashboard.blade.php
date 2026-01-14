<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('firewalls.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add Firewall
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Firewalls</h3>

                    @if($firewallsWithStatus->isEmpty())
                        <p class="text-gray-500">No firewalls configured yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($firewallsWithStatus as $firewall)
                                <div x-data="{
                                            loading: true,
                                            online: false,
                                            status: null,
                                            error: null,
                                            // Traffic Monitor (Rate Calculation)
                                            bandwidthHistory: new Array(20).fill({ in: 0, out: 0 }),
                                            currentTraffic: { in: '0 Bps', out: '0 Bps' },
                                            lastBytes: { in: 0, out: 0, time: 0 },
                                            
                                            updateBandwidthFromInterfaces(interfaces) {
                                                // Find WAN interface (case insensitive) or default to first
                                                let wan = null;
                                                const wanKey = Object.keys(interfaces).find(key => key.toLowerCase() === 'wan');
                                                
                                                if (wanKey) {
                                                     wan = interfaces[wanKey];
                                                } else {
                                                     // Fallback to first
                                                     const firstKey = Object.keys(interfaces)[0];
                                                     if (firstKey) wan = interfaces[firstKey];
                                                }

                                                if (!wan) return;

                                                const now = new Date().getTime();
                                                const bytesIn = parseFloat(wan.inbytes || 0);
                                                const bytesOut = parseFloat(wan.outbytes || 0);

                                                let inRate = 0;
                                                let outRate = 0;

                                                // Calculate rate if we have history
                                                if (this.lastBytes.time > 0) {
                                                    const timeDiff = (now - this.lastBytes.time) / 1000; // seconds
                                                    if (timeDiff > 0) {
                                                        // Rate in bits per second: (Delta Bytes * 8) / Seconds
                                                        // Handle potential counter wrap (if new < old, ignore or reset)
                                                        if (bytesIn >= this.lastBytes.in) {
                                                            inRate = ((bytesIn - this.lastBytes.in) * 8) / timeDiff;
                                                        }
                                                        if (bytesOut >= this.lastBytes.out) {
                                                            outRate = ((bytesOut - this.lastBytes.out) * 8) / timeDiff;
                                                        }
                                                    }
                                                }

                                                // Update Last State
                                                this.lastBytes = { in: bytesIn, out: bytesOut, time: now };

                                                // Update History & Text
                                                this.bandwidthHistory.shift();
                                                this.bandwidthHistory.push({ in: inRate, out: outRate });
                                                this.currentTraffic = { 
                                                    in: this.formatBytes(inRate, true), // true = bits
                                                    out: this.formatBytes(outRate, true) 
                                                };
                                            },
                                            
                                            formatBytes(size, isBits = false) {
                                                if (!+size) return isBits ? '0 bps' : '0 B';
                                                const k = 1024;
                                                const decimals = 2;
                                                const dm = decimals < 0 ? 0 : decimals;
                                                const sizes = isBits 
                                                    ? ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'] 
                                                    : ['B', 'KB', 'MB', 'GB', 'TB'];
                                                const i = Math.floor(Math.log(size) / Math.log(k));
                                                return `${parseFloat((size / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
                                            },
                                            getGraphPoints(type) {
                                                const max = Math.max(...this.bandwidthHistory.map(d => Math.max(d.in, d.out))) || 100;
                                                const height = 30; // Compact height
                                                const width = 100; 
                                                const step = width / (this.bandwidthHistory.length - 1);
                                                return this.bandwidthHistory.map((d, i) => {
                                                    const val = d[type];
                                                    const y = height - ((val / max) * height);
                                                    return `${i * step},${y}`;
                                                }).join(' ');
                                            },

                                            init() {
                                                this.fetchStatus();
                                                this.setupWebSocket();

                                                // Poll every 5 seconds to trigger backend update & broadcast
                                                setInterval(() => {
                                                    this.fetchStatus();
                                                }, 5000);
                                            },

                                            fetchStatus() {
                                                // Add timestamp to prevent browser caching
                                                fetch('{{ route('firewall.check-status', $firewall) }}?t=' + new Date().getTime())
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        this.loading = false;
                                                        this.online = data.online;
                                                        if (data.online) {
                                                            this.status = data.status;
                                            // Update Traffic & Interface Status
                                            if (data.status.interfaces) {
                                                this.updateBandwidthFromInterfaces(data.status.interfaces);
                                            }
                                                        } else {
                                                            this.error = data.error;
                                                        }
                                                    })
                                                    .catch(error => {
                                                        this.loading = false;
                                                        this.online = false;
                                                        this.error = 'Failed to check status';
                                                    });
                                            },

                                            setupWebSocket() {
                                                if (window.Echo) {
                                                    console.log('Listening for firewall.{{ $firewall->id }} updates...');
                                                    window.Echo.private('firewall.{{ $firewall->id }}')
                                                        .listen('.firewall.status.update', (e) => {
                                                            console.log('Received update for firewall {{ $firewall->id }}:', e);
                                                            this.loading = false;
                                                            this.online = true;
                                                            this.online = true;
                                                            this.status = e.status;
                                                            if (e.status.interfaces) {
                                                                this.updateBandwidthFromInterfaces(e.status.interfaces);
                                                            }
                                                        });
                                                } else {
                                                    console.log('Echo not ready, retrying in 500ms...');
                                                    setTimeout(() => this.setupWebSocket(), 500);
                                                }
                                            }
                                        }" class="border dark:border-gray-700 rounded-lg p-6 bg-white dark:bg-gray-800 hover:shadow-lg transition">
                                    
                                    {{-- Header Row: Name & Actions --}}
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <h4 class="font-bold text-xl">{{ $firewall->name }}</h4>
                                            
                                            <template x-if="loading">
                                                <span class="bg-gray-200 text-gray-800 text-xs px-2.5 py-0.5 rounded animate-pulse">Loading...</span>
                                            </template>
                                            <template x-if="!loading && online">
                                                <span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Online</span>
                                            </template>
                                            <template x-if="!loading && !online">
                                                <span class="bg-red-100 text-red-800 text-xs px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Offline</span>
                                            </template>

                                             @if(auth()->user()->role === 'admin')
                                                <span class="text-xs text-gray-500 border-l pl-3 dark:border-gray-600">{{ $firewall->company->name }}</span>
                                            @endif
                                            <span class="text-xs text-gray-400 border-l pl-3 dark:border-gray-600 font-mono">{{ $firewall->url }}</span>
                                        </div>

                                        <div class="flex gap-2">
                                            <a href="{{ route('firewall.dashboard', $firewall) }}"
                                                class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded">
                                                Manage
                                            </a>
                                            <a href="{{ route('firewalls.edit', $firewall) }}"
                                                class="bg-gray-500 hover:bg-gray-700 text-white text-sm font-bold py-2 px-4 rounded">
                                                Edit
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Content Area --}}
                                    <div class="min-h-[6rem]">
                                        <template x-if="loading">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-pulse">
                                                <div class="space-y-2">
                                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                                                </div>
                                                <div class="space-y-2">
                                                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                                                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                                                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="!loading && online && status && status.data">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                {{-- Left Column: System Details Table --}}
                                                <div>
                                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                        <tbody>
                                                            <tr class="border-b dark:border-gray-700">
                                                                <th class="py-1 font-medium text-gray-900 dark:text-gray-300">Version</th>
                                                                <td class="py-1" x-text="status.data.version"></td>
                                                            </tr>
                                                            <tr class="border-b dark:border-gray-700">
                                                                <th class="py-1 font-medium text-gray-900 dark:text-gray-300">REST API</th>
                                                                <td class="py-1" x-text="status.api_version || 'Unknown'"></td>
                                                            </tr>
                                                            <tr class="border-b dark:border-gray-700">
                                                                <th class="py-1 font-medium text-gray-900 dark:text-gray-300">Platform</th>
                                                                <td class="py-1" x-text="status.data.platform"></td>
                                                            </tr>
                                                            <tr class="border-b dark:border-gray-700">
                                                                <th class="py-1 font-medium text-gray-900 dark:text-gray-300">BIOS</th>
                                                                <td class="py-1">
                                                                    <div class="flex flex-col text-xs">
                                                                        <span x-text="status.data.bios_vendor"></span>
                                                                        <span x-text="status.data.bios_version"></span>
                                                                        <span x-text="status.data.bios_date"></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="border-b dark:border-gray-700">
                                                                <th class="py-1 font-medium text-gray-900 dark:text-gray-300">CPU System</th>
                                                                <td class="py-1">
                                                                    <div class="flex flex-col text-xs">
                                                                        <span x-text="status.data.cpu_model"></span>
                                                                        <span class="text-gray-500" x-text="(status.data.cpu_count || '1') + ' CPUs'"></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="dark:border-gray-700">
                                                                <th class="py-1 font-medium text-gray-900 dark:text-gray-300">Uptime</th>
                                                                <td class="py-1" x-text="status.data.uptime"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Right Column: Mini Graphs --}}
                                                <div class="space-y-3">
                                                    {{-- CPU --}}
                                                    <div>
                                                        <div class="flex justify-between mb-1 text-xs">
                                                            <span class="font-medium text-gray-700 dark:text-gray-300">CPU Usage</span>
                                                            <span class="text-gray-700 dark:text-gray-300" x-text="(status.data.cpu_usage || 0) + '%'"></span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" :style="'width: ' + (status.data.cpu_usage || 0) + '%'"></div>
                                                        </div>
                                                    </div>

                                                    {{-- Memory --}}
                                                    <div>
                                                        <div class="flex justify-between mb-1 text-xs">
                                                            <span class="font-medium text-gray-700 dark:text-gray-300">Memory Usage</span>
                                                            <span class="text-gray-700 dark:text-gray-300" x-text="(status.data.mem_usage || 0) + '%'"></span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                            <div class="bg-purple-600 h-2 rounded-full transition-all duration-500" :style="'width: ' + (status.data.mem_usage || 0) + '%'"></div>
                                                        </div>
                                                    </div>

                                                    {{-- Swap --}}
                                                    <div>
                                                        <div class="flex justify-between mb-1 text-xs">
                                                            <span class="font-medium text-gray-700 dark:text-gray-300">Swap Usage</span>
                                                            <span class="text-gray-700 dark:text-gray-300" x-text="(status.data.swap_usage || 0) + '%'"></span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                            <div class="bg-red-600 h-2 rounded-full transition-all duration-500" :style="'width: ' + (status.data.swap_usage || 0) + '%'"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Disk -->
                                                    <div>
                                                        <div class="flex justify-between mb-1 text-xs">
                                                            <span class="font-medium text-gray-700 dark:text-gray-300">Disk Usage (/)</span>
                                                            <span class="text-gray-700 dark:text-gray-300" x-text="(status.data.disk_usage || 0) + '%'"></span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                            <div class="bg-yellow-600 h-2 rounded-full transition-all duration-500" :style="'width: ' + (status.data.disk_usage || 0) + '%'"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Temperature -->
                                                    <template x-if="status?.data?.temp_c">
                                                        <div>
                                                            <div class="flex justify-between mb-1 text-xs">
                                                                <span class="font-medium text-gray-700 dark:text-gray-300">Temperature</span>
                                                                <span class="text-gray-700 dark:text-gray-300"
                                                                    x-text="status.data.temp_c + '°C'"></span>
                                                            </div>
                                                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                                <div class="bg-orange-500 h-2 rounded-full transition-all duration-500"
                                                                    :style="'width: ' + Math.min(status.data.temp_c, 100) + '%'">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Interface Status Indicators -->
                                                    <template x-if="status && status.interfaces">
                                                        <div class="mt-3">
                                                            <div class="mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Interfaces</div>
                                                            <div class="flex flex-wrap gap-2">
                                                                <template x-for="(iface, name) in status.interfaces" :key="name">
                                                                    <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded text-xs border border-gray-100 dark:border-gray-600">
                                                                        <div class="w-2 h-2 rounded-full"
                                                                            :class="{
                                                                                'bg-green-500': iface.status === 'up' || iface.status === 'associated',
                                                                                'bg-red-500': iface.status === 'down' || iface.status === 'no carrier',
                                                                                'bg-yellow-500': !['up', 'down', 'associated', 'no carrier'].includes(iface.status)
                                                                            }"></div>
                                                                        <span class="font-mono uppercase text-gray-600 dark:text-gray-300" x-text="iface.descr || name"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Compact Traffic Monitor -->
                                                    <div class="mt-4">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Traffic Monitor</span>
                                                            <div class="flex gap-4 text-xs">
                                                                <span class="text-green-600 dark:text-green-400 font-mono">In: <span x-text="currentTraffic.in"></span></span>
                                                                <span class="text-blue-600 dark:text-blue-400 font-mono">Out: <span x-text="currentTraffic.out"></span></span>
                                                            </div>
                                                        </div>
                                                        <div class="h-8 w-full bg-gray-50 dark:bg-gray-900 rounded overflow-hidden relative border border-gray-100 dark:border-gray-700 flex">
                                                            <!-- Combined or Split Graph? Let's do overlay -->
                                                            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 30">
                                                                <!-- Inbound -->
                                                                <polyline :points="getGraphPoints('in')" fill="none" stroke="#22c55e" stroke-width="1.5" vector-effect="non-scaling-stroke" />
                                                                <!-- Outbound -->
                                                                <polyline :points="getGraphPoints('out')" fill="none" stroke="#3b82f6" stroke-width="1.5" vector-effect="non-scaling-stroke" style="opacity: 0.7" />
                                                            </svg>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="!loading && !online">
                                            <div class="text-center py-6">
                                                <p class="text-red-500 font-semibold">Firewall is unreachable</p>
                                                <p class="text-sm text-gray-500" x-text="error || 'Connection timed out or refused'"></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>