<section class="space-y-6">
    <header class="mb-6">
        <h2 class="text-lg font-bold dark:text-white flex items-center gap-2" style="color:#CE1126;">
            <i class="fas fa-trash-alt"></i> {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-sm dark:text-gray-400" style="color:#5a7aaa;">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div class="inline-block">
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
                style="background:linear-gradient(135deg,#CE1126,#b50c1f); box-shadow:0 3px 12px rgba(206,17,38,0.25);">
            <i class="fas fa-exclamation-triangle"></i> {{ __('Delete Account') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 space-y-6">
            @csrf
            @method('delete')

            <div>
                <h2 class="text-lg font-bold" style="color:#CE1126;">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ __('Are you sure you want to delete your account?') }}
                </h2>
                <p class="mt-2 text-sm" style="color:#5a7aaa;">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
            </div>

            <div class="border-l-4 p-4 rounded" style="border-left-color:#CE1126; background:#fff5f5;">
                <p class="text-sm font-500" style="color:#CE1126;">
                    <i class="fas fa-info-circle mr-2"></i> {{ __('This action cannot be undone.') }}
                </p>
            </div>

            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    {{ __('Confirm Password') }} <span style="color:#CE1126;">*</span>
                </label>
                <input type="password"
                        id="password"
                        name="password"
                        placeholder="{{ __('Enter your password') }}"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                               dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                        style="border-color:{{ $errors->userDeletion->has('password') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                        onfocus="this.style.borderColor='#CE1126'; this.style.boxShadow='0 0 0 3px rgba(206,17,38,0.10)'"
                        onblur="this.style.borderColor='{{ $errors->userDeletion->has('password') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'" />
                @error('password', 'userDeletion')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border text-sm font-700 transition
                               dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                        style="border-color:#c5d8f5; color:#5a7aaa; background:#f8f9fa;">
                    <i class="fas fa-times"></i> {{ __('Cancel') }}
                </button>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
                        style="background:linear-gradient(135deg,#CE1126,#b50c1f); box-shadow:0 3px 12px rgba(206,17,38,0.25);">
                    <i class="fas fa-trash-alt"></i> {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
