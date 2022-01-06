<?php

namespace ClarkWinkelmann\GodotEmbed\Upload;

use FoF\Upload\Contracts\Template;
use FoF\Upload\File;

class GodotTemplate implements Template
{
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
        return '[godot]' . $file->url . '[/godot]';
    }
}
