<x-designer-layout title="{{ $concept->title }}">
    @include('designer.partials.project-header')
    <article class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="font-serif text-3xl text-[#123D2B]">{{ $concept->title }}</h2>
                <p class="mt-1 text-sm text-[#66756C]">{{ $concept->statusLabel() }}</p>
            </div>
            @can('submit', $concept)
                <form method="POST" action="{{ route('designer.concepts.submit', [$project, $concept]) }}">@csrf<button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Submit for Approval</button></form>
            @endcan
        </div>
        <p class="mt-4 text-sm leading-relaxed text-[#66756C]">{{ $concept->description }}</p>
        @if ($concept->notes)
            <p class="mt-2 text-sm text-[#66756C]">{{ $concept->notes }}</p>
        @endif
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($concept->files as $file)
                <figure>
                    <img src="{{ $file->url() }}" alt="" class="h-40 w-full rounded-xl object-cover">
                    <figcaption class="mt-1 text-xs text-[#66756C]">{{ ucfirst(str_replace('_', ' ', $file->kind)) }} · {{ $file->caption }}</figcaption>
                </figure>
            @endforeach
        </div>
        @can('update', $concept)
            <form method="POST" action="{{ route('designer.concepts.update', [$project, $concept]) }}" enctype="multipart/form-data" class="mt-6 grid gap-3 border-t border-[#ece7dc] pt-4">
                @csrf
                @method('PUT')
                <label class="text-sm">Title<input name="title" value="{{ $concept->title }}" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
                <label class="text-sm">Description<textarea name="description" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2">{{ $concept->description }}</textarea></label>
                <label class="text-sm">Notes<textarea name="notes" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2">{{ $concept->notes }}</textarea></label>
                <label class="text-sm">Add renders or plans<input name="images[]" type="file" accept="image/*" multiple></label>
                <button class="w-fit rounded-full border border-[#123D2B] px-4 py-2 text-sm text-[#123D2B]">Save changes</button>
            </form>
        @endcan
    </article>
</x-designer-layout>
