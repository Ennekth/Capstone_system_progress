@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="w-full max-w-sm bg-white border border-gray-200 rounded-xl shadow-sm p-8">

        <h1 class="text-2xl font-bold text-center mb-1">Inventory System</h1>
        <p class="text-sm text-gray-500 text-center mb-6">Sign in to your account</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="loginname" class="block text-sm font-medium text-gray-700 mb-1">
                    Username or Email
                </label>
                <input type="text" name="loginname" id="loginname" value="{{ old('loginname') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
            </div>

            <div>
    <label for="loginpassword" class="block text-sm font-medium text-gray-700 mb-1">
        Password
    </label>

    <div class="relative">
        <input type="password" name="loginpassword" id="loginpassword"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm
                      focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">

        <button type="button" id="togglePassword"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('loginpassword');

    toggleBtn.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
    });
</script>
@endpush

            <button type="submit"
                    class="w-full bg-gray-900 text-white text-sm font-medium py-2.5 rounded-lg
                           hover:bg-gray-800 transition-colors">
                Login
            </button>
        </form>

        

    </div>
    
@endsection