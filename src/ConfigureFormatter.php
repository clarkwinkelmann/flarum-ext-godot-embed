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
            '[GODOT filesize={INT?} version={SIMPLETEXT?} cover={URL2?} args={TEXT?} width={NUMBER1;defaultValue=600} height={NUMBER2;defaultValue=400} touch={CHOICE=1,yes?} autoload={CHOICE2=1,yes?} toolbar={CHOICE3=left,right,hidden;defaultValue=right}]{URL}[/GODOT]',
            '<div class="godot-embed" style="--godot-embed-width: {@width}; --godot-embed-height: {@height};"><div class="godot-embed-wrapper"><iframe src="{$GODOT_EMBED_URL}?url={URL}&filesize={@filesize}&version={@version}&cover={@cover}&args={@args}&touch={@touch}&autoload={@autoload}&toolbar={@toolbar}" allowfullscreen></iframe></div></div>'
        );
    }
}
