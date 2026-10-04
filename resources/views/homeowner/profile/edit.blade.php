<x-homeowner-layout title="Profile">
    <h1 class="font-serif text-3xl text-forest sm:text-4xl">Profile</h1>
    <div class="mt-6 grid gap-6 lg:grid-cols-[16rem_minmax(0,1fr)]">
        <aside class="rounded-3xl border border-[#ece7dc] bg-white p-5 text-center shadow-sm">
            <img src="{{ $user->profile_photo_url }}" alt="" class="mx-auto h-24 w-24 rounded-full object-cover">
            <p class="mt-3 font-serif text-2xl text-forest">{{ $user->name }}</p>
            <p class="text-sm text-mist">Homeowner</p>
            <dl class="mt-4 space-y-2 text-sm text-left">
                <div class="flex justify-between"><dt class="text-mist">Active</dt><dd>{{ $active }}</dd></div>
                <div class="flex justify-between"><dt class="text-mist">Completed</dt><dd>{{ $completed }}</dd></div>
                <div class="flex justify-between"><dt class="text-mist">Member since</dt><dd>{{ $user->created_at->format('M Y') }}</dd></div>
            </dl>
        </aside>
        <form method="POST" action="{{ route('homeowner.profile.update') }}" enctype="multipart/form-data" class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            @csrf
            @method('PUT')
            <x-validation-errors class="mb-4" />
            <div><label for="name" class="mb-1 block text-sm">Full name</label><input id="name" name="name" required value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-line px-3 py-2 text-sm"></div>
            <div class="mt-4"><label class="mb-1 block text-sm">Email</label><p class="rounded-2xl bg-[#F6F1E7] px-3 py-2 text-sm">{{ $user->email }}</p></div>
            <div class="mt-4"><label for="phone" class="mb-1 block text-sm">Phone</label><input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-2xl border border-line px-3 py-2 text-sm"></div>
            <div class="mt-4"><label for="address" class="mb-1 block text-sm">Address</label><input id="address" name="address" value="{{ old('address', $user->address) }}" class="w-full rounded-2xl border border-line px-3 py-2 text-sm"></div>
            <div class="mt-4"><label for="photo" class="mb-1 block text-sm">Profile photo</label><input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="text-sm"></div>
            <button class="mt-6 rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory">Save profile</button>
        </form>
    </div>
</x-homeowner-layout>
