<section id="how-it-works" class="scroll-mt-24">
    <div class="mx-auto max-w-[1240px] px-5 py-16 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="How It Works">
            A Simple Process,<br>A Better Renovation
        </x-section-heading>
        <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-mist">
            From idea to completion, RenovaHub keeps every conversation, decision and update connected.
        </p>

        @php
            $steps = [
                ['01', 'Create Your Project', 'Set your project goals, budget and timeline.'],
                ['02', 'Invite Your Team', 'Add homeowners, designers and contractors.'],
                ['03', 'Plan & Assign Tasks', 'Break the project into tasks and assign responsibilities.'],
                ['04', 'Manage Quotations', 'Request, compare and approve quotations.'],
                ['05', 'Track Progress', 'Monitor project progress and updates.'],
                ['06', 'Complete Your Renovation', 'Finalize the project and enjoy your new space.'],
            ];
        @endphp

        <ol class="process mt-12">
            @foreach ($steps as [$index, $title, $copy])
                <li class="process-step">
                    <span class="process-index">{{ $index }}</span>
                    <h3 class="text-sm font-medium text-charcoal">{{ $title }}</h3>
                    <p class="mt-2 text-[13px] leading-relaxed text-mist lg:px-1">{{ $copy }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
