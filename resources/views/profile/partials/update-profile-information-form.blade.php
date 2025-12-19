<section>
    <header>
        <h2 class="text-lg font-medium text-slate-200">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')
        @method('patch')

        @if ($user->imageUrl())
            <div>
                <img src="{{ $user->imageUrl() }}" alt="{{ $user->name }}" class="rounded-full h-20 w-20">
            </div>
        @endif

        <!-- Image -->
        <div>
            <x-input-label for="image" :value="__('Avatar')" />
            <x-text-input id="image" class="block mt-1 w-full" type="file" name="image"
                :value="old('image')" autofocus />
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autofocus autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-300">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-cyan-400 hover:text-cyan-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 focus:ring-offset-[#0B1120]">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-cyan-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Bio -->
        <div class="mt-4">
            <x-input-label for="bio" :value="__('Bio')" />
            <x-input-textarea id="bio" class="block mt-1 w-full" name="bio">{{ old('bio', $user->bio) }}</x-input-textarea>
            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-cyan-400 font-mono"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
