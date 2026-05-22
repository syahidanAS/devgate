<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Two Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add additional security to your account using two factor authentication.') }}
        </p>
    </header>

    <div class="mt-6">
        @if(!auth()->user()->google2fa_secret)
            @if(isset($QR_Image))
                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    <p>1. Scan barcode ini menggunakan aplikasi Google Authenticator atau Authy.</p>
                    <p class="mt-2">2. Masukkan 6 digit kode OTP yang muncul untuk mengaktifkan 2FA.</p>
                </div>
                
                <div class="mb-4 bg-white p-4 inline-block rounded-lg shadow-sm border">
                    {!! $QR_Image !!}
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-500 font-mono">Secret Key: {{ $secret }}</p>
                </div>

                <form method="post" action="{{ route('2fa.enable') }}" class="mt-6 space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="otp" value="{{ __('Kode OTP') }}" />
                        <x-text-input id="otp" name="otp" type="text" class="mt-1 block w-full max-w-xs" required autofocus placeholder="123456" />
                        <x-input-error class="mt-2" :messages="$errors->get('otp')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Aktifkan 2FA') }}</x-primary-button>
                    </div>
                </form>
            @else
                <a href="{{ route('2fa.setup') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Mulai Setup 2FA
                </a>
            @endif
        @else
            <div class="p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 mb-4 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Two Factor Authentication saat ini <strong>AKTIF</strong>. Akun Anda lebih aman.</span>
            </div>

            <form method="post" action="{{ route('2fa.disable') }}" class="mt-6 space-y-6" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan 2FA?');">
                @csrf
                <div class="flex items-center gap-4">
                    <x-danger-button>{{ __('Nonaktifkan 2FA') }}</x-danger-button>
                </div>
            </form>
        @endif
    </div>
</section>
