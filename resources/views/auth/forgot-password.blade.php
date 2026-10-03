<x-guest-layout>
    <x-auth-split
        :image="asset('images/renova/auth-login.jpg')"
        image-caption="We’ll send a secure link so you can choose a new password."
        image-alt="Sunlit modern house with concrete, planting and warm natural light"
    >
        <x-slot name="imageTitle">
            Reset your<br>password.
        </x-slot>

        <x-slot name="header">
            <a href="{{ route('login') }}" class="font-medium text-forest transition hover:text-leaf">Back to log in</a>
        </x-slot>

        <div>
            <h1 class="font-serif text-[2.5rem] font-medium leading-none tracking-[-0.03em] text-forest">Forgot password</h1>
            <p class="mt-3 text-sm leading-relaxed text-mist">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>

            @session('status')
                <div class="mt-6 rounded-2xl border border-line bg-sand/70 px-4 py-3 text-sm text-forest">
                    {{ $value }}
                </div>
            @endsession

            <x-validation-errors class="mt-6" />

            <form method="POST" action="{{ route('password.email') }}" class="mt-8">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-[13px] text-mist">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="rh-input">
                </div>

                <button type="submit" class="rh-btn mt-6 w-full py-3.5">
                    Email password reset link
                </button>
            </form>
        </div>
    </x-auth-split>
</x-guest-layout>
