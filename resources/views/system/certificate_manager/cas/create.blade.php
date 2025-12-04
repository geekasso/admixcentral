<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Certificate Authority') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('system.certificate_manager.cas.store', $firewall) }}"
                        x-data="{ method: 'internal' }">
                        @csrf

                        <!-- Method Selection -->
                        <div class="mb-4">
                            <x-input-label for="method_select" :value="__('Method')" />
                            <select id="method_select" x-model="method"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="internal">Create an internal Certificate Authority</option>
                                <option value="import">Import an existing Certificate Authority</option>
                            </select>
                            <input type="hidden" name="method" x-bind:value="method">
                        </div>

                        <!-- Descriptive Name -->
                        <div class="mb-4">
                            <x-input-label for="descr" :value="__('Descriptive Name')" />
                            <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr"
                                :value="old('descr')" required />
                            <x-input-error :messages="$errors->get('descr')" class="mt-2" />
                        </div>

                        <!-- Internal CA Fields -->
                        <div x-show="method === 'internal'">
                            <div class="mb-4">
                                <x-input-label for="keylen" :value="__('Key Length')" />
                                <select id="keylen" name="keylen"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="2048">2048</option>
                                    <option value="4096">4096</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <x-input-label for="digest_alg" :value="__('Digest Algorithm')" />
                                <select id="digest_alg" name="digest_alg"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="sha256">SHA256</option>
                                    <option value="sha1">SHA1</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <x-input-label for="lifetime" :value="__('Lifetime (days)')" />
                                <x-text-input id="lifetime" class="block mt-1 w-full" type="number" name="lifetime"
                                    :value="old('lifetime', 3650)" />
                            </div>

                            <h3 class="text-lg font-medium mt-6 mb-2">Subject Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="dn_country" :value="__('Country Code')" />
                                    <x-text-input id="dn_country" class="block mt-1 w-full" type="text"
                                        name="dn_country" :value="old('dn_country', 'US')" maxlength="2" />
                                </div>
                                <div>
                                    <x-input-label for="dn_state" :value="__('State or Province')" />
                                    <x-text-input id="dn_state" class="block mt-1 w-full" type="text" name="dn_state"
                                        :value="old('dn_state')" />
                                </div>
                                <div>
                                    <x-input-label for="dn_city" :value="__('City')" />
                                    <x-text-input id="dn_city" class="block mt-1 w-full" type="text" name="dn_city"
                                        :value="old('dn_city')" />
                                </div>
                                <div>
                                    <x-input-label for="dn_organization" :value="__('Organization')" />
                                    <x-text-input id="dn_organization" class="block mt-1 w-full" type="text"
                                        name="dn_organization" :value="old('dn_organization')" />
                                </div>
                                <div>
                                    <x-input-label for="dn_email" :value="__('Email Address')" />
                                    <x-text-input id="dn_email" class="block mt-1 w-full" type="email" name="dn_email"
                                        :value="old('dn_email')" />
                                </div>
                                <div>
                                    <x-input-label for="dn_commonname" :value="__('Common Name')" />
                                    <x-text-input id="dn_commonname" class="block mt-1 w-full" type="text"
                                        name="dn_commonname" :value="old('dn_commonname')" />
                                </div>
                            </div>
                        </div>

                        <!-- Import CA Fields -->
                        <div x-show="method === 'import'">
                            <div class="mb-4">
                                <x-input-label for="crt" :value="__('Certificate Data')" />
                                <textarea id="crt" name="crt" rows="5"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                                <p class="text-sm text-gray-500 mt-1">Paste the certificate data in X.509 PEM format.
                                </p>
                            </div>
                            <div class="mb-4">
                                <x-input-label for="prv" :value="__('Private Key Data')" />
                                <textarea id="prv" name="prv" rows="5"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                                <p class="text-sm text-gray-500 mt-1">Paste the private key data in X.509 PEM format.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>