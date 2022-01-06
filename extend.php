<?php

namespace ClarkWinkelmann\GodotEmbed;

use Flarum\Extend;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),

    new Extend\Locales(__DIR__ . '/locale'),

    (new Extend\Routes('forum'))
        ->get('/godot-embed', 'godot-embed', Controller\Embed::class),

    (new Extend\View())
        ->namespace('godot-embed', __DIR__ . '/views'),

    (new Extend\Formatter)
        ->configure(ConfigureFormatter::class),

    (new Extend\ServiceProvider())
        ->register(Upload\Provider::class),
];
