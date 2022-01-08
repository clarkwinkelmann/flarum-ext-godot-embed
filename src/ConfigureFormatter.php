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
            '[GODOT cover={URL2?} pck_size={NUMBER3} args={TEXT?} width={NUMBER1;defaultValue=600} height={NUMBER2;defaultValue=400}]{URL}[/GODOT]',
            '<div class="godot-embed" style="--godot-embed-width: {@width}; --godot-embed-height: {@height};"><div class="godot-embed-wrapper"><iframe src="{$GODOT_EMBED_URL}?url={URL}&pck_size={@pck_size}&cover={@cover}&args={@args}" allowfullscreen></iframe></div></div>'
        );
    }
}
