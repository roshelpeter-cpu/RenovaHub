<x-guest-layout>
    <x-auth-split
        :image="asset('images/renova/auth-register.jpg')"
        image-caption="Plan. Design. Collaborate. Build a better tomorrow."
        image-alt="Warm timber interior opening onto a planted courtyard"
    >
        <x-slot name="imageTitle">
            Your Vision.<br>Our Platform.
        </x-slot>

        <x-slot name="header">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-forest transition hover:text-leaf">Log in</a>
        </x-slot>

        @php
            $roles = [
                'homeowner' => ['label' => 'Homeowner', 'copy' => 'Plan and manage my own renovation.', 'icon' => 'home'],
                'contractor' => ['label' => 'Contractor', 'copy' => 'Find projects, manage tasks and collaborate.', 'icon' => 'build'],
                'designer' => ['label' => 'Designer', 'copy' => 'Collaborate with clients and bring designs to life.', 'icon' => 'pen'],
            ];
        @endphp

        <div>
            <h1 class="font-serif text-[2.45rem] font-medium leading-none tracking-[-0.03em] text-forest">Create Your Account</h1>
            <p class="mt-3 text-sm leading-relaxed text-mist">Join RenovaHub and start your renovation journey.</p>

            <x-validation-errors class="mt-6" />

            <form method="POST" action="{{ route('register') }}" class="mt-8">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-[13px] text-mist">Full name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="rh-input">
                </div>

                <div class="mt-4">
                    <label for="email" class="mb-2 block text-[13px] text-mist">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="rh-input">
                </div>

                <div class="mt-4">
                    <label for="password" class="mb-2 block text-[13px] text-mist">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="rh-input">
                </div>

                <div class="mt-4">
                    <label for="password_confirmation" class="mb-2 block text-[13px] text-mist">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="rh-input">
                </div>

                <fieldset class="mt-6">
                    <legend class="mb-3 text-[13px] text-mist">I am a</legend>
                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-3">
                        @foreach ($roles as $value => $role)
                            <label class="role-card cursor-pointer">
                                <input
                                    type="radio"
                                    name="role"
                                    value="{{ $value }}"
                                    class="peer sr-only"
                                    @checked(old('role') === $value)
                                    @if ($loop->first) required @endif
                                >
                                <span class="flex h-full flex-col rounded-2xl border border-line bg-white px-3 py-3.5 text-center transition peer-checked:border-forest peer-checked:bg-cream peer-checked:shadow-[0_10px_24px_-18px_rgba(23,63,42,0.8)] peer-focus-visible:ring-2 peer-focus-visible:ring-forest/30 hover:border-leaf/50">
                                    <span class="role-mark mx-auto mb-2 inline-flex h-9 w-9 items-center justify-center rounded-full bg-sand text-forest">
                                        @if ($role['icon'] === 'home')
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z"/></svg>
                                        @elseif ($role['icon'] === 'build')
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14.5 5.5 18.5 9.5M4 20l5.2-1.3L19.8 8.1a1.6 1.6 0 0 0-2.3-2.3L6.9 16.4 4 20Z"/></svg>
                                        @else
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h4l11-11-4-4L4 16v4Z"/><path d="m13 7 4 4"/></svg>
                                        @endif
                                    </span>
                                    <span class="text-[13px] font-medium text-charcoal">{{ $role['label'] }}</span>
                                    <span class="mt-1 text-[11px] leading-snug text-mist">{{ $role['copy'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-5">
                        <label for="terms" class="flex items-start gap-2 text-sm text-mist">
                            <input type="checkbox" name="terms" id="terms" required class="rh-check mt-0.5">
                            <span>
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline decoration-line underline-offset-4 hover:text-forest">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline decoration-line underline-offset-4 hover:text-forest">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </span>
                        </label>
                    </div>
                @endif

                <button type="submit" class="rh-btn mt-6 w-full py-3.5">
                    Create Account
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
        </div>
    </x-auth-split>
</x-guest-layout>
