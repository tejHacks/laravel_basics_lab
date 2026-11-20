<x-layout title="Routes demo">
    <x-demo-banner lesson="route-parameters" :files="['routes/web.php', 'app/Http/Controllers/Demo/HelloController.php']">
        An invokable controller on <code>/demos/hello/{name?}</code> with <code>->whereAlpha('name')</code> and an optional <code>?shout=1</code> query string.
    </x-demo-banner>

    <div class="panel" style="text-align:center; padding: 40px 18px">
        <p style="font-size: clamp(1.6rem, 6vw, 3rem); font-weight: 800; margin: 0">
            @php($greeting = 'Hello, '.($name ?? 'stranger').'!')
            {{ $shout ? Str::upper($greeting) : $greeting }}
        </p>
    </div>

    <h2>Try these URLs</h2>
    <ul>
        <li><a href="{{ route('demo.hello') }}">/demos/hello</a>: no name, so the default is used</li>
        <li><a href="{{ route('demo.hello', 'Ada') }}">/demos/hello/Ada</a>: a route parameter</li>
        <li><a href="{{ route('demo.hello', ['name' => 'Linus', 'shout' => 1]) }}">/demos/hello/Linus?shout=1</a>: plus a query string</li>
        <li><a href="{{ url('/demos/hello/R2D2') }}">/demos/hello/R2D2</a>: digits fail <code>whereAlpha</code>, so you get a <strong>404</strong></li>
    </ul>
</x-layout>
