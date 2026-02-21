<section>
    <header class="mb-6">
        <h2 class="text-lg font-bold dark:text-white" style="color:#003087;">
            <i class="fas fa-id-card mr-2" style="color:#CE1126;"></i> {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm dark:text-gray-400" style="color:#5a7aaa;">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        {{-- Name Field --}}
        <div>
            <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                {{ __('Name') }} <span style="color:#CE1126;">*</span>
            </label>
            <input type="text" id="name" name="name" 
                   value="{{ old('name', $user->name) }}"
                   placeholder="Enter your full name"
                   required autofocus autocomplete="name"
                   class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                          dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                   style="border-color:{{ $errors->has('name') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                   onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                   onblur="this.style.borderColor='{{ $errors->has('name') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'" />
            @error('name')
                <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email Field --}}
        <div>
            <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                {{ __('Email Address') }} <span style="color:#CE1126;">*</span>
            </label>
            <input type="email" id="email" name="email" 
                   value="{{ old('email', $user->email) }}"
                   placeholder="Enter your email address"
                   required autocomplete="username"
                   class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                          dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                   style="border-color:{{ $errors->has('email') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                   onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                   onblur="this.style.borderColor='{{ $errors->has('email') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'" />
            @error('email')
                <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-lg p-3" style="background:#fff3cd; border:1px solid #ffc107;">
                    <p class="text-sm" style="color:#856404;">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="font-semibold ml-1" style="color:#0057B8;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm" style="color:#15803d;">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3 pt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
                    style="background:linear-gradient(135deg,#0057B8,#003087); box-shadow:0 3px 12px rgba(0,87,184,0.25);">
                <i class="fas fa-save"></i> {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-500 flex items-center gap-2"
                    style="color:#15803d;">
                    <i class="fas fa-check-circle"></i> {{ __('Saved successfully!') }}
                </p>
            @endif
        </div>
    </form>
</section>
