<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Akun Anda dilindungi dengan Autentikasi 2-Langkah. Harap masukkan 6 digit kode OTP dari aplikasi Authenticator Anda (seperti Google Authenticator atau Authy).') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex items-center justify-between mt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Batal & Logout') }}
            </button>
        </form>

        <form method="POST" action="{{ route('2fa.verify') }}" class="inline-block">
            @csrf
            <!-- OTP -->
            <div class="flex items-center gap-4">
                <div>
                    <x-text-input id="otp" class="block w-full" type="text" name="otp" required autofocus autocomplete="one-time-code" placeholder="Kode OTP" />
                </div>
                <x-primary-button>
                    {{ __('Verifikasi') }}
                </x-primary-button>
            </div>
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </form>
    </div>
</x-guest-layout>
