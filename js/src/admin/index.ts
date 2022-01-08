import app from 'flarum/admin/app';

app.initializers.add('godot-embed', () => {
    app.extensionData
        .for('clarkwinkelmann-godot-embed')
        .registerSetting({
            type: 'text',
            setting: 'godot-embed.basePath',
            label: app.translator.trans('clarkwinkelmann-godot-embed.admin.settings.basePath'),
            help: app.translator.trans('clarkwinkelmann-godot-embed.admin.settings.basePathHelp'),
        })
        .registerSetting({
            type: 'number',
            min: 0,
            setting: 'godot-embed.wasmFileSize',
            label: app.translator.trans('clarkwinkelmann-godot-embed.admin.settings.wasmFileSize'),
            help: app.translator.trans('clarkwinkelmann-godot-embed.admin.settings.wasmFileSizeHelp'),
        })
        .registerSetting({
            type: 'text',
            setting: 'godot-embed.iframeHost',
            label: app.translator.trans('clarkwinkelmann-godot-embed.admin.settings.iframeHost'),
            help: app.translator.trans('clarkwinkelmann-godot-embed.admin.settings.iframeHostHelp'),
        });
});
