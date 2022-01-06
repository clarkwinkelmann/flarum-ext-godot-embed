<?php

namespace ClarkWinkelmann\GodotEmbed;

use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Laminas\Diactoros\Uri;
use s9e\TextFormatter\Configurator;

class ConfigureFormatter
{
    protected $url;
    protected $settings;

    public function __construct(UrlGenerator $url, SettingsRepositoryInterface $settings)
    {
        $this->url = $url;
        $this->settings = $settings;
    }

    public function __invoke(Configurator $config)
    {
        $iframeUrl = new Uri($this->url->to('forum')->route('godot-embed'));

        if ($host = $this->settings->get('godot-embed.iframeHost')) {
            $iframeUrl = $iframeUrl->withHost($host);
        }

        $config->rendering->parameters['GODOT_EMBED_URL'] = $iframeUrl;

        $config->BBcodes->addCustom(
            '[GODOT]{URL}[/GODOT]',
            '<div class="godot-embed"><iframe src="{$GODOT_EMBED_URL}?url={URL}"></iframe></div>'
        );
    }
}
