<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, user-scalable=no"/>
    <title>{{ $translator->trans('clarkwinkelmann-godot-embed.embed.title') }}</title>
    <link rel="stylesheet" href="{{ $cssPath }}"/>
    <style>
        html, body {
            overflow: hidden;
            min-height: 100vh;
        }

        body {
            touch-action: none;
            margin: 0;
            border: 0 none;
            padding: 0;
            text-align: center;
            background-color: black;
        }

        #canvas {
            display: block;
            margin: 0;
            color: white;
        }

        #canvas:focus {
            outline: none;
        }

        .godot {
            font-family: 'Noto Sans', 'Droid Sans', Arial, sans-serif;
            color: #e0e0e0;
            background-color: #3b3943;
            background-image: linear-gradient(to bottom, #403e48, #35333c);
            border: 1px solid #45434e;
            box-shadow: 0 0 1px 1px #2f2d35;
        }

        /* Status display
         * ============== */

        #status {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            /* don't consume click events - make children visible explicitly */
            visibility: hidden;
        }

        #status-progress {
            width: 366px;
            height: 7px;
            background-color: #38363A;
            border: 1px solid #444246;
            padding: 1px;
            box-shadow: 0 0 2px 1px #1B1C22;
            border-radius: 2px;
            visibility: visible;
        }

        @media only screen and (orientation: portrait) {
            #status-progress {
                width: 61.8%;
            }
        }

        #status-progress-inner {
            height: 100%;
            width: 0;
            box-sizing: border-box;
            transition: width 0.5s linear;
            background-color: #202020;
            border: 1px solid #222223;
            box-shadow: 0 0 1px 1px #27282E;
            border-radius: 3px;
        }

        #status-indeterminate {
            height: 42px;
            visibility: visible;
            position: relative;
        }

        #status-indeterminate > div {
            width: 4.5px;
            height: 0;
            border-style: solid;
            border-width: 9px 3px 0 3px;
            border-color: #2b2b2b transparent transparent transparent;
            transform-origin: center 21px;
            position: absolute;
        }

        #status-indeterminate > div:nth-child(1) {
            transform: rotate(22.5deg);
        }

        #status-indeterminate > div:nth-child(2) {
            transform: rotate(67.5deg);
        }

        #status-indeterminate > div:nth-child(3) {
            transform: rotate(112.5deg);
        }

        #status-indeterminate > div:nth-child(4) {
            transform: rotate(157.5deg);
        }

        #status-indeterminate > div:nth-child(5) {
            transform: rotate(202.5deg);
        }

        #status-indeterminate > div:nth-child(6) {
            transform: rotate(247.5deg);
        }

        #status-indeterminate > div:nth-child(7) {
            transform: rotate(292.5deg);
        }

        #status-indeterminate > div:nth-child(8) {
            transform: rotate(337.5deg);
        }

        #status-notice {
            margin: 0 100px;
            line-height: 1.3;
            visibility: visible;
            padding: 4px 6px;
        }
    </style>
</head>
<body>
<canvas id="canvas">
    {{ $translator->trans('clarkwinkelmann-godot-embed.embed.canvas-unsupported') }}
</canvas>
<div id="status">
    <div id="status-progress" style="display: none;" oncontextmenu="event.preventDefault();">
        <div id="status-progress-inner"></div>
    </div>
    <div id="status-indeterminate" style="display: none;" oncontextmenu="event.preventDefault();">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div id="status-notice" class="godot" style="display: none;"></div>
</div>
<div class="godot-start" id="js-load" style="@if ($cover) background-image: url({{ $cover }}) @endif">
    <div>
        <i class="icon fas fa-play"></i>
        <div>{{ $translator->trans('clarkwinkelmann-godot-embed.embed.load-game') }}</div>
    </div>
