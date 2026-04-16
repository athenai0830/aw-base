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

});

// ============================================================
// 画像最適化タブ
// ============================================================
(function() {
    var optimizeBtn = document.getElementById('awb-optimize-btn');
    var deleteBtn   = document.getElementById('awb-delete-btn');
    if ( ! optimizeBtn && ! deleteBtn ) return;

    var progressWrap   = document.getElementById('awb-progress-wrap');
    var progressBar    = document.getElementById('awb-progress-bar');
    var progressStatus = document.getElementById('awb-progress-status');
    var resultMsg      = document.getElementById('awb-result-msg');
    var optimizedCount = document.getElementById('awb-optimized-count');
    var pendingCount   = document.getElementById('awb-pending-count');
    var totalCount     = document.getElementById('awb-total-count');

    function showResult( msg, isError ) {
        resultMsg.textContent = msg;
        resultMsg.className   = isError ? 'is-error' : 'is-success';
        resultMsg.style.display = 'block';
    }

    // ── 一括最適化 ──────────────────────────────────────────
    if ( optimizeBtn ) {
        optimizeBtn.addEventListener('click', async function() {
            optimizeBtn.disabled = true;
            resultMsg.style.display = 'none';
            progressWrap.style.display = 'block';
            progressBar.style.width = '0%';
            progressStatus.textContent = '処理中...';

            var offset = 0;
            var total  = parseInt( totalCount.textContent, 10 ) || 0;

            while ( true ) {
                var body = new URLSearchParams({
                    action: 'awbase_optimize_batch',
                    nonce:  awbaseAdminData.optimizeNonce,
                    offset: offset,
                });

                var res;
                try {
                    res = await fetch( awbaseAdminData.ajaxUrl, {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body:    body,
                    } );
                } catch ( e ) {
                    showResult( '通信エラーが発生しました。', true );
                    break;
                }

                var data = await res.json();
                if ( ! data.success ) {
                    showResult( 'エラーが発生しました。', true );
                    break;
                }

                var d = data.data;
                offset = d.offset;
                if ( d.total ) total = d.total;

                progressBar.style.width    = d.pct + '%';
                progressStatus.textContent = d.offset + ' / ' + d.total + ' 枚処理済み（' + d.pct + '%）';

                if ( d.done ) {
                    progressBar.style.width    = '100%';
                    progressStatus.textContent = '完了！ ' + d.total + ' 枚を処理しました';
                    if ( optimizedCount ) optimizedCount.textContent = d.total + ' 枚';
                    if ( pendingCount )   pendingCount.textContent   = '0 枚';
                    optimizeBtn.textContent  = '最適化済み';
                    if ( deleteBtn ) deleteBtn.disabled = false;
                    showResult( '最適化が完了しました。', false );
                    break;
                }
            }
        });
    }

    // ── 全削除 ──────────────────────────────────────────────
    if ( deleteBtn ) {
        deleteBtn.addEventListener('click', async function() {
            if ( ! confirm( 'aw-thumbs 内の生成ファイルをすべて削除します。よろしいですか？\n（元画像は削除されません）' ) ) return;

            deleteBtn.disabled = true;
            resultMsg.style.display = 'none';

            var body = new URLSearchParams({
                action: 'awbase_delete_thumbs',
                nonce:  awbaseAdminData.deleteNonce,
            });

            try {
                var res  = await fetch( awbaseAdminData.ajaxUrl, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body:    body,
                } );
                var data = await res.json();
                if ( data.success ) {
                    var deleted = data.data.deleted;
                    if ( optimizedCount ) optimizedCount.textContent = '0 枚';
                    if ( pendingCount && totalCount ) pendingCount.textContent = totalCount.textContent;
                    if ( optimizeBtn ) {
                        optimizeBtn.disabled    = false;
                        optimizeBtn.textContent = 'すべて最適化';
                    }
                    progressWrap.style.display = 'none';
                    showResult( deleted + ' ファイルを削除しました。', false );
                } else {
                    showResult( '削除に失敗しました。', true );
                    deleteBtn.disabled = false;
                }
            } catch ( e ) {
                showResult( '通信エラーが発生しました。', true );
                deleteBtn.disabled = false;
            }
        });
    }
}());
