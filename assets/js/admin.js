// AW-Base Admin JS
jQuery(document).ready(function($) {

    // Media uploader for all image fields
    function awbaseOpenMediaUploader(inputId, previewId) {
        var mediaUploader = wp.media({
            title: '画像を選択',
            button: { text: '選択' },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#' + inputId).val(attachment.url);
            if ($('#' + previewId).length) {
                $('#' + previewId).attr('src', attachment.url).addClass('has-image');
            }
        });

        mediaUploader.open();
    }

    // Bind upload buttons
    $(document).on('click', '.awbase-upload-btn', function(e) {
        e.preventDefault();
        var inputId = $(this).data('input');
        var previewId = $(this).data('preview');
        awbaseOpenMediaUploader(inputId, previewId);
    });

    // Bind remove buttons
    $(document).on('click', '.awbase-remove-btn', function(e) {
        e.preventDefault();
        var inputId = $(this).data('input');
        var previewId = $(this).data('preview');
        $('#' + inputId).val('');
        if ($('#' + previewId).length) {
            $('#' + previewId).attr('src', '').removeClass('has-image');
        }
    });

    // Init previews
    $('.awbase-image-input').each(function() {
        var val = $(this).val();
        var previewId = $(this).data('preview');
        if (val && $('#' + previewId).length) {
            $('#' + previewId).attr('src', val).addClass('has-image');
        }
    });

    // URL text input → live preview sync
    $(document).on('input', '.awbase-image-input[type="text"]', function() {
        var val = $(this).val().trim();
        var previewId = $(this).data('preview');
        if (previewId && $('#' + previewId).length) {
            if (val) {
                $('#' + previewId).attr('src', val).addClass('has-image');
            } else {
                $('#' + previewId).attr('src', '').removeClass('has-image');
            }
        }
    });

    // Slider values display
    $('input[type="range"]').on('input', function() {
        var valId = $(this).data('val');
        if (valId) {
            $('#' + valId).text($(this).val());
        }
    });

    // コンテンツ幅バリデーション（一般設定タブ）
    $('form').on('submit', function(e) {
        var $siteMax  = $('input[name="awbase_settings[site_max_width]"]');
        var $contentW = $('input[name="awbase_settings[content_width]"]');
        var $sidebarW = $('input[name="awbase_settings[sidebar_width]"]');
        var $gap      = $('input[name="awbase_settings[gap_main_sidebar]"]');

        // フィールドが存在する（一般設定タブ）場合のみチェック
        if ( ! $siteMax.length ) return;

        var siteMax  = parseInt( $siteMax.val(), 10 )  || 0;
        var contentW = parseInt( $contentW.val(), 10 ) || 0;
        var sidebarW = parseInt( $sidebarW.val(), 10 ) || 0;
        var gap      = parseInt( $gap.val(), 10 )      || 0;
        var total    = contentW + sidebarW + gap;

        // 既存のエラー表示を除去
        $('.awbase-width-error').remove();
        $siteMax.add($contentW).add($sidebarW).add($gap).css('border-color', '');

        if ( siteMax > 0 && total > siteMax ) {
            e.preventDefault();
            var msg = 'エラー: メインコンテンツ幅(' + contentW + 'px) ＋ サイドバー幅(' + sidebarW + 'px) ＋ 間隔(' + gap + 'px) ＝ ' + total + 'px がサイト全体の最大幅(' + siteMax + 'px)を超えています。';
            $siteMax.add($contentW).add($sidebarW).add($gap).css('border-color', '#d63638');
            $gap.closest('p').after('<p class="awbase-width-error" style="color:#d63638;font-weight:bold;margin-top:4px;">' + msg + '</p>');
            $siteMax[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Tab navigation (for any future use)
    // Currently handled by PHP URL params, but adding JS smooth experience

    // ============================================================
    // 画像最適化タブ
    // ============================================================
    var $optimizeBtn = $('#awb-optimize-btn');
    var $deleteBtn   = $('#awb-delete-btn');

    if ( $optimizeBtn.length || $deleteBtn.length ) {
        var $progressWrap   = $('#awb-progress-wrap');
        var $progressBar    = $('#awb-progress-bar');
        var $progressStatus = $('#awb-progress-status');
        var $resultMsg      = $('#awb-result-msg');
        var $optimizedCount = $('#awb-optimized-count');
        var $pendingCount   = $('#awb-pending-count');
        var $totalCount     = $('#awb-total-count');

        function awbShowResult( msg, isError ) {
            $resultMsg
                .text( msg )
                .attr( 'class', isError ? 'is-error' : 'is-success' )
                .show();
        }

        var awbSkip         = 0;
        var awbRetries      = 0;
        var awbBatchRetries = 0;

        function awbRunBatch() {
            $.ajax({
                url:      awbaseAdminData.ajaxUrl,
                type:     'POST',
                dataType: 'json',
                timeout:  30000,
                data: {
                    action: 'awbase_optimize_batch',
                    nonce:  awbaseAdminData.optimizeNonce,
                    skip:   awbSkip,
                },
                success: function( res ) {
                    awbRetries = 0;
                    if ( ! res || ! res.success ) {
                        awbShowResult( 'エラーが発生しました。', true );
                        $optimizeBtn.prop( 'disabled', false );
                        return;
                    }
                    var d = res.data;
                    if ( d.processed === 0 && ! d.done ) {
                        awbBatchRetries++;
                        if ( awbBatchRetries <= 5 ) {
                            $progressStatus.text( 'リトライ中（' + awbBatchRetries + '/5）...' );
                            awbRunBatch();
                            return;
                        }
                        awbBatchRetries = 0;
                        awbSkip += 5;
                    } else {
                        awbBatchRetries = 0;
                        awbSkip = 0;
                    }
                    $progressBar.css( 'width', d.pct + '%' );
                    $progressStatus.text( ( d.total - d.remaining ) + ' 枚処理済み（' + d.pct + '%）' );

                    if ( d.done ) {
                        $progressBar.css( 'width', '100%' );
                        $optimizeBtn.prop( 'disabled', false ).text( 'すべて最適化' );
                        $deleteBtn.prop( 'disabled', false );

                        if ( d.unprocessed && d.unprocessed.length > 0 ) {
                            $progressStatus.text( '完了（未処理: ' + d.unprocessed.length + ' 枚）' );
                            var listHtml = '<p style="margin:10px 0 4px;font-weight:600;">処理できなかった画像（' + d.unprocessed.length + ' 枚）:</p><ul style="margin:0;max-height:200px;overflow-y:auto;background:#f9f9f9;border:1px solid #ddd;padding:8px 8px 8px 24px;">';
                            $.each( d.unprocessed, function( i, item ) {
                                listHtml += '<li style="font-size:12px;">' + item.name + '</li>';
                            });
                            listHtml += '</ul>';
                            $resultMsg.html( listHtml ).attr( 'class', '' ).show();
                        } else {
                            $progressStatus.text( '完了！ すべて最適化しました' );
                            $optimizedCount.text( $totalCount.text() );
                            $pendingCount.text( '0 枚' );
                            $optimizeBtn.text( '最適化済み' ).prop( 'disabled', true );
                            awbShowResult( '最適化が完了しました。', false );
                        }
                    } else {
                        awbRunBatch();
                    }
                },
                error: function() {
                    awbRetries++;
                    if ( awbRetries <= 5 ) {
                        // 問題のある画像を1枚飛ばしてリトライ
                        awbSkip++;
                        $progressStatus.text( '問題のある画像をスキップして続行中...' );
                        awbRunBatch();
                    } else {
                        awbShowResult( '処理を続行できません。サーバーのメモリ制限を確認してください。', true );
                        $optimizeBtn.prop( 'disabled', false );
                    }
                },
            });
        }

        // ── 一括最適化 ────────────────────────────────────────
        $optimizeBtn.on( 'click', function() {
            awbSkip         = 0;
            awbRetries      = 0;
            awbBatchRetries = 0;
            $optimizeBtn.prop( 'disabled', true );
            $resultMsg.hide();
            $progressWrap.show();
            $progressBar.css( 'width', '0%' );
            $progressStatus.text( '処理中...' );
            awbRunBatch();
        });

        // ── 全削除 ────────────────────────────────────────────
        $deleteBtn.on( 'click', function() {
            if ( ! confirm( 'aw-thumbs 内の生成ファイルをすべて削除します。よろしいですか？\n（元画像は削除されません）' ) ) return;
            $deleteBtn.prop( 'disabled', true );
            $resultMsg.hide();

            $.ajax({
                url:  awbaseAdminData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'awbase_delete_thumbs',
                    nonce:  awbaseAdminData.deleteNonce,
                },
                success: function( res ) {
                    if ( res.success ) {
                        $optimizedCount.text( '0 枚' );
                        $pendingCount.text( $totalCount.text() );
                        $optimizeBtn.prop( 'disabled', false ).text( 'すべて最適化' );
                        $progressWrap.hide();
                        awbShowResult( res.data.deleted + ' ファイルを削除しました。', false );
                    } else {
                        awbShowResult( '削除に失敗しました。', true );
                        $deleteBtn.prop( 'disabled', false );
                    }
                },
                error: function() {
                    awbShowResult( '通信エラーが発生しました。', true );
                    $deleteBtn.prop( 'disabled', false );
                },
            });
        });
    }

});
