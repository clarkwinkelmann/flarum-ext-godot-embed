import app from 'flarum/admin/app';
import AdminPage from 'flarum/admin/components/AdminPage';
import Button from 'flarum/common/components/Button';
import Switch from 'flarum/common/components/Switch';
import Tooltip from 'flarum/common/components/Tooltip';
import icon from 'flarum/common/helpers/icon';

const settingName = 'godot-embed.versions';
const translationPrefix = 'clarkwinkelmann-godot-embed.admin.settings.';

app.initializers.add('godot-embed', () => {
    app.extensionData
        .for('clarkwinkelmann-godot-embed')
        .registerSetting(function (this: AdminPage) {
            let versions: {
                key?: string
                basePath?: string
                wasmFileSize?: number
                default?: boolean
            }[] = [];

            try {
                versions = JSON.parse(this.setting(settingName)());
            } catch (e) {
                // do nothing, we'll reset to something usable
            }

            if (!Array.isArray(versions)) {
                versions = [];
            }

            return [
                m('.Form-group', [
                    m('label', app.translator.trans(translationPrefix + 'versions')),
                    m('table', [
                        m('thead', m('tr', [
                            m('th', app.translator.trans(translationPrefix + 'versionHeader.key')),
                            m('th', [
                                app.translator.trans(translationPrefix + 'versionHeader.basePath'),
                                ' ',
                                Tooltip.component({
                                    text: app.translator.trans(translationPrefix + 'versionHeader.basePathHelp'),
                                }, icon('fas fa-question-circle')),
                            ]),
                            m('th', [
                                app.translator.trans(translationPrefix + 'versionHeader.wasmFileSize'),
                                ' ',
                                Tooltip.component({
                                    text: app.translator.trans(translationPrefix + 'versionHeader.wasmFileSizeHelp'),
                                }, icon('fas fa-question-circle')),
                            ]),
                            m('th', [
                                app.translator.trans(translationPrefix + 'versionHeader.default'),
                                ' ',
                                Tooltip.component({
                                    text: app.translator.trans(translationPrefix + 'versionHeader.defaultHelp'),
                                }, icon('fas fa-question-circle')),
                            ]),
                            m('th'),
                        ])),
                        m('tbody', [
                            versions.map((version, index) => m('tr', [
                                m('td', m('input.FormControl', {
                                    type: 'text',
                                    value: version.key || '',
                                    onchange: (event: Event) => {
                                        version.key = (event.target as HTMLInputElement).value;
                                        this.setting(settingName)(JSON.stringify(versions));
                                    },
                                })),
                                m('td', m('input.FormControl', {
                                    type: 'text',
                                    value: version.basePath || '',
                                    onchange: (event: Event) => {
                                        version.basePath = (event.target as HTMLInputElement).value;
                                        this.setting(settingName)(JSON.stringify(versions));
                                    },
                                })),
                                m('td', m('input.FormControl', {
                                    type: 'number',
                                    min: 0,
                                    value: version.wasmFileSize || '',
                                    onchange: (event: Event) => {
                                        const value = parseInt((event.target as HTMLInputElement).value);
                                        if (value > 0) {
                                            version.wasmFileSize = value;
                                        } else {
                                            delete version.wasmFileSize;
                                        }
                                        this.setting(settingName)(JSON.stringify(versions));
                                    },
                                })),
                                m('td', Switch.component({
                                    state: !!version.default,
                                    onchange: (value: boolean) => {
                                        if (value) {
                                            version.default = true;
                                        } else {
                                            delete version.default;
                                        }

                                        // Disable previous value so only one is checked at a time
                                        versions.forEach(otherVersion => {
                                            if (otherVersion !== version && otherVersion.default) {
                                                delete otherVersion.default;
                                            }
                                        });

                                        this.setting(settingName)(JSON.stringify(versions));
                                    },
                                })),
                                m('td', Button.component({
                                    className: 'Button Button--icon',
                                    icon: 'fas fa-times',
                                    onclick: () => {
                                        versions.splice(index, 1);

                                        this.setting(settingName)(JSON.stringify(versions));
                                    },
                                })),
                            ])),
                            m('tr', m('td', {
                                colspan: 4,
                            }, Button.component({
                                className: 'Button Button--block',
                                onclick: () => {
                                    versions.push({
                                        key: '',
                                    });

                                    this.setting(settingName)(JSON.stringify(versions));
                                },
                            }, app.translator.trans(translationPrefix + 'versionAdd')))),
                        ]),
                    ]),
                ]),
            ];
        })
        .registerSetting({
            type: 'text',
            setting: 'godot-embed.iframeHost',
            label: app.translator.trans(translationPrefix + 'iframeHost'),
            help: app.translator.trans(translationPrefix + 'iframeHostHelp'),
        })
        .registerSetting({
            type: 'color',
            setting: 'godot-embed.backgroundColor',
            placeholder: '#000',
            label: app.translator.trans(translationPrefix + 'backgroundColor'),
            help: app.translator.trans(translationPrefix + 'backgroundColorHelp'),
        })
        .registerSetting({
            type: 'text',
            setting: 'godot-embed.coverFallback',
            placeholder: 'https://example.com/image.jpg',
            label: app.translator.trans(translationPrefix + 'coverFallback'),
            help: app.translator.trans(translationPrefix + 'coverFallbackHelp'),
        })
        .registerSetting({
            type: 'switch',
            setting: 'godot-embed.toolbarHover',
            label: app.translator.trans(translationPrefix + 'toolbarHover'),
            help: app.translator.trans(translationPrefix + 'toolbarHoverHelp'),
        });
});
