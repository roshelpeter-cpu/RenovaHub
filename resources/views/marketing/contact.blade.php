<section id="contact" class="scroll-mt-24 bg-forest text-ivory">
    <div class="mx-auto max-w-[1240px] px-5 py-16 lg:px-8 lg:py-20">
        <div class="grid items-start gap-10 lg:grid-cols-[0.92fr_1.08fr] lg:gap-14">
            <div>
                <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-white/60">Contact</p>
                <h2 class="mt-3 font-serif text-[2.4rem] font-medium leading-[1.08] tracking-[-0.03em] sm:text-5xl">
                    Let's Build Better<br>Spaces Together
                </h2>
                <p class="mt-4 max-w-md text-sm leading-relaxed text-white/75">
                    Have a question or want to work with us? We'd love to hear from you.
                </p>

                <a href="{{ route('register') }}" class="mt-7 inline-flex items-center gap-2 rounded-full bg-ivory px-5 py-3 text-sm font-medium text-forest transition hover:bg-sand">
                    Get Started
                    <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>

                <dl class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-[11px] uppercase tracking-[0.16em] text-white/50">Email</dt>
                        <dd class="mt-1 text-sm"><a href="mailto:hello@renovahub.com" class="hover:text-sand">hello@renovahub.com</a></dd>
                    </div>
                    <div>
                        <dt class="text-[11px] uppercase tracking-[0.16em] text-white/50">Phone</dt>
                        <dd class="mt-1 text-sm">+94 11 234 5678</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] uppercase tracking-[0.16em] text-white/50">Location</dt>
                        <dd class="mt-1 text-sm">Colombo, Sri Lanka</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] uppercase tracking-[0.16em] text-white/50">Working Hours</dt>
                        <dd class="mt-1 text-sm">Mon – Fri, 8:00 AM – 6:00 PM</dd>
                    </div>
                </dl>
            </div>

            <form data-contact-form class="rounded-[28px] bg-ivory p-6 text-charcoal shadow-[0_24px_50px_-32px_rgba(0,0,0,0.45)] sm:p-8">
                <div data-contact-fields class="space-y-4">
                    <div>
                        <label for="contact-name" class="mb-2 block text-[13px] text-mist">Full name</label>
                        <input id="contact-name" name="name" type="text" required class="rh-input" autocomplete="name">
                    </div>
                    <div>
                        <label for="contact-email" class="mb-2 block text-[13px] text-mist">Email address</label>
                        <input id="contact-email" name="email" type="email" required class="rh-input" autocomplete="email">
                    </div>
                    <div>
                        <label for="contact-subject" class="mb-2 block text-[13px] text-mist">Subject</label>
                        <input id="contact-subject" name="subject" type="text" required class="rh-input">
                    </div>
                    <div>
                        <label for="contact-message" class="mb-2 block text-[13px] text-mist">Message</label>
                        <textarea id="contact-message" name="message" rows="5" required class="rh-input resize-y"></textarea>
                    </div>
                    <button type="submit" class="rh-btn w-full py-3.5 sm:w-auto">
                        Send Message
                        <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </button>
                </div>
                <div data-contact-success class="hidden py-10 text-center">
                    <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-sand text-forest">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m5 12 5 5L20 7"/></svg>
                    </span>
                    <h3 class="mt-5 font-serif text-3xl text-forest">Message received</h3>
                    <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-mist">Thank you. This note stays on your screen for now — a member of the RenovaHub studio will be in touch once the inbox is connected.</p>
                </div>
            </form>
        </div>

        <div class="mt-14 grid gap-8 border-t border-white/15 pt-8 sm:grid-cols-3">
            <div>
                <h3 class="text-sm font-medium">Sustainable Spaces</h3>
                <p class="mt-1 text-sm text-white/65">Eco-friendly materials and practices.</p>
            </div>
            <div>
                <h3 class="text-sm font-medium">Trusted Professionals</h3>
                <p class="mt-1 text-sm text-white/65">Verified designers and contractors.</p>
            </div>
            <div>
                <h3 class="text-sm font-medium">Beautiful Results</h3>
                <p class="mt-1 text-sm text-white/65">Homes that feel calm, stylish and lasting.</p>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-2 border-t border-white/10 pt-6 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} RenovaHub. Colombo, Sri Lanka.</p>
            <p>Renovation, architecture and collaboration in one workspace.</p>
        </div>
    </div>
</section>
