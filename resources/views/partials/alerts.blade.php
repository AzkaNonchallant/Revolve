@if (session('success'))
    <div class="alert alert-success" role="alert">
        <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="m9 12 2 2 4-4"/>
        </svg>
        <div class="flex-grow">{{ session('success') }}</div>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error" role="alert">
        <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="m15 9-6 6M9 9l6 6"/>
        </svg>
        <div class="flex-grow">{{ session('error') }}</div>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success" role="alert">
        <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="m9 12 2 2 4-4"/>
        </svg>
        <div class="flex-grow">{{ session('status') }}</div>
    </div>
@endif