</div>
<div class="godot-toolbar">
    <div class="godot-toolbar-handle">
        <i class="fas fa-caret-left"></i>
    </div>
    <button class="Button Button--block" id="js-quit">
        <i class="Button-icon icon fas fa-sign-out-alt"></i>
        <span class="Button-label">{{ $translator->trans('clarkwinkelmann-godot-embed.embed.quit-game') }}</span>
    </button>
    <button class="Button Button--block" id="js-restart">
        <i class="Button-icon icon fas fa-redo"></i>
        <span class="Button-label">{{ $translator->trans('clarkwinkelmann-godot-embed.embed.restart-game') }}</span>
    </button>
    <button class="Button Button--block" id="js-fullscreen">
        <i class="Button-icon icon fas fa-expand"></i>
        <span class="Button-label">{{ $translator->trans('clarkwinkelmann-godot-embed.embed.full-screen') }}</span>
    </button>
</div>
<div class="AlertManager godot-mobile-compatibility" id="js-mobile-compatibility">
    @unless($mobileCompatible)
        <div class="AlertManager-alert">
            <div class="Alert Alert--danger">
            <span class="Alert-body">
                {{ $translator->trans('clarkwinkelmann-godot-embed.embed.mobile-incompatible') }}
            </span>
            </div>
        </div>
    @endunless
</div>

