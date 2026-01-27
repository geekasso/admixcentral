<div class="card-modern">
    <div class="card-header-modern">
        <div class="card-icon-wrapper">
            <svg class="card-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div>
            <h3 class="card-title-modern">
                {{ __('Two Factor Authentication') }}
            </h3>
            <p class="card-subtitle-modern">
                {{ __('Add additional security to your account using two factor authentication.') }}
            </p>
        </div>
    </div>

    <div class="card-body-modern">
        <div x-data="{
        qrCode: null,
        recoveryCodes: [],
        confirming: false,
        confirmationCode: '',
        password: '',
        confirmingPassword: false,
        passwordError: null,
        showingQrCode: false,
        showingRecoveryCodes: false,
        enabled: {{ !empty($user->two_factor_secret) ? 'true' : 'false' }},
        confirmed: {{ !empty($user->two_factor_confirmed_at) ? 'true' : 'false' }},
        actionType: null, // 'enable' or 'disable' or 'showRecovery'

        startEnable() {
            this.actionType = 'enable';
            this.password = '';
            this.passwordError = null;
            $dispatch('open-modal', 'confirm-password-2fa');
            this.$nextTick(() => this.$refs.passwordInput.focus());
        },

        startDisable() {
            this.actionType = 'disable';
            this.password = '';
            this.passwordError = null;
            $dispatch('open-modal', 'confirm-password-2fa');
            this.$nextTick(() => this.$refs.passwordInput.focus());
        },
        
        startShowRecoveryCodes() {
             this.actionType = 'showRecovery';
             this.password = '';
             this.passwordError = null;
             $dispatch('open-modal', 'confirm-password-2fa');
             this.$nextTick(() => this.$refs.passwordInput.focus());
        },

        confirmPassword() {
            axios.post('/user/confirm-password', {
                password: this.password
            })
            .then(() => {
                $dispatch('close-modal', 'confirm-password-2fa');
                this.password = '';
                this.passwordError = null;

                if (this.actionType === 'enable') {
                    this.enable2FA();
                } else if (this.actionType === 'disable') {
                    this.disable2FA();
                } else if (this.actionType === 'showRecovery') {
                     this.showRecoveryCodes();
                }
            })
            .catch(error => {
                this.passwordError = error.response.data.message || 'Invalid password.';
                this.$refs.passwordInput.focus();
            });
        },

        enable2FA() {
            axios.post('/user/two-factor-authentication')
                .then(() => {
                    this.enabled = true;
                    this.confirmed = false; 
                    this.showQrCode();
                })
                .catch(error => {
                    console.error('Error enabling 2FA:', error);
                });
        },

        confirm2FA() {
            axios.post('/user/confirmed-two-factor-authentication', {
                code: this.confirmationCode
            })
            .then(() => {
                this.confirmed = true;
                $dispatch('close-modal', '2fa-setup');
                this.showingQrCode = false; 
                this.showingRecoveryCodes = true; 
                this.showRecoveryCodes();
            })
            .catch(error => {
                console.error('Error confirming 2FA:', error);
                alert('Invalid Code. Please try again.'); 
            });
        },

        disable2FA() {
            axios.delete('/user/two-factor-authentication')
                .then(() => {
                    this.enabled = false;
                    this.confirmed = false;
                    this.qrCode = null;
                    this.recoveryCodes = [];
                })
                .catch(error => {
                    console.error('Error disabling 2FA:', error);
                });
        },

        showQrCode() {
            axios.get('/user/two-factor-qr-code')
                .then(response => {
                    this.qrCode = response.data.svg;
                    this.showingQrCode = true;
                    // Open the setup modal
                    $dispatch('open-modal', '2fa-setup');
                })
                .catch(error => {
                    console.error(error);
                });
        },

        showRecoveryCodes() {
            axios.get('/user/two-factor-recovery-codes')
                .then(response => {
                    this.recoveryCodes = response.data;
                    this.showingRecoveryCodes = true;
                })
                .catch(error => {
                    console.error(error);
                });
        },

        regenerateRecoveryCodes() {
            axios.post('/user/two-factor-recovery-codes')
                .then(response => {
                    this.showRecoveryCodes();
                })
                .catch(error => {
                    console.error(error);
                });
        }
    }">
            <!-- Initial State: Enable Button -->
            <template x-if="! enabled">
                <div>
                    <x-primary-button type="button" x-on:click="startEnable">
                        {{ __('Enable') }}
                    </x-primary-button>
                </div>
            </template>

            <!-- Managed State: Enabled options -->
            <template x-if="enabled">
                <div class="space-y-4">
                    @if (empty(auth()->user()->two_factor_confirmed_at))
                        <!-- Not Confirmed Message (Edge case if page reloaded mid-setup) -->
                        <div class="text-sm text-yellow-600 dark:text-yellow-400 font-medium" x-show="! confirmed">
                            {{ __('You have enabled 2FA but have not confirmed it yet. Click "Enable" to finish setup.') }}
                        </div>
                        <x-primary-button type="button" x-on:click="startEnable" x-show="! confirmed">
                            {{ __('Finish Enabling') }}
                        </x-primary-button>
                    @endif

                    <div class="text-sm text-gray-600 dark:text-gray-400" x-show="confirmed">
                        {{ __('Two factor authentication is enabled.') }}
                    </div>

                    <div class="flex flex-wrap gap-4" x-show="confirmed">
                        <!-- Show Recovery Codes -->
                        <x-secondary-button x-on:click="startShowRecoveryCodes" x-show="! showingRecoveryCodes">
                            {{ __('Show Recovery Codes') }}
                        </x-secondary-button>

                        <!-- Disable -->
                        <x-danger-button type="button" x-on:click="startDisable">
                            {{ __('Disable') }}
                        </x-danger-button>
                    </div>

                    <!-- Recovery Codes Display -->
                    <div x-show="showingRecoveryCodes" class="mt-4" style="display: none;">
                        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                        </div>

                        <div
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 font-mono text-sm grid grid-cols-2 gap-2 max-w-xl">
                            <template x-for="code in recoveryCodes" :key="code">
                                <div x-text="code" class="text-gray-900 dark:text-gray-100"></div>
                            </template>
                        </div>

                        <div class="mt-4 flex gap-4">
                            <x-secondary-button x-on:click="regenerateRecoveryCodes">
                                {{ __('Regenerate Recovery Codes') }}
                            </x-secondary-button>

                            <x-secondary-button x-on:click="showingRecoveryCodes = false">
                                {{ __('Hide') }}
                            </x-secondary-button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Password Confirmation Modal -->
            <x-modal name="confirm-password-2fa" focusable>
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">
                                {{ __('Confirm Password') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('For your security, please confirm your password to continue.') }}
                                </p>
                                <div class="mt-4">
                                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                                    <x-text-input x-ref="passwordInput" id="password_2fa" type="password"
                                        class="mt-1 block w-full" placeholder="{{ __('Password') }}" x-model="password"
                                        @keydown.enter="confirmPassword" />

                                    <p x-show="passwordError" x-text="passwordError"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <x-primary-button class="sm:ms-3 w-full sm:w-auto justify-center" x-on:click="confirmPassword">
                        {{ __('Confirm') }}
                    </x-primary-button>
                    <x-secondary-button class="mt-3 sm:mt-0 w-full sm:w-auto justify-center"
                        x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                </div>
            </x-modal>

            <!-- 2FA Enrollment / QR Code Modal -->
            <x-modal name="2fa-setup" focusable>
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/20 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.875 14.25l1.214 1.942a2.25 2.25 0 001.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9a2.25 2.25 0 010-3.182l.981-.981c.563-.563 1.346-.837 2.126-.689l1.25.234c.54.1 1.096-.1 1.492-.536l.241-.266c.9-.99 2.517-.99 3.417 0l.241.266c.396.435.952.635 1.492.536l1.25-.234c.78-.148 1.563.125 2.126.688l.981.981A2.25 2.25 0 0121.59 9m-19.18 0l-.882 2.524a3 3 0 00.999 3.528l2.91 1.922m13.12 0l2.91-1.922a3 3 0 00.999-3.528L21.59 9m-13.12 0V5.25a2.25 2.25 0 014.5 0V9m-4.5 0h4.5" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">
                                {{ __('Finish Enabling Two Factor Authentication') }}
                            </h3>
                            <div class="mt-2 text-left">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Scan the following QR code using your phone\'s authenticator application.') }}
                                </p>

                                <div
                                    class="mt-4 p-4 bg-white dark:bg-gray-100 rounded-lg inline-block border border-gray-200">
                                    <div x-html="qrCode"></div>
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="code" value="{{ __('Code') }}" />
                                    <x-text-input id="code" type="text" name="code" class="block mt-1 w-1/2"
                                        inputmode="numeric" autofocus autocomplete="one-time-code"
                                        x-model="confirmationCode" @keydown.enter="confirm2FA" />
                                    <p class="text-sm text-gray-500 mt-2">Enter the 6-digit code from your app.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <x-primary-button class="sm:ms-3 w-full sm:w-auto justify-center" x-on:click="confirm2FA">
                        {{ __('Confirm') }}
                    </x-primary-button>
                    <x-secondary-button class="mt-3 sm:mt-0 w-full sm:w-auto justify-center"
                        x-on:click="$dispatch('close'); disable2FA()">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                </div>
            </x-modal>

        </div>
    </div>
</div>