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

        /* Inputs - estils més específics */
        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="checkbox"],
        .form-control, input, textarea, select {
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            color: #ffffff !important;
            backdrop-filter: blur(10px);
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        /* Placeholder */
        input::placeholder,
        textarea::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        /* Focus */
        input:focus, textarea:focus, select:focus {
            background: rgba(255, 255, 255, 0.25) !important;
            border-color: #a78bfa !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.2rem rgba(167, 139, 250, 0.35) !important;
            outline: none !important;
        }

        /* Text dels spans */
        .text-sm, .text-gray-600 {
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
    
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
