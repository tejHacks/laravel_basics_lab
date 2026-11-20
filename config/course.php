<?php

return [
    // Folder holding curriculum.json and the lesson Markdown files.
    'path' => resource_path('lessons'),

    // Accent colour used throughout the UI.
    'accent' => env('COURSE_ACCENT', '#F53003'),

    // Links in the top menu: [label, route name, route pattern that marks it "active"].
    'demos' => [
        ['Tasks demo', 'demo.tasks.index', 'demo.tasks.*'],
        ['Auth demo', 'demo.account', 'demo.account,demo.login,demo.register'],
        ['Routes demo', 'demo.hello', 'demo.hello'],
    ],
];
