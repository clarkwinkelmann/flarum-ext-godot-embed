<?php

namespace ClarkWinkelmann\GodotEmbed\Controller;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\View\Factory;
use Laminas\Diactoros\Response\HtmlResponse;
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

        $pathPrefix = $this->settings->get('godot-embed.basePath');

        $javascriptPath = $pathPrefix . '/godot.js';
        $basePath = $pathPrefix . '/godot'; // Godot adds .wasm automatically

        return new HtmlResponse(
            $this->view->make('godot-embed::embed')
                ->with('url', Arr::get($params, 'url'))
                ->with('javascriptPath', $javascriptPath)
                ->with('basePath', $basePath)
                ->render()
        );
    }
}
