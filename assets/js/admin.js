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
