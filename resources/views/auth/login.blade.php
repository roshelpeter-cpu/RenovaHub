<x-guest-layout>
    <x-auth-split
        :image="asset('images/renova/auth-login.jpg')"
        image-caption="Create spaces that inspire, today and tomorrow."
        image-alt="Sunlit modern house with concrete, planting and warm natural light"
    >
        <x-slot name="imageTitle">
            Good Design<br>Builds Better<br>Lives
        </x-slot>

        <x-slot name="header">
            <a href="{{ url('/#how-it-works') }}" class="transition hover:text-forest">How it works</a>
            <span class="mx-2 text-line">·</span>
            <a href="{{ route('register') }}" class="font-medium text-forest transition hover:text-leaf">Create an account</a>
        </x-slot>

        <div>
            <h1 class="font-serif text-[2.6rem] font-medium leading-none tracking-[-0.03em] text-forest">Welcome Back</h1>
            <p class="mt-3 text-sm leading-relaxed text-mist">Log in to continue your renovation journey.</p>

            <x-validation-errors class="mt-6" />

            @session('status')
                <div class="mt-4 rounded-2xl border border-line bg-sand/70 px-4 py-3 text-sm text-forest">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="mt-8">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-[13px] text-mist">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="rh-input">
                </div>

                <div class="mt-4">
                    <label for="password" class="mb-2 block text-[13px] text-mist">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="rh-input">
                </div>

                <div class="mt-4 flex items-center justify-between gap-3">
                    <label for="remember_me" class="flex items-center gap-2 text-sm text-mist">
                        <input id="remember_me" type="checkbox" name="remember" class="rh-check">
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-mist underline decoration-line underline-offset-4 transition hover:text-forest">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="rh-btn mt-6 w-full py-3.5">
                    Log in
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-line"></div>
                </div>
                <div class="relative flex justify-center text-[11px] uppercase tracking-[0.18em] text-olive">
                    <span class="bg-cream px-3">Or</span>
                </div>
            </div>

            <x-google-button />

            <p class="mt-8 text-center text-sm text-mist">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-medium text-forest transition hover:text-leaf">Create one →</a>
            </p>
        </div>
    </x-auth-split>
</x-guest-layout>
