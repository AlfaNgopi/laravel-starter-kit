<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('permissions.index') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Permissions') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('Edit') }}</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Edit Permission') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Update permission details') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('permissions.update', $permission) }}" method="POST" class="max-w-md">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <x-forms.input label="Name" name="name" type="text" value="{{ old('name', $permission->name) }}" required />
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('e.g., create-posts, edit-users, delete-comments') }}</p>
                </div>

                <div class="flex gap-3">
                    <x-button type="primary">{{ __('Update') }}</x-button>
                    <a href="{{ route('permissions.index') }}">
                        <x-button type="secondary">{{ __('Cancel') }}</x-button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
