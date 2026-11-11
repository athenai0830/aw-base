/**
 * テーブルブロック：右カラム（ブロック設定）に「列の幅」パネルを追加し、列ごとの幅を％で指定できるようにする
 * 属性 awbaseColWidths（列順の数値配列。0＝自動）。保存HTMLは変えず、ブロックのコメントに属性だけ持つ
 * 表示は CSS 変数 --awb-col-N ＋ nth-child の CSS（inc/table-width.php。表側の付与も同ファイルの awbase_table_cols_render）
 */
( function ( wp ) {
	const el = wp.element.createElement;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl, Button } = wp.components;
	const { createHigherOrderComponent } = wp.compose;
	const MAX_COLS = 12;   // inc/table-width.php の CSS もこの数まで

	function isTable( name ) {
		return 'core/table' === name;
	}

	function cleanWidth( value ) {
		const n = parseInt( value, 10 );
		return n >= 1 && n <= 100 ? n : 0;
	}

	// 現在の列数（見出し行・本文・フッターで一番多い行）
	function countCols( attrs ) {
		let max = 0;
		[ 'head', 'body', 'foot' ].forEach( function ( section ) {
			( attrs[ section ] || [] ).forEach( function ( row ) {
				max = Math.max( max, ( row.cells || [] ).length );
			} );
		} );
		return Math.min( max, MAX_COLS );
	}

	// 末尾の「自動」を落とし、全部自動なら undefined
	function normalize( widths ) {
		const list = widths.map( cleanWidth );
		while ( list.length && ! list[ list.length - 1 ] ) {
			list.pop();
		}
		return list.length ? list : undefined;
	}

	function styleVars( widths ) {
		const style = {};
		( widths || [] ).slice( 0, MAX_COLS ).forEach( function ( w, i ) {
			if ( cleanWidth( w ) ) {
				style[ '--awb-col-' + ( i + 1 ) ] = cleanWidth( w ) + '%';
			}
		} );
		return style;
	}

	wp.hooks.addFilter( 'blocks.registerBlockType', 'awbase/table-cols-attribute', function ( settings, name ) {
		if ( ! isTable( name ) ) {
			return settings;
		}
		settings.attributes = Object.assign( {}, settings.attributes, {
			awbaseColWidths: { type: 'array', items: { type: 'number' } },
		} );
		return settings;
	} );

	// 右カラムのパネル
	const withColControls = createHigherOrderComponent( function ( BlockEdit ) {
		return function ( props ) {
			if ( ! isTable( props.name ) ) {
				return el( BlockEdit, props );
			}
			const cols = countCols( props.attributes );
			const widths = props.attributes.awbaseColWidths || [];
			const fields = [];
			let total = 0;
			let autoCount = 0;
			for ( let i = 0; i < cols; i++ ) {
				const w = cleanWidth( widths[ i ] );
				total += w;
				if ( ! w ) {
					autoCount++;
				}
				fields.push(
					el( TextControl, {
						key: i,
						label: ( i + 1 ) + '列目（％）',
						type: 'number',
						min: 1,
						max: 100,
						placeholder: '自動',
						value: w ? String( w ) : '',
						onChange: function ( value ) {
							const next = widths.slice();
							while ( next.length < cols ) {
								next.push( 0 );
							}
							next[ i ] = cleanWidth( value );
							props.setAttributes( { awbaseColWidths: normalize( next ) } );
						},
						__nextHasNoMarginBottom: true,
						__next40pxDefaultSize: true,
					} )
				);
			}

			let note = '空欄の列は残りの幅を自動で分け合います。';
			let noteColor = '#757575';
			if ( total > 100 ) {
				note = '合計が' + total + '％で100％を超えています。表が本文幅からはみ出します。';
				noteColor = '#cc1818';
			} else if ( total && ! autoCount && total < 100 ) {
				note = '合計' + total + '％。全列を指定した場合、100％に満たない分は各列に割り振られます。';
			} else if ( total ) {
				note = '指定の合計 ' + total + '％。' + note;
			}

			const body = cols
				? [
					el( 'div', { key: 'fields', style: { display: 'grid', gap: '12px' } }, fields ),
					el( 'p', { key: 'note', style: { fontSize: '12px', color: noteColor, margin: '12px 0 0' } }, note ),
					el( 'p', { key: 'note2', style: { fontSize: '12px', color: '#757575', margin: '6px 0 0' } }, '列を追加・削除したときは、幅を見直してください（指定は列の位置に付きます）。' ),
					widths.length
						? el( Button, {
							key: 'reset',
							variant: 'secondary',
							size: 'small',
							style: { marginTop: '12px' },
							onClick: function () {
								props.setAttributes( { awbaseColWidths: undefined } );
							},
						}, 'すべて自動に戻す' )
						: null,
				]
				: el( 'p', { style: { fontSize: '12px', color: '#757575', margin: 0 } }, '表を作成すると、列ごとの幅を指定できます。' );

			return el(
				wp.element.Fragment,
				null,
				el( BlockEdit, props ),
				el( InspectorControls, null, el( PanelBody, { title: '列の幅', initialOpen: true }, body ) )
			);
		};
	}, 'withColControls' );

	wp.hooks.addFilter( 'editor.BlockEdit', 'awbase/table-cols-control', withColControls );

	// エディタ上の見た目にも反映（表側と同じクラス・CSS変数を付ける）
	const withColWrapper = createHigherOrderComponent( function ( BlockListBlock ) {
		return function ( props ) {
			if ( ! isTable( props.name ) ) {
				return el( BlockListBlock, props );
			}
			const vars = styleVars( props.attributes.awbaseColWidths );
			if ( ! Object.keys( vars ).length ) {
				return el( BlockListBlock, props );
			}
			const wrapperProps = Object.assign( {}, props.wrapperProps );
			wrapperProps.style = Object.assign( {}, wrapperProps.style, vars );
			return el(
				BlockListBlock,
				Object.assign( {}, props, {
					wrapperProps: wrapperProps,
					className: ( props.className ? props.className + ' ' : '' ) + 'awb-col-w',
				} )
			);
		};
	}, 'withColWrapper' );

	wp.hooks.addFilter( 'editor.BlockListBlock', 'awbase/table-cols-wrapper', withColWrapper );
} )( window.wp );
