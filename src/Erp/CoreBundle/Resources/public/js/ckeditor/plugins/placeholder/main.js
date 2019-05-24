CKEDITOR.plugins.add('placeholder',
    {
        requires: ['richcombo'],
        init: function (editor) {
            //  array of strings to choose from that'll be inserted into the editor
            var strings = [];
            strings.push(['{{firstName}}', 'First name', 'First name']);
            strings.push(['{{lastName}}', 'Last name', 'Last name']);
            strings.push(['{{date}}', 'Current Date', 'Current Date']);
            strings.push(['{{daysBefore}}', 'Days Before', 'Days Before']);
            strings.push(['{{daysAfter}}', 'Days After', 'Days After']);

            // add the menu to the editor
            editor.ui.addRichCombo('placeholder',
                {
                    label: 'Insert placeholders',
                    title: 'Insert placeholders',
                    voiceLabel: 'Insert placeholders',
                    className: 'cke_format',
                    multiSelect: false,
                    panel:
                        {
                            css: [editor.config.contentsCss, CKEDITOR.skin.getPath('editor')],
                            voiceLabel: editor.lang.panelVoiceLabel
                        },

                    init: function () {
                        this.startGroup('Insert placeholders');
                        for (var i in strings) {
                            this.add(strings[i][0], strings[i][1], strings[i][2]);
                        }
                    },

                    onClick: function (value) {
                        editor.focus();
                        editor.fire('saveSnapshot');
                        editor.insertHtml(value);
                        editor.fire('saveSnapshot');
                    }
                });
        }
    });