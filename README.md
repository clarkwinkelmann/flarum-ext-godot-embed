# Godot Embed

[![MIT license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/clarkwinkelmann/flarum-ext-godot-embed/blob/master/LICENSE.md) [![Latest Stable Version](https://img.shields.io/packagist/v/clarkwinkelmann/flarum-ext-godot-embed.svg)](https://packagist.org/packages/clarkwinkelmann/flarum-ext-godot-embed) [![Total Downloads](https://img.shields.io/packagist/dt/clarkwinkelmann/flarum-ext-godot-embed.svg)](https://packagist.org/packages/clarkwinkelmann/flarum-ext-godot-embed) [![Donate](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.me/clarkwinkelmann)

This extension adds a `[godot]<URL to pck file>[/godot]` bbcode to Flarum that renders an iframe with an embedded player.

The bbcode is registered into FoF Upload as a template.

Before you can use the extension, you need to download the templates from https://godotengine.org/download and host the `webassembly_release.zip` files inside somewhere on your server.
Then provide the public URL to that folder in the "Base path" setting in the admin panel.
For example, you can extract to `<Flarum installation>/public/assets/godot` and set `/assets/godot` in the setting.

**This extension doesn't provide any built-in security!**
For use in production, make sure the engine and game files are accessed through a CDN URL that doesn't allow access to Flarum cookies.

For example if your forum is hosted at `www.example.com`, you could configure `sandbox.example.com` as an alias in your VirtualHost.
Then use that domain for the "Iframe Host" setting as well as FoF Upload CDN URL in case files are hosted using the `local` adapter.

If you are using a naked/apex domain as canonical URL, you can't use a subdomain as the sandbox domain!
You will need a different domain!
(sorry, that's how cookies work!)

You should add Apache or nginx rewrites for the following URLs:

- `/godot-embed` on main Flarum domain: block access with 401 or 404. Prevents abuse because it can load arbitrary files via query parameter.
- `/assets/files/*` on main Flarum domain: block access with 401 or 404 (when using FoF Upload `local` adapter). Malicious actors could use the liberal file validation to upload HTML files and trick users to visit for XSS.
- `/` (or anything except `/godot-embed` and `/assets/files/*`) on sandbox domain: redirect to main domain. This prevents anyone from accidentally trying to login on wrong domain or search engines from indexing duplicate content.

## Installation

    composer require clarkwinkelmann/flarum-ext-godot-embed

## Support

This extension is under **minimal maintenance**.

It was developed for a client and released as open-source for the benefit of the community.
I might publish simple bugfixes or compatibility updates for free.

You can [contact me](https://clarkwinkelmann.com/flarum) to sponsor additional features or updates.

Support is offered on a "best effort" basis through the Flarum community thread.

## Links

- [GitHub](https://github.com/clarkwinkelmann/flarum-ext-godot-embed)
- [Packagist](https://packagist.org/packages/clarkwinkelmann/flarum-ext-godot-embed)
