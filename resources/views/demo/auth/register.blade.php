<x-layout title="Register">
    <x-demo-banner lesson="authentication" :files="['app/Http/Controllers/Demo/AuthController.php', 'app/Models/User.php']">
        Registration with validation (<code>unique:users</code>, <code>confirmed</code>, <code>Password::defaults()</code>) and automatic password hashing.
    </x-demo-banner>

    <div style="max-width: 420px; margin: 0 auto">
        <h1>Create an account</h1>
        <form method="POST" action="{{ route('demo.register') }}" class="panel">
            @csrf
            @foreach ([
                ['name', 'Name', 'text', 'name'],
                ['email', 'Email', 'email', 'email'],
                ['password', 'Password', 'password', 'new-password'],
                ['password_confirmation', 'Confirm password', 'password', 'new-password'],
            ] as [$field, $label, $type, $autocomplete])
                <div class="field">
                    <label for="{{ $field }}">{{ $label }}</label>
                    <input id="{{ $field }}" type="{{ $type }}" name="{{ $field }}" autocomplete="{{ $autocomplete }}"
                           value="{{ $type === 'password' ? '' : old($field) }}" @class(['invalid' => $errors->has($field)])>
                    @error($field) <p class="error">{{ $message }}</p> @enderror
                </div>
            @endforeach
            <button class="btn btn-primary" style="width:100%; justify-content:center">Register</button>
        </form>
        <p class="muted">Already registered? <a href="{{ route('demo.login') }}">Log in</a></p>
    </div>
</x-layout>
