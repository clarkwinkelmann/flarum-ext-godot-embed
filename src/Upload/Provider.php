<?php

namespace ClarkWinkelmann\GodotEmbed\Upload;

use Flarum\Foundation\AbstractServiceProvider;
use FoF\Upload\Helpers\Util;

class Provider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->make(Util::class)->addRenderTemplate($this->container->make(GodotTemplate::class));
    }
}
