<section id="contact" class="relative scroll-mt-24 overflow-hidden bg-[#0e291c] text-ivory">
    <div class="absolute inset-y-0 right-0 hidden w-[54%] lg:block" aria-hidden="true">
        <img src="{{ asset('images/renova/auth-register.jpg') }}" alt="" class="h-full w-full object-cover object-[70%_center]">
        <div class="absolute inset-0 bg-gradient-to-r from-[#0e291c] via-[#0e291c]/55 to-[#0e291c]/10"></div>
    </div>
    <div class="pointer-events-none absolute bottom-0 left-0 hidden h-28 w-40 text-[#6f8f62]/40 lg:block" aria-hidden="true">
        <x-leaf class="h-28 w-40" />
    </div>

    <div class="relative mx-auto grid max-w-[1200px] items-center gap-10 px-5 py-16 lg:grid-cols-[0.92fr_1.08fr] lg:gap-8 lg:px-6 lg:py-20">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-white/55">Contact</p>
            <h2 class="mt-3 font-serif text-[2.35rem] font-medium leading-[1.05] tracking-[-0.03em] sm:text-[2.85rem]">
                Let's Build Better<br>Spaces Together
                <x-leaf class="ml-1 inline h-7 w-7 -translate-y-1 text-[#c5d5b8] sm:h-8 sm:w-8" />
            </h2>
            <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/75">
                Have a question or want to work with us? We'd love to hear from you.
            </p>

            <dl class="mt-8 space-y-4 text-sm">
                @foreach ([
                    ['email', 'Email', 'hello@renovahub.com', 'mailto:hello@renovahub.com'],
                    ['phone', 'Phone', '+94 11 234 5678', null],
                    ['pin', 'Location', 'Colombo, Sri Lanka', null],
                    ['clock', 'Working Hours', 'Mon – Fri, 8:00 AM – 6:00 PM', null],
                ] as [$icon, $label, $value, $href])
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-white/20 text-white/80" aria-hidden="true">
                            @if ($icon === 'email')
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 6h16v12H4Z"/><path d="m4 7 8 6 8-6"/></svg>
                            @elseif ($icon === 'phone')
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 3.5h3l1.5 4-2 1.2a12 12 0 0 0 5.8 5.8l1.2-2 4 1.5v3A2 2 0 0 1 18.5 19 15.5 15.5 0 0 1 5 5.5 2 2 0 0 1 7 3.5Z"/></svg>
                            @elseif ($icon === 'pin')
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="8"/><path d="M12 8v4.5l3 2"/></svg>
                            @endif
                        </span>
                        <div>
                            <dt class="text-[11px] uppercase tracking-[0.14em] text-white/45">{{ $label }}</dt>
                            <dd class="mt-0.5 text-white/90">
                                @if ($href)
                                    <a href="{{ $href }}" class="hover:text-sand">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    </div>
                @endforeach
            </dl>
        </div>

        <form data-contact-form class="rounded-[26px] bg-white p-6 text-charcoal shadow-[0_28px_60px_-28px_rgba(0,0,0,0.55)] sm:p-7 lg:ml-auto lg:w-full lg:max-w-[460px]">
            <div data-contact-fields>
                <h3 class="text-[1.35rem] font-semibold tracking-[-0.02em] text-charcoal">Send Us a Message</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-mist">Fill in the form below and we'll get back to you as soon as possible.</p>
                <div class="mt-5 space-y-3">
                    <label class="rh-contact-field">
                        <span class="rh-contact-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="3.2"/><path d="M5.5 19.2a6.5 6.5 0 0 1 13 0"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] text-mist">Full Name</span>
                            <input name="name" type="text" required autocomplete="name" placeholder="Enter your full name" class="rh-contact-input">
                        </span>
                    </label>
                    <label class="rh-contact-field">
                        <span class="rh-contact-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 6h16v12H4Z"/><path d="m4 7 8 6 8-6"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] text-mist">Email Address</span>
                            <input name="email" type="email" required autocomplete="email" placeholder="Enter your email address" class="rh-contact-input">
                        </span>
                    </label>
                    <label class="rh-contact-field">
                        <span class="rh-contact-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 7h10l6 5-6 5H4Z"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] text-mist">Subject</span>
                            <select name="subject" required class="rh-contact-input">
                                <option value="" disabled selected>Select a subject</option>
                                <option>General enquiry</option>
                                <option>Project support</option>
                                <option>Partnership</option>
                                <option>Something else</option>
                            </select>
                        </span>
                    </label>
                    <label class="rh-contact-field items-start">
                        <span class="rh-contact-icon mt-0.5" aria-hidden="true">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 6.5h14v9H8l-3 2.5v-2.5H5Z"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] text-mist">Message</span>
                            <textarea name="message" rows="3" required placeholder="Write your message here..." class="rh-contact-input resize-y"></textarea>
                        </span>
                    </label>
                    <button type="submit" class="rh-btn mt-2 w-full py-3.5">
                        Send Message
                        <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </button>
                </div>
            </div>
            <div data-contact-success class="hidden py-10 text-center">
                <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#e7f0e4] text-forest">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m5 12 5 5L20 7"/></svg>
                </span>
                <h3 class="mt-5 font-serif text-3xl text-forest">Message received</h3>
                <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-mist">Thank you. A member of the RenovaHub studio will be in touch once the inbox is connected.</p>
            </div>
        </form>
    </div>
</section>
