<?php

namespace ClarkWinkelmann\GodotEmbed\Upload;

use Flarum\Settings\SettingsRepositoryInterface;
use FoF\Upload\Contracts\Template;
use FoF\Upload\File;
use Illuminate\Support\Arr;

class GodotTemplate implements Template
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function tag(): string
    {
        return 'godot';
    }

    public function name(): string
    {
        return 'Godot Embed';
    }

    public function description(): string
    {
        return 'Integrate with [godot] bbcode';
    }

    public function preview(File $file): string
    {
        $versionString = '';

        $versions = json_decode($this->settings->get('godot-embed.versions'), true);

        if (is_array($versions)) {
            foreach ($versions as $version) {
                if (Arr::get($version, 'default')) {
                    $versionString .= ' version=' . Arr::get($version, 'key');
                }
            }
        }

        return '[godot filesize=' . $file->size . $versionString . ']' . $file->url . '[/godot]';
    }
}
