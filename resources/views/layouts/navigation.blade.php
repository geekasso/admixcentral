<nav x-data="{ open: false, systemOpen: false, firewallOpen: false, servicesOpen: false, vpnOpen: false, statusOpen: false, diagnosticsOpen: false, systemActive: {{ request()->routeIs('system.*') || request()->routeIs('firewall.system.*') ? 'true' : 'false' }}, firewallActive: {{ request()->routeIs('firewall.aliases.*') || request()->routeIs('firewall.nat.*') || request()->routeIs('firewall.rules.*') || request()->routeIs('firewall.schedules.*') || request()->routeIs('firewall.limiters.*') || request()->routeIs('firewall.virtual_ips.*') ? 'true' : 'false' }}, servicesActive: {{ request()->routeIs('services.*') ? 'true' : 'false' }}, vpnActive: {{ request()->routeIs('vpn.*') ? 'true' : 'false' }}, statusActive: {{ request()->routeIs('status.*') ? 'true' : 'false' }}, diagnosticsActive: {{ request()->routeIs('diagnostics.*') ? 'true' : 'false' }} }"
    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo - Removed as per user request -->
                <!--
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>
                -->

                <!-- Navigation Centered Group -->
                <div class="hidden space-x-8 sm:flex flex-1 justify-center items-center">

                    @if(request()->route('firewall'))
                        <!-- Separator removed as per user request -->
                    @endif
                    @if(request()->route('firewall'))
                        <!-- pfSense-Style Dropdowns (only when managing a firewall) -->
                        
                        <a href="{{ route('firewall.dashboard', request()->route('firewall')) }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('firewall.dashboard') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}">
                            Dashboard
                        </a>

                        <!-- System Dropdown -->
                        <div class="relative" @click.away="systemOpen = false">
                            <button @click="systemOpen = !systemOpen"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                :class="{
                                    'border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-400': systemActive,
                                    'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700': !systemActive
                                }">
                                System
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="systemOpen" style="display: none;"
                                class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <div class="py-1" role="menu">
                                    <a href="{{ route('system.advanced', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Advanced</a>
                                    <a href="{{ route('system.certificate_manager.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Cert.
                                        Manager</a>
                                    <a href="{{ route('system.general-setup', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">General
                                        Setup</a>
                                    <a href="{{ route('system.high-avail-sync', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">High
                                        Avail. Sync</a>
                                    <a href="{{ route('system.package_manager.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Package
                                        Manager</a>
                                    <a href="{{ route('firewall.system.routing', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Routing</a>
                                    <a href="{{ route('system.update', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Update</a>
                                    <a href="{{ route('system.rest-api.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Update
                                        REST API</a>
                                    <a href="{{ route('system.user_manager.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">User
                                        Manager</a>

                                </div>
                            </div>
                        </div>

                        <!-- Interfaces Dropdown -->
                        <div class="relative" x-data="{ interfacesOpen: false }" @click.away="interfacesOpen = false">
                            <button @click="interfacesOpen = !interfacesOpen"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                :class="{
                                    'border-indigo-400 dark:border-indigo-600 text-gray-500 dark:text-gray-400': {{ request()->routeIs('firewall.interfaces.index') ? 'true' : 'false' }},
                                    'border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-400': {{ request()->routeIs('firewall.interfaces.*') || request()->routeIs('firewall.vlans.*') && !request()->routeIs('firewall.interfaces.index') ? 'true' : 'false' }},
                                    'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700': !({{ request()->routeIs('firewall.interfaces.*') || request()->routeIs('firewall.vlans.*') ? 'true' : 'false' }})
                                }">
                                Interfaces
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="interfacesOpen"
                                class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <div class="py-1" role="menu">
                                    <a href="{{ route('firewall.interfaces.index', [request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Overview</a>
                                    <a href="{{ route('interfaces.assignments', [request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Assignments</a>
                                    <a href="{{ route('interfaces.bridges.index', [request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Bridges</a>
                                    <a href="{{ route('interfaces.gre.index', [request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">GRE</a>
                                    <a href="{{ route('interfaces.groups.index', [request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Groups</a>
                                    <a href="{{ route('interfaces.laggs.index', [request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">LAGGs</a>
                                    <a href="{{ route('interfaces.vlans.index', [request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">VLANs</a>
                                    <a href="{{ route('interfaces.wireless.index', [request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Wireless</a>
                                </div>
                            </div>
                        </div>

                        <!-- Firewall Dropdown -->
                        <div class="relative" @click.away="firewallOpen = false">
                            <button @click="firewallOpen = !firewallOpen"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                :class="{
                                    'border-indigo-400 dark:border-indigo-600 text-gray-500 dark:text-gray-400': {{ request()->routeIs('firewall.rules.index') ? 'true' : 'false' }},
                                    'border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-400': (firewallOpen || firewallActive) && !{{ request()->routeIs('firewall.rules.index') ? 'true' : 'false' }},
                                    'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700': (!firewallOpen && !firewallActive)
                                }">
                                Firewall
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="firewallOpen" style="display: none;"
                                class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <div class="py-1" role="menu">
                                    <a href="{{ route('firewall.aliases.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 pl-6">Aliases</a>
                                    <a href="{{ route('firewall.nat.port-forward', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 pl-6">NAT</a>
                                    <a href="{{ route('firewall.rules.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 pl-6">Rules</a>
                                    <a href="{{ route('firewall.schedules.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 pl-6">Schedules</a>
                                    <a href="{{ route('firewall.limiters.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 pl-6">Traffic
                                        Shaper</a>
                                    <a href="{{ route('firewall.virtual_ips.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 pl-6">Virtual
                                        IPs</a>
                                </div>
                            </div>
                        </div>

                        <!-- Services Dropdown -->
                        <div class="relative" @click.away="servicesOpen = false">
                            <button @click="servicesOpen = !servicesOpen"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                :class="{
                                    'border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-400': servicesActive,
                                    'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700': !servicesActive
                                }">
                                Services
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="servicesOpen" style="display: none;"
                                class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <div class="py-1" role="menu">
                                    <a href="{{ route('services.acme.certificates', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">ACME
                                        Certificates</a>
                                    <a href="{{ route('services.auto-config-backup', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Auto
                                        Config Backup</a>
                                    <a href="{{ route('services.captive-portal', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Captive
                                        Portal</a>
                                    <a href="{{ route('services.dhcp-relay', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DHCP
                                        Relay</a>
                                    <a href="{{ route('services.dhcp.index', ['firewall' => request()->route('firewall')]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DHCP
                                        Server</a>
                                    <a href="{{ route('services.dhcpv6-relay', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DHCPv6
                                        Relay</a>
                                    <a href="{{ route('services.dhcpv6-server', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DHCPv6
                                        Server</a>
                                    <a href="{{ route('services.dns-forwarder', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DNS
                                        Forwarder</a>
                                    <a href="{{ route('services.dns.resolver', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DNS
                                        Resolver</a>
                                    <a href="{{ route('services.dynamic-dns', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Dynamic
                                        DNS</a>
                                    <a href="{{ route('services.igmp-proxy', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">IGMP
                                        Proxy</a>
                                    <a href="{{ route('services.ntp', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">NTP</a>
                                    <a href="{{ route('services.pppoe-server', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">PPPoE
                                        Server</a>
                                    <a href="{{ route('services.router-advertisement', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Router
                                        Advertisement</a>
                                    <a href="{{ route('services.snmp', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">SNMP</a>
                                    <a href="{{ route('services.upnp', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">UPnP
                                        IGD & PCP</a>
                                    <a href="{{ route('services.wake-on-lan', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Wake-on-LAN</a>
                                    <a href="{{ route('vpn.wireguard.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">WireGuard</a>
                                </div>
                            </div>
                        </div>

                        <!-- VPN Dropdown -->
                        <div class="relative" @click.away="vpnOpen = false">
                            <button @click="vpnOpen = !vpnOpen"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                :class="{
                                    'border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-400': vpnActive,
                                    'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700': !vpnActive
                                }">
                                VPN
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="vpnOpen" style="display: none;"
                                class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <div class="py-1" role="menu">
                                    <a href="{{ route('vpn.ipsec', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">IPsec</a>
                                    <a href="{{ route('vpn.l2tp', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">L2TP</a>
                                    <a href="{{ route('vpn.openvpn.servers', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">OpenVPN</a>
                                    <a href="{{ route('vpn.wireguard.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">WireGuard</a>
                                </div>
                            </div>
                        </div>

                        <!-- Status Dropdown -->
                        <div class="relative" @click.away="statusOpen = false">
                            <button @click="statusOpen = !statusOpen"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                :class="{
                                    'border-indigo-400 dark:border-indigo-600 text-gray-500 dark:text-gray-400': {{ request()->routeIs('status.dashboard') ? 'true' : 'false' }},
                                    'border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-400': statusActive && !{{ request()->routeIs('status.dashboard') ? 'true' : 'false' }},
                                    'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700': !statusActive
                                }">
                                Status
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="statusOpen" style="display: none;"
                                class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <div class="py-1" role="menu">
                                    <a href="{{ route('status.captive-portal', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Captive
                                        Portal</a>
                                    <a href="{{ route('status.carp', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">CARP
                                        (failover)</a>
                                    <a href="{{ route('status.dashboard', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                                    <a href="{{ route('status.dhcp-leases', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DHCP
                                        Leases</a>
                                    <a href="{{ route('status.dhcpv6-leases', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DHCPv6
                                        Leases</a>
                                    <a href="{{ route('status.filter-reload', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Filter
                                        Reload</a>
                                    <a href="{{ route('status.gateways', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Gateways</a>
                                    <a href="{{ route('status.interfaces.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Interfaces</a>
                                    <a href="{{ route('status.ipsec', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">IPsec</a>
                                    <a href="{{ route('status.monitoring', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Monitoring</a>
                                    <a href="{{ route('status.ntp', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">NTP</a>
                                    <a href="{{ route('status.openvpn', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">OpenVPN</a>
                                    <a href="{{ route('status.queues', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Queues</a>
                                    <a href="{{ route('status.services', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Services</a>
                                    <a href="{{ route('status.system-logs', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">System
                                        Logs</a>
                                    <a href="{{ route('status.traffic-graph', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Traffic
                                        Graph</a>
                                    <a href="{{ route('status.upnp', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">UPnP
                                        IGD & PCP</a>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnostics Dropdown -->
                        <div class="relative" @click.away="diagnosticsOpen = false">
                            <button @click="diagnosticsOpen = !diagnosticsOpen"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                :class="{
                                    'border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-400': diagnosticsActive,
                                    'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700': !diagnosticsActive
                                }">
                                Diagnostics
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="diagnosticsOpen" style="display: none;"
                                class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <div class="py-1" role="menu">
                                    <a href="{{ route('diagnostics.arp-table', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">ARP
                                        Table</a>
                                    <a href="{{ route('diagnostics.authentication', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Authentication</a>
                                    <a href="{{ route('diagnostics.backup.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Backup
                                        & Restore</a>
                                    <a href="{{ route('diagnostics.command-prompt', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Command
                                        Prompt</a>
                                    <a href="{{ route('diagnostics.dns-lookup', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">DNS
                                        Lookup</a>
                                    <a href="{{ route('diagnostics.edit-file', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Edit
                                        File</a>
                                    <a href="{{ route('diagnostics.factory-defaults', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Factory
                                        Defaults</a>
                                    <a href="{{ route('diagnostics.halt-system', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Halt
                                        System</a>
                                    <a href="{{ route('diagnostics.limiter-info', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Limiter
                                        Info</a>
                                    <a href="{{ route('diagnostics.ndp-table', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">NDP
                                        Table</a>
                                    <a href="{{ route('diagnostics.packet_capture.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Packet
                                        Capture</a>
                                    <a href="{{ route('diagnostics.pf-info', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">pfInfo</a>
                                    <a href="{{ route('diagnostics.pf-top', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">pfTop</a>
                                    <a href="{{ route('diagnostics.ping', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Ping</a>
                                    <a href="{{ route('diagnostics.reboot.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Reboot</a>
                                    <a href="{{ route('diagnostics.routes', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Routes</a>
                                    <a href="{{ route('diagnostics.smart-status', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">S.M.A.R.T.
                                        Status</a>
                                    <a href="{{ route('diagnostics.sockets', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Sockets</a>
                                    <a href="{{ route('diagnostics.states', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">States</a>
                                    <a href="{{ route('diagnostics.states-summary', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">States
                                        Summary</a>
                                    <a href="{{ route('diagnostics.system-activity', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">System
                                        Activity</a>
                                    <a href="{{ route('diagnostics.tables', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Tables</a>
                                    <a href="{{ route('diagnostics.test_port.index', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Test
                                        Port</a>
                                    <a href="{{ route('diagnostics.traceroute', request()->route('firewall')) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Traceroute</a>
                                </div>
                            </div>
                        </div>

                    @endif
                    <!-- End pfSense-Style Dropdowns -->
                </div>
            </div>



            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('firewalls.index')" :active="request()->routeIs('firewalls.*')">
                {{ __('Firewalls') }}
            </x-responsive-nav-link>
            @if(Auth::user()->isGlobalAdmin())
                <x-responsive-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.*')">
                    {{ __('Companies') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('system.customization.index')"
                    :active="request()->routeIs('system.customization.*')">
                    {{ __('Customization') }}
                </x-responsive-nav-link>
            @elseif(Auth::user()->isCompanyAdmin())
                <x-responsive-nav-link :href="route('companies.show', Auth::user()->company_id)"
                    :active="request()->routeIs('companies.show')">
                    {{ __('My Company') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>