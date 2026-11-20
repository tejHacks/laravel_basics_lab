<x-layout title="Your account">
    <x-demo-banner lesson="middleware" :files="['routes/web.php (the auth middleware group)', 'app/Http/Controllers/Demo/AuthController.php']">
        This page is protected by the <code>auth</code> middleware. Log out, then try to visit <code>/demos/account</code>: you'll be redirected to the login page, then sent back here afterwards (<code>redirect()->intended()</code>).
    </x-demo-banner>

    <div class="panel">
        <h1 style="margin-top:0">Hello, {{ $user->name }} 👋</h1>
        <table>
            <tr><td class="muted">Email</td><td>{{ $user->email }}</td></tr>
            <tr><td class="muted">Member since</td><td>{{ $user->created_at->toFormattedDateString() }}</td></tr>
            <tr><td class="muted">auth()->id()</td><td><code>{{ auth()->id() }}</code></td></tr>
        </table>
        <form method="POST" action="{{ route('demo.logout') }}" style="margin-top:16px">
            @csrf
            <button class="btn">Log out</button>
        </form>
    </div>
</x-layout>