<script type="text/javascript" src="{{ $javascriptPath }}"></script>
<script type="text/javascript">//<![CDATA[
    (function () {
        const engine = new Engine({
            args: @json($args),
            fileSizes: @json($fileSizes),
            experimentalVK：@json($virtualkey),
            onProgress: function (current, total) {
                if (total > 0) {
                    statusProgressInner.style.width = current / total * 100 + '%';
                    setStatusMode('progress');
                    if (current === total) {
                        // wait for progress bar animation
                        setTimeout(() => {
                            setStatusMode('indeterminate');
                        }, 500);
                    }
                } else {
                    setStatusMode('indeterminate');
                }
            },
            onExit: function () {
                quitting = false;
                updateQuitIcon();

                // Hide old canvas so it doesn't appear before next game loads
                // Can't use context to clear rectangle because we're not sure what kind of context will be used by the engine
                const canvas = document.getElementById('canvas');
                canvas.width = 0;
                canvas.height = 0;

                // Allow loading screen to re-appear
                initializing = true;

                if (restarting) {
                    startGame();
                } else {
                    // If the game quits by itself, we go back to the loading screen
                    // that way the canvas doesn't continue to show a frozen image
                    document.getElementById('js-load').style.display = 'flex';

                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    }

                    // Safari
                    if (document.webkitFullscreenElement) {
                        document.webkitExitFullscreen();
                    }
                }
            },
            onPrint: function () {
                // Same as default but with prefix
                console.log.apply(console, [@json($consolePrefix)].concat(Array.from(arguments)));
            },
            onPrintError: function (var_args) {
                console.error.apply(console, [@json($consolePrefix)].concat(Array.from(arguments)));
            },
        });

        const INDETERMINATE_STATUS_STEP_MS = 100;
        const statusProgress = document.getElementById('status-progress');
        const statusProgressInner = document.getElementById('status-progress-inner');
        const statusIndeterminate = document.getElementById('status-indeterminate');
        const statusNotice = document.getElementById('status-notice');

        let initializing = true;
        let statusMode = 'hidden';
        let restarting = false;
        let quitting = false;

        let animationCallbacks = [];

        function animate(time) {
            animationCallbacks.forEach(callback => callback(time));
            requestAnimationFrame(animate);
        }

        requestAnimationFrame(animate);

        function setStatusMode(mode) {
            if (statusMode === mode || !initializing)
                return;
            [statusProgress, statusIndeterminate, statusNotice].forEach(elem => {
                elem.style.display = 'none';
            });
            animationCallbacks = animationCallbacks.filter(function (value) {
                return (value !== animateStatusIndeterminate);
            });
            switch (mode) {
                case 'progress':
                    statusProgress.style.display = 'block';
                    break;
                case 'indeterminate':
                    statusIndeterminate.style.display = 'block';
                    animationCallbacks.push(animateStatusIndeterminate);
                    break;
                case 'notice':
                    statusNotice.style.display = 'block';
                    break;
                case 'hidden':
                    break;
                default:
                    throw new Error('Invalid status mode');
            }
            statusMode = mode;
        }

        function animateStatusIndeterminate(ms) {
            const i = Math.floor(ms / INDETERMINATE_STATUS_STEP_MS % 8);
            if (statusIndeterminate.children[i].style.borderTopColor === '') {
                Array.prototype.slice.call(statusIndeterminate.children).forEach(child => {
                    child.style.borderTopColor = '';
                });
                statusIndeterminate.children[i].style.borderTopColor = '#dfdfdf';
            }
        }

        function setStatusNotice(text) {
            while (statusNotice.lastChild) {
                statusNotice.removeChild(statusNotice.lastChild);
            }
            text.split('\n').forEach((line) => {
                statusNotice.appendChild(document.createTextNode(line));
                statusNotice.appendChild(document.createElement('br'));
            });
        }

        function displayFailureNotice(err) {
            const msg = err.message || err;
            console.error(msg);
            setStatusNotice(msg);
            setStatusMode('notice');
            initializing = false;
        }

        function updateQuitIcon() {
            document.getElementById('js-quit').querySelector('.icon').className = 'Button-icon icon fas fa-' + (quitting ? 'spinner fa-pulse' : 'sign-out-alt');
        }

        function updateRestartIcon() {
            document.getElementById('js-restart').querySelector('.icon').className = 'Button-icon icon fas fa-' + (restarting ? 'spinner fa-pulse' : 'redo');
        }

        function startGame() {
            // Hide compatibility message if user decides to start game anyway
            document.getElementById('js-mobile-compatibility').style.display = 'none';

            if (!Engine.isWebGLAvailable()) {
                displayFailureNotice(@json($translator->trans('clarkwinkelmann-godot-embed.embed.webgl-not-available')));
            } else {
                setStatusMode('indeterminate');

                Promise.all([
                    engine.init(@json($basePath)),
                    engine.preloadFile(@json($url)),
                ]).then(function () {
                    setStatusMode('indeterminate');

                    restarting = false;
                    updateRestartIcon();

                    return engine.start({}).then(() => {
                        setStatusMode('hidden');
                        initializing = false;
                    }, displayFailureNotice);
                });
            }
        }

        document.getElementById('js-load').addEventListener('click', function () {
            this.style.display = 'none';

            startGame();
        });

        @if ($autoload)
        document.getElementById('js-load').style.display = 'none';
        startGame();
        @endif

        document.getElementById('js-quit').addEventListener('click', function () {
            if (quitting && confirm(@json($translator->trans('clarkwinkelmann-godot-embed.embed.force-quit')))) {
                setStatusMode('indeterminate');
                
                const url = new URL(window.location.href);
                
                // On next page load, never run automatically
                url.searchParams.delete('autoload');

                window.location.href = url.href;

                return;
            }

            engine.requestQuit()

            quitting = true;
            updateQuitIcon();
        });

        document.getElementById('js-restart').addEventListener('click', function () {
            if (restarting && confirm(@json($translator->trans('clarkwinkelmann-godot-embed.embed.force-quit')))) {
                setStatusMode('indeterminate');

                const url = new URL(window.location.href);

                // On next page load, run automatically
                url.searchParams.set('autoload', '1');

                window.location.href = url.href;

                return;
            }

            engine.requestQuit();

            restarting = true;
            updateRestartIcon();

            // The game will restart inside onExit callback
        });

        function fullscreenFallback() {
            if (confirm(@json($translator->trans('clarkwinkelmann-godot-embed.embed.fullscreen-not-available')))) {
                window.open(window.location.href);
            }
        }

        document.getElementById('js-fullscreen').addEventListener('click', function () {
            const canvas = document.getElementById('canvas');

            if (canvas.requestFullscreen) {
                canvas.requestFullscreen().catch(fullscreenFallback);
                return;
            }

            // Safari
            if (canvas.webkitRequestFullscreen) {
                canvas.webkitRequestFullscreen().catch(fullscreenFallback);
                return;
            }

            fullscreenFallback();
        });
    })();

    //]]></script>
</body>
</html>
