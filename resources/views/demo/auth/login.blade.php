<x-layout title="Log in">
    <x-demo-banner lesson="authentication" :files="['app/Http/Controllers/Demo/AuthController.php', 'routes/web.php', 'bootstrap/app.php']">
        Hand-written login with <code>Auth::attempt()</code>, session regeneration, "remember me", rate limiting and the <code>guest</code>/<code>auth</code> middleware.
        Seeded account: <code>learner@example.com</code> / <code>password</code>.
    </x-demo-banner>

    <div style="max-width: 420px; margin: 0 auto">
        <h1>Log in</h1>
        <form method="POST" action="{{ route('demo.login') }}" class="panel">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" @class(['invalid' => $errors->has('email')])>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" autocomplete="current-password">
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label style="display:flex; gap:8px; align-items:center; font-weight:500">
                    <input type="checkbox" name="remember" value="1"> Remember me
                </label>
            </div>
            <button class="btn btn-primary" style="width:100%; justify-content:center">Log in</button>
        </form>
        <p class="muted">No account? <a href="{{ route('demo.register') }}">Register</a></p>
    </div>
</x-layout>
