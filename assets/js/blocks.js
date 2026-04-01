/* global wp */
/* AW-Base Gutenberg Blocks – No JSX, uses wp.element.createElement */
(function () {
    'use strict';

    var el          = wp.element.createElement;
    var registerBlockType  = wp.blocks.registerBlockType;
    var ServerSideRender   = wp.serverSideRender;
    var useBlockProps      = wp.blockEditor.useBlockProps;
    var InspectorControls  = wp.blockEditor.InspectorControls;
    var PanelBody    = wp.components.PanelBody;
    var TextControl  = wp.components.TextControl;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;

    // ──────────────────────────────────────────────────────────────────────────
    // [btn] ボタンブロック
    // ──────────────────────────────────────────────────────────────────────────
    registerBlockType('aw-base/btn', {
        title: 'AW ボタン',
        icon: 'button',
        category: 'aw-base-blocks',
        attributes: {
            url:     { type: 'string',  default: '#' },
            text:    { type: 'string',  default: 'ボタン' },
            color:   { type: 'string',  default: 'primary' },
            size:    { type: 'string',  default: 'normal' },
            circle:  { type: 'string',  default: '0' },
            width:   { type: 'string',  default: '' },
            outline: { type: 'string',  default: '0' },
            target:  { type: 'string',  default: '' },
        },
        edit: function (props) {
            var a = props.attributes;
            var blockProps = useBlockProps({ style: { padding: '8px' } });

            var colorMap = {
                primary:   { bg: '#C30E24', text: '#fff' },
                secondary: { bg: '#11114D', text: '#fff' },
                success:   { bg: '#28a745', text: '#fff' },
                danger:    { bg: '#dc3545', text: '#fff' },
                warning:   { bg: '#ffc107', text: '#333' },
            };
            var sizeMap = {
                small:  { padding: '6px 16px',  fontSize: '0.85rem' },
                normal: { padding: '10px 28px', fontSize: '1rem'    },
                large:  { padding: '14px 36px', fontSize: '1.15rem' },
            };
            var clr    = colorMap[a.color] || colorMap.primary;
            var sz     = sizeMap[a.size]   || sizeMap.normal;
            var radius = a.circle === '1' ? '99px' : '4px';
            var btnW   = a.width ? a.width + '%' : 'auto';
            var btnStyle = {
                display:        'inline-block',
                width:          btnW,
                padding:        sz.padding,
                fontSize:       sz.fontSize,
                fontWeight:     '700',
                borderRadius:   radius,
                lineHeight:     '1.4',
                textDecoration: 'none',
                border:         '2px solid ' + clr.bg,
                background:     a.outline === '1' ? 'transparent' : clr.bg,
                color:          a.outline === '1' ? clr.bg : clr.text,
                cursor:         'default',
                userSelect:     'none',
                boxSizing:      'border-box',
            };

            return el('div', blockProps,
                el(InspectorControls, null,
                    el(PanelBody, { title: 'ボタン設定', initialOpen: true },
                        el(TextControl, {
                            label: 'ボタンテキスト',
                            value: a.text,
                            onChange: function (v) { props.setAttributes({ text: v }); }
                        }),
                        el(TextControl, {
                            label: 'リンク先 URL',
                            value: a.url,
                            onChange: function (v) { props.setAttributes({ url: v }); },
                            placeholder: 'https://example.com/'
                        }),
                        el(SelectControl, {
                            label: 'カラー',
                            value: a.color,
                            options: [
                                { label: 'Primary（アクセント色）', value: 'primary' },
                                { label: 'Secondary（メイン色）',   value: 'secondary' },
                                { label: 'Success（緑）',          value: 'success' },
                                { label: 'Danger（赤）',           value: 'danger' },
                                { label: 'Warning（黄）',          value: 'warning' },
                            ],
                            onChange: function (v) { props.setAttributes({ color: v }); }
                        }),
                        el(SelectControl, {
                            label: 'サイズ',
                            value: a.size,
                            options: [
                                { label: 'S（小）', value: 'small'  },
                                { label: 'M（標準）', value: 'normal' },
                                { label: 'L（大）',  value: 'large'  },
                            ],
                            onChange: function (v) { props.setAttributes({ size: v }); }
                        }),
                        el(ToggleControl, {
                            label: '円形にする',
                            checked: a.circle === '1',
                            onChange: function (v) { props.setAttributes({ circle: v ? '1' : '0' }); }
                        }),
                        el(SelectControl, {
                            label: '幅',
                            value: a.width,
                            options: [
                                { label: 'auto（テキスト幅）', value: '' },
                                { label: '25%', value: '25' },
                                { label: '50%', value: '50' },
                                { label: '75%', value: '75' },
                                { label: '100%', value: '100' },
                            ],
                            onChange: function (v) { props.setAttributes({ width: v }); }
                        }),
                        el(ToggleControl, {
                            label: 'アウトラインボタン',
                            checked: a.outline === '1',
                            onChange: function (v) { props.setAttributes({ outline: v ? '1' : '0' }); }
                        }),
                        el(ToggleControl, {
                            label: '別タブで開く',
                            checked: a.target === '_blank',
                            onChange: function (v) { props.setAttributes({ target: v ? '_blank' : '' }); }
                        })
                    )
                ),
                el('div', { style: { textAlign: 'center', padding: '8px 0' } },
                    el('span', { style: btnStyle }, a.text || 'ボタン')
                )
            );
        },
        save: function () { return null; }
    });

    // ──────────────────────────────────────────────────────────────────────────
    // [blogcard] ブログカードブロック
    // ──────────────────────────────────────────────────────────────────────────
    registerBlockType('aw-base/blogcard', {
        title: 'AW ブログカード',
        icon: 'admin-links',
        category: 'aw-base-blocks',
        attributes: {
            url:    { type: 'string', default: '' },
            target: { type: 'string', default: '' },
        },
        edit: function (props) {
            var a = props.attributes;
            var blockProps = useBlockProps({ style: { padding: '8px' } });

            // エディタ用コンパクトプレビュー（クリック無効・ページ遷移しない）
            var previewStyle = {
                display: 'flex',
                alignItems: 'center',
                gap: '10px',
                padding: '8px 12px',
                background: '#fff',
                border: '1px solid #ddd',
                borderRadius: '4px',
                boxShadow: '0 1px 3px rgba(0,0,0,0.08)',
                pointerEvents: 'none',
                userSelect: 'none',
            };
            var thumbStyle = {
                width: '60px',
                height: '40px',
                background: '#e9ecef',
                borderRadius: '3px',
                flexShrink: 0,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                fontSize: '18px',
            };
            var labelStyle = {
                flex: 1,
                minWidth: 0,
                overflow: 'hidden',
                textOverflow: 'ellipsis',
                whiteSpace: 'nowrap',
                fontSize: '13px',
                color: '#1e1e1e',
                fontWeight: 600,
            };
            var urlStyle = {
                fontSize: '11px',
                color: '#888',
                overflow: 'hidden',
                textOverflow: 'ellipsis',
                whiteSpace: 'nowrap',
            };

            var preview = a.url
                ? el('div', { style: previewStyle },
                    el('div', { style: thumbStyle }, '🔗'),
                    el('div', { style: { flex: 1, minWidth: 0 } },
                        el('div', { style: labelStyle }, a.url),
                        el('div', { style: urlStyle }, '📎 ' + (a.target === '_blank' ? '別タブで開く' : '同タブで開く'))
                    )
                  )
                : el('div', {
                    style: {
                        padding: '16px 24px',
                        background: '#f8f9fa',
                        border: '2px dashed #dee2e6',
                        borderRadius: '6px',
                        textAlign: 'center',
                        color: '#6c757d',
                        fontSize: '13px',
                    }
                }, '🔗 下の入力フィールドにURLを指定してください');

            var inlineInput = el('div', { style: { marginTop: '8px', background: '#fff', padding: '12px', border: '1px solid #ddd', borderRadius: '4px' } },
                el(TextControl, {
                    label: '🔗 ブログカードURL',
                    value: a.url,
                    onChange: function (v) { props.setAttributes({ url: v }); },
                    placeholder: 'https://example.com/post/'
                })
            );

            return el('div', blockProps,
                el(InspectorControls, null,
                    el(PanelBody, { title: 'ブログカード設定', initialOpen: true },
                        el(ToggleControl, {
                            label: '別タブで開く',
                            checked: a.target === '_blank',
                            onChange: function (v) { props.setAttributes({ target: v ? '_blank' : '' }); }
                        })
                    )
                ),
                preview,
                inlineInput
            );
        },
        save: function () { return null; }
    });

    // ──────────────────────────────────────────────────────────────────────────
    // [faq] FAQ ブロック
    // ──────────────────────────────────────────────────────────────────────────
    registerBlockType('aw-base/faq', {
        title: 'AW FAQ',
        icon: 'editor-help',
        category: 'aw-base-blocks',
        attributes: {
            items: {
                type: 'array',
                default: [{ q: '', a: '' }],
                items: { type: 'object' }
            }
        },
        edit: function (props) {
            var a = props.attributes;
            var items = a.items || [];
            var blockProps = useBlockProps({ className: 'awb-faq-editor' });

            function updateItem(index, key, val) {
                var newItems = items.slice();
                newItems[index] = Object.assign({}, newItems[index]);
                newItems[index][key] = val;
                props.setAttributes({ items: newItems });
            }

            return el('div', blockProps,
                el('div', { style: { fontWeight: 700, marginBottom: '12px', fontSize: '14px' } }, 'AW FAQ ブロック'),
                items.map(function (item, i) {
                    return el('div', {
                        key: i,
                        style: { border: '1px solid #ddd', padding: '12px 16px', marginBottom: '8px', borderRadius: '4px', background: '#fff' }
                    },
                        el('div', { style: { fontWeight: 700, marginBottom: '8px', color: '#007cba' } }, 'Q' + (i + 1)),
                        el(TextControl, {
                            label: '質問',
                            value: item.q || '',
                            onChange: function (v) { updateItem(i, 'q', v); }
                        }),
                        el('div', null,
                            el('label', { style: { display: 'block', fontWeight: 600, marginBottom: '4px', fontSize: '11px', textTransform: 'uppercase' } }, '回答'),
                            el('textarea', {
                                value: item.a || '',
                                onChange: function (e) { updateItem(i, 'a', e.target.value); },
                                rows: 3,
                                style: { width: '100%', padding: '8px', borderRadius: '4px', border: '1px solid #ccc', resize: 'vertical' }
                            })
                        ),
                        items.length > 1
                            ? el('button', {
                                onClick: function () {
                                    props.setAttributes({ items: items.filter(function (_, j) { return j !== i; }) });
                                },
                                style: { marginTop: '8px', color: '#dc3545', cursor: 'pointer', background: 'none', border: 'none', padding: 0 }
                            }, '✕ この項目を削除')
                            : null
                    );
                }),
                el('button', {
                    onClick: function () { props.setAttributes({ items: items.concat([{ q: '', a: '' }]) }); },
                    style: { padding: '8px 16px', background: '#007cba', color: '#fff', border: 'none', borderRadius: '4px', cursor: 'pointer', marginTop: '8px' }
                }, '+ Q&Aを追加')
            );
        },
        save: function () { return null; }
    });

    // ──────────────────────────────────────────────────────────────────────────
    // [timeline] タイムラインブロック
    // ──────────────────────────────────────────────────────────────────────────
    registerBlockType('aw-base/timeline', {
        title: 'AW タイムライン',
        icon: 'list-view',
        category: 'aw-base-blocks',
        attributes: {
            items: {
                type: 'array',
                default: [{ label: '', content: '' }],
                items: { type: 'object' }
            }
        },
        edit: function (props) {
            var a = props.attributes;
            var items = a.items || [];
            var blockProps = useBlockProps();

            function updateItem(index, key, val) {
                var newItems = items.slice();
                newItems[index] = Object.assign({}, newItems[index]);
                newItems[index][key] = val;
                props.setAttributes({ items: newItems });
            }

            return el('div', blockProps,
                el('div', { style: { fontWeight: 700, marginBottom: '12px', fontSize: '14px' } }, 'AW タイムライン'),
                items.map(function (item, i) {
                    return el('div', {
                        key: i,
                        style: { display: 'flex', gap: '12px', alignItems: 'flex-start', marginBottom: '8px', padding: '12px', border: '1px solid #ddd', borderRadius: '4px', background: '#fff' }
                    },
                        el('div', { style: { flex: '0 0 160px' } },
                            el(TextControl, {
                                label: 'ラベル（日付など）',
                                value: item.label || '',
                                onChange: function (v) { updateItem(i, 'label', v); }
                            })
                        ),
                        el('div', { style: { flex: 1 } },
                            el('label', { style: { display: 'block', fontWeight: 600, marginBottom: '4px', fontSize: '11px', textTransform: 'uppercase' } }, '内容'),
                            el('textarea', {
                                value: item.content || '',
                                onChange: function (e) { updateItem(i, 'content', e.target.value); },
                                rows: 2,
                                style: { width: '100%', padding: '8px', borderRadius: '4px', border: '1px solid #ccc', resize: 'vertical' }
                            })
                        ),
                        i > 0 ? el('button', {
                            onClick: function () {
                                props.setAttributes({ items: items.filter(function (_, j) { return j !== i; }) });
                            },
                            style: { marginTop: '24px', color: '#dc3545', cursor: 'pointer', background: 'none', border: 'none' }
                        }, '✕') : null
                    );
                }),
                el('button', {
                    onClick: function () { props.setAttributes({ items: items.concat([{ label: '', content: '' }]) }); },
                    style: { padding: '8px 16px', background: '#007cba', color: '#fff', border: 'none', borderRadius: '4px', cursor: 'pointer', marginTop: '8px' }
                }, '+ 項目を追加')
            );
        },
        save: function () { return null; }
    });

    // ──────────────────────────────────────────────────────────────────────────
    // [new-list] 新着記事一覧ブロック
    // ──────────────────────────────────────────────────────────────────────────
    registerBlockType('aw-base/new-list', {
        title: 'AW 新着記事一覧',
        icon: 'list-view',
        category: 'aw-base-blocks',
        attributes: {
            title:     { type: 'string',  default: '' },
            title_tag: { type: 'string',  default: 'h2' },
            cats:      { type: 'string',  default: '' },
            snippet:   { type: 'boolean', default: true },
        },
        edit: function (props) {
            var a = props.attributes;
            var blockProps = useBlockProps({ style: { padding: '8px' } });
            return el('div', blockProps,
                el(InspectorControls, null,
                    el(PanelBody, { title: '新着記事一覧 設定', initialOpen: true },
                        el(TextControl, {
                            label: 'セクションタイトル（省略可）',
                            value: a.title,
                            onChange: function (v) { props.setAttributes({ title: v }); },
                            placeholder: '例：新着記事'
                        }),
                        el(SelectControl, {
                            label: 'タイトルタグ',
                            value: a.title_tag,
                            options: [
                                { label: 'H2', value: 'h2' },
                                { label: 'H3', value: 'h3' },
                                { label: 'H4', value: 'h4' },
                            ],
                            onChange: function (v) { props.setAttributes({ title_tag: v }); }
                        }),
                        el(TextControl, {
                            label: 'カテゴリID（カンマ区切り・省略時は全カテゴリ）',
                            value: a.cats,
                            onChange: function (v) { props.setAttributes({ cats: v }); },
                            placeholder: '例：3,7,12'
                        }),
                        el(ToggleControl, {
                            label: '抜粋文を表示する',
                            checked: a.snippet,
                            onChange: function (v) { props.setAttributes({ snippet: v }); }
                        })
                    )
                ),
                el(ServerSideRender, { block: 'aw-base/new-list', attributes: a })
            );
        },
        save: function () { return null; }
    });

    // ──────────────────────────────────────────────────────────────────────────
    // [accordion] アコーディオンブロック
    // ──────────────────────────────────────────────────────────────────────────
    registerBlockType('aw-base/accordion', {
        title: 'AW アコーディオン',
        icon: 'arrow-down-alt2',
        category: 'aw-base-blocks',
        attributes: {
            items: {
                type: 'array',
                default: [{ title: '', content: '', open: false }],
                items: { type: 'object' }
            }
        },
        edit: function (props) {
            var a = props.attributes;
            var items = a.items || [];
            var blockProps = useBlockProps();

            function updateItem(index, key, val) {
                var newItems = items.slice();
                newItems[index] = Object.assign({}, newItems[index]);
                newItems[index][key] = val;
                props.setAttributes({ items: newItems });
            }

            return el('div', blockProps,
                el('div', { style: { fontWeight: 700, marginBottom: '12px', fontSize: '14px' } }, 'AW アコーディオン'),
                items.map(function (item, i) {
                    return el('div', {
                        key: i,
                        style: { border: '1px solid #ddd', padding: '12px 16px', marginBottom: '8px', borderRadius: '4px', background: '#fff' }
                    },
                        el(TextControl, {
                            label: '見出し（クリックする部分）',
                            value: item.title || '',
                            onChange: function (v) { updateItem(i, 'title', v); }
                        }),
                        el('div', null,
                            el('label', { style: { display: 'block', fontWeight: 600, marginBottom: '4px', fontSize: '11px', textTransform: 'uppercase' } }, '内容'),
                            el('textarea', {
                                value: item.content || '',
                                onChange: function (e) { updateItem(i, 'content', e.target.value); },
                                rows: 3,
                                style: { width: '100%', padding: '8px', borderRadius: '4px', border: '1px solid #ccc', resize: 'vertical' }
                            })
                        ),
                        el(ToggleControl, {
                            label: '初期状態: 開いた状態にする',
                            checked: !!item.open,
                            onChange: function (v) { updateItem(i, 'open', v); }
                        }),
                        i > 0 ? el('button', {
                            onClick: function () {
                                props.setAttributes({ items: items.filter(function (_, j) { return j !== i; }) });
                            },
                            style: { color: '#dc3545', cursor: 'pointer', background: 'none', border: 'none', padding: 0 }
                        }, '✕ 削除') : null
                    );
                }),
                el('button', {
                    onClick: function () { props.setAttributes({ items: items.concat([{ title: '', content: '', open: false }]) }); },
                    style: { padding: '8px 16px', background: '#007cba', color: '#fff', border: 'none', borderRadius: '4px', cursor: 'pointer', marginTop: '8px' }
                }, '+ アイテムを追加')
            );
        },
        save: function () { return null; }
    });

}());
