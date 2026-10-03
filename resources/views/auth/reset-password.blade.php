<x-guest-layout>
    <x-auth-split
        :image="asset('images/renova/auth-login.jpg')"
        image-caption="Choose a new password and return to your renovation workspace."
        image-alt="Sunlit modern house with concrete, planting and warm natural light"
    >
        <x-slot name="imageTitle">
            Choose a<br>new password.
        </x-slot>

        <x-slot name="header">
            <a href="{{ route('login') }}" class="font-medium text-forest transition hover:text-leaf">Back to log in</a>
        </x-slot>

        <div>
            <h1 class="font-serif text-[2.5rem] font-medium leading-none tracking-[-0.03em] text-forest">Reset password</h1>
            <p class="mt-3 text-sm leading-relaxed text-mist">Enter a new password for your RenovaHub account.</p>

            <x-validation-errors class="mt-6" />

            <form method="POST" action="{{ route('password.update') }}" class="mt-8">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="mb-2 block text-[13px] text-mist">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="rh-input">
                </div>

                <div class="mt-4">
                    <label for="password" class="mb-2 block text-[13px] text-mist">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="rh-input">
                </div>

                <div class="mt-4">
                    <label for="password_confirmation" class="mb-2 block text-[13px] text-mist">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="rh-input">
                </div>

                <button type="submit" class="rh-btn mt-6 w-full py-3.5">
                    Reset password
                </button>
            </form>
        </div>
    </x-auth-split>
</x-guest-layout>
