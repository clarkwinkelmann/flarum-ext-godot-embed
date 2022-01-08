<?php

namespace ClarkWinkelmann\GodotEmbed\Controller;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\View\Factory;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Embed implements RequestHandlerInterface
{
    protected $view;
    protected $settings;

    public function __construct(Factory $view, SettingsRepositoryInterface $settings)
    {
        $this->view = $view;
        $this->settings = $settings;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();

        // Use same CSS as forum so we can re-use FontAwesome
        $cssPath = new Uri(resolve('flarum.assets.forum')->makeCss()->getUrl());

        // Update host to be same as iframe otherwise fonts won't load
        if ($host = $this->settings->get('godot-embed.iframeHost')) {
            $cssPath = $cssPath->withHost($host);
        }

        $pathPrefix = $this->settings->get('godot-embed.basePath');

        $javascriptPath = $pathPrefix . '/godot.js';
        $basePath = $pathPrefix . '/godot'; // Godot adds .wasm automatically

        $args = [
            '--main-pack',
            Arr::get($params, 'url'),
        ];

        $userArgs = Arr::get($params, 'args');

        if ($userArgs) {
            // Separate args similarly to command line, but keep quoted strings together
            // And remove the quotes
            foreach (["'", '"'] as $quote) {
                // Use positive lookahead to match multiple occurrences right after another
                $userArgs = preg_replace_callback('~(\s|^)' . $quote . '([^"]+)' . $quote . '(?=\s|$)~', function ($matches) {
                    return $matches[1] . '' . str_replace(' ', '%%PRESERVE_SPACE%%', $matches[2]);
                }, $userArgs);
            }

            $newArgs = explode(' ', $userArgs);

            $newArgs = array_map(function ($arg) {
                return str_replace('%%PRESERVE_SPACE%%', ' ', $arg);
            }, $newArgs);

            $args = array_merge($args, $newArgs);
        }

        $url = Arr::get($params, 'url');

        $fileSizes = [
            $url => (int)Arr::get($params, 'filesize'),
            $basePath . '.wasm' => (int)$this->settings->get('godot-embed.wasmFileSize'),
        ];

        return new HtmlResponse(
            $this->view->make('godot-embed::embed')
                ->with('url', $url)
                ->with('cover', Arr::get($params, 'cover'))
                ->with('mobileCompatible', (bool)Arr::get($params, 'mobile'))
                ->with('args', $args)
                ->with('fileSizes', $fileSizes)
                ->with('cssPath', $cssPath)
                ->with('javascriptPath', $javascriptPath)
                ->with('basePath', $basePath)
                ->render()
        );
    }
}
