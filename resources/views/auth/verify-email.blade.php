<x-guest-layout>
    <style>
        /* Títols */
        h1, h2, h3, h4, h5 {
            color: #ffffff !important;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* Text general */
        body, p, label, span, div {
            color: #ffffff !important;
        }

        /* Labels específics */
        label, .block.font-medium.text-sm {
            color: #ffffff !important;
            font-weight: 500;
            margin-bottom: 8px;
        }

        /* Text dels spans */
        .text-sm, .text-gray-600, .text-green-600 {
            color: #ffffff !important;
        }

        /* Botons */
        button, .btn {
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            background-color: rgba(167, 139, 250, 0.3) !important;
            color: #fff !important;
            transition: all 0.2s ease;
            display: inline-block;
        }

        button:hover, .btn:hover {
            background-color: rgba(167, 139, 250, 0.5) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* Links */
        a {
            color: #c4b5fd !important;
            text-decoration: none;
        }

        a:hover {
            color: #e0d7ff !important;
            text-decoration: underline;
        }
    </style>
    
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
