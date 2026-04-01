<?php
// AW-Base Post Meta Fields
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Add Meta Box
function awbase_add_post_meta_boxes() {
    $screens = [ 'post', 'page' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'awbase_post_options',
            'AW-Base 投稿／個別SEO・表示設定',
            'awbase_post_options_html',
            $screen,
            'normal',
            'high'
        );
    }
    // Review schema meta box (posts only)
    add_meta_box(
        'awbase_review_schema',
        'AW-Base レビュー構造化データ',
        'awbase_review_schema_html',
        'post',
        'normal',
        'default'
    );
    // Citation schema meta box (posts + pages)
    foreach ( $screens as $screen ) {
        add_meta_box(
            'awbase_citation_schema',
            'AW-Base 引用・参考文献 (Citation)',
            'awbase_citation_schema_html',
            $screen,
            'normal',
            'default'
        );
    }
}
add_action( 'add_meta_boxes', 'awbase_add_post_meta_boxes' );

// 2. Meta Box HTML
function awbase_post_options_html( $post ) {
    wp_nonce_field( 'awbase_save_post_meta', 'awbase_post_meta_nonce' );

    $is_post = ( $post->post_type === 'post' );

    $seo_title          = get_post_meta( $post->ID, 'awbase_seo_title', true );
    $seo_desc           = get_post_meta( $post->ID, 'awbase_seo_desc', true );
    $noindex            = get_post_meta( $post->ID, 'awbase_noindex', true );
    $hide_eyecatch      = get_post_meta( $post->ID, 'awbase_hide_eyecatch', true );
    $hide_toc           = get_post_meta( $post->ID, 'awbase_hide_toc', true );
    $hide_title         = get_post_meta( $post->ID, 'awbase_hide_title', true );
    $hide_reading_time  = get_post_meta( $post->ID, 'awbase_hide_reading_time', true );
    $layout             = get_post_meta( $post->ID, 'awbase_layout', true );
    ?>
    <table class="form-table">
        <tr>
            <th>テンプレート（レイアウト）</th>
            <td>
                <select name="awbase_layout">
                    <option value="" <?php selected('', $layout); ?>>デフォルト（テーマ設定に従う）</option>
                    <option value="2c" <?php selected('2c', $layout); ?>>2カラム（サイドバーあり）</option>
                    <option value="1c" <?php selected('1c', $layout); ?>>1カラム（サイドバーなし）</option>
                    <option value="wide" <?php selected('wide', $layout); ?>>ワイドレイアウト（コンテンツ全幅）</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>表示設定</th>
            <td>
                <label style="display:block;margin-bottom:6px;">
                    <input type="checkbox" name="awbase_hide_title" value="1" <?php checked('1', $hide_title); ?>>
                    タイトルを表示しない
                </label>
                <?php if ( $is_post ) : ?>
                <label style="display:block;margin-bottom:6px;">
                    <input type="checkbox" name="awbase_hide_reading_time" value="1" <?php checked('1', $hide_reading_time); ?>>
                    読了時間を表示しない
                </label>
                <?php endif; ?>
                <label style="display:block;margin-bottom:6px;">
                    <input type="checkbox" name="awbase_hide_toc" value="1" <?php checked('1', $hide_toc); ?>>
                    目次を表示しない
                </label>
                <label style="display:block;margin-bottom:6px;">
                    <input type="checkbox" name="awbase_hide_eyecatch" value="1" <?php checked('1', $hide_eyecatch); ?>>
                    アイキャッチ画像を表示しない
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="awbase_seo_title">SEOタイトル</label></th>
            <td><input type="text" id="awbase_seo_title" name="awbase_seo_title" value="<?php echo esc_attr($seo_title); ?>" class="large-text"></td>
        </tr>
        <tr>
            <th><label for="awbase_seo_desc">メタディスクリプション</label></th>
            <td><textarea id="awbase_seo_desc" name="awbase_seo_desc" rows="3" class="large-text"><?php echo esc_textarea($seo_desc); ?></textarea></td>
        </tr>
        <tr>
            <th>SEOインデックス設定</th>
            <td>
                <label><input type="checkbox" name="awbase_noindex" value="1" <?php checked('1', $noindex); ?>> 検索エンジンにインデックスさせない (noindex)</label>
            </td>
        </tr>
    </table>
    <?php
}

// 2b. Review Schema Meta Box HTML
function awbase_review_schema_html( $post ) {
    wp_nonce_field( 'awbase_save_review_schema', 'awbase_review_schema_nonce' );

    $enable      = get_post_meta( $post->ID, 'awbase_review_enable', true );
    $item_name   = get_post_meta( $post->ID, 'awbase_review_item_name', true );
    $item_type   = get_post_meta( $post->ID, 'awbase_review_item_type', true );
    $rating      = get_post_meta( $post->ID, 'awbase_review_rating', true );
    $best_rating = get_post_meta( $post->ID, 'awbase_review_best_rating', true ) ?: '5';
    $description = get_post_meta( $post->ID, 'awbase_review_description', true );
    ?>
    <table class="form-table">
        <tr>
            <th>レビュー構造化データ</th>
            <td>
                <label><input type="checkbox" name="awbase_review_enable" value="1" <?php checked('1', $enable); ?>> このページにレビュー構造化データを出力する</label>
            </td>
        </tr>
        <tr>
            <th><label for="awbase_review_item_name">商品・対象名</label></th>
            <td><input type="text" id="awbase_review_item_name" name="awbase_review_item_name" value="<?php echo esc_attr($item_name); ?>" class="large-text" placeholder="例：商品名、映画タイトル、書籍名など"></td>
        </tr>
        <tr>
            <th><label for="awbase_review_item_type">種別</label></th>
            <td>
                <select id="awbase_review_item_type" name="awbase_review_item_type">
                    <option value="Product" <?php selected('Product', $item_type); ?>>商品 (Product)</option>
                    <option value="Book" <?php selected('Book', $item_type); ?>>書籍 (Book)</option>
                    <option value="Movie" <?php selected('Movie', $item_type); ?>>映画 (Movie)</option>
                    <option value="SoftwareApplication" <?php selected('SoftwareApplication', $item_type); ?>>アプリ・ソフト</option>
                    <option value="LocalBusiness" <?php selected('LocalBusiness', $item_type); ?>>店舗・ビジネス</option>
                    <option value="Thing" <?php selected('Thing', $item_type); ?>>その他</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="awbase_review_rating">評価</label></th>
            <td>
                <input type="number" id="awbase_review_rating" name="awbase_review_rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5" step="0.5" class="small-text"> /
                <input type="number" name="awbase_review_best_rating" value="<?php echo esc_attr($best_rating); ?>" min="1" max="10" class="small-text"> （最高評価）
            </td>
        </tr>
        <tr>
            <th><label for="awbase_review_description">レビュー概要</label></th>
            <td><textarea id="awbase_review_description" name="awbase_review_description" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea></td>
        </tr>
    </table>
    <?php
}

// 3. Save Meta Box data
function awbase_save_post_meta( $post_id ) {
    // Check nonces and permissions
    if ( ! isset( $_POST['awbase_post_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['awbase_post_meta_nonce'], 'awbase_save_post_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Text fields
    $text_fields = ['awbase_seo_title', 'awbase_seo_desc', 'awbase_layout'];
    foreach ( $text_fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
    }

    // Checkbox fields
    $checkbox_fields = ['awbase_noindex', 'awbase_hide_eyecatch', 'awbase_hide_toc', 'awbase_hide_title', 'awbase_hide_reading_time'];
    foreach ( $checkbox_fields as $field ) {
        update_post_meta( $post_id, $field, isset( $_POST[$field] ) ? '1' : '0' );
    }

    // Save review schema
    if ( isset( $_POST['awbase_review_schema_nonce'] ) && wp_verify_nonce( $_POST['awbase_review_schema_nonce'], 'awbase_save_review_schema' ) ) {
        update_post_meta( $post_id, 'awbase_review_enable', isset($_POST['awbase_review_enable']) ? '1' : '0' );
        update_post_meta( $post_id, 'awbase_review_item_name', sanitize_text_field( $_POST['awbase_review_item_name'] ?? '' ) );
        update_post_meta( $post_id, 'awbase_review_item_type', sanitize_text_field( $_POST['awbase_review_item_type'] ?? 'Product' ) );
        update_post_meta( $post_id, 'awbase_review_rating', sanitize_text_field( $_POST['awbase_review_rating'] ?? '' ) );
        update_post_meta( $post_id, 'awbase_review_best_rating', sanitize_text_field( $_POST['awbase_review_best_rating'] ?? '5' ) );
        update_post_meta( $post_id, 'awbase_review_description', sanitize_textarea_field( $_POST['awbase_review_description'] ?? '' ) );
    }
}
add_action( 'save_post', 'awbase_save_post_meta' );

// 4. Output Review Schema in <head>
function awbase_output_review_schema() {
    if ( ! is_singular('post') ) return;
    $post_id = get_the_ID();
    if ( get_post_meta( $post_id, 'awbase_review_enable', true ) !== '1' ) return;

    $item_name   = get_post_meta( $post_id, 'awbase_review_item_name', true );
    $item_type   = get_post_meta( $post_id, 'awbase_review_item_type', true ) ?: 'Product';
    $rating      = (float) get_post_meta( $post_id, 'awbase_review_rating', true );
    $best_rating = (float) ( get_post_meta( $post_id, 'awbase_review_best_rating', true ) ?: '5' );
    $description = get_post_meta( $post_id, 'awbase_review_description', true );

    if ( ! $item_name || ! $rating ) return;

    // 著者名: WPユーザー → 組織名 → サイト名
    $options     = get_option( 'awbase_settings', awbase_get_default_settings() );
    $author_name = ! empty( $options['schema_author_name'] )
        ? $options['schema_author_name']
        : get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) );
    if ( empty( $author_name ) ) {
        $author_name = ! empty( $options['schema_org_name'] ) ? $options['schema_org_name'] : get_bloginfo( 'name' );
    }

    $review_rating = array(
        '@type'       => 'Rating',
        'ratingValue' => $rating,
        'bestRating'  => $best_rating,
        'worstRating' => 1,
    );

    // itemReviewed に review を含めることで Google の必須要件を満たす
    $item_reviewed = array(
        '@type'  => $item_type,
        'name'   => $item_name,
        'review' => array(
            '@type'        => 'Review',
            'author'       => array( '@type' => 'Person', 'name' => $author_name ),
            'reviewRating' => $review_rating,
        ),
    );

    $schema = array(
        '@context'      => 'https://schema.org',
        '@type'         => 'Review',
        'name'          => get_the_title(),
        'description'   => $description ?: get_the_excerpt(),
        'datePublished' => get_the_date( 'Y-m-d' ),
        'author'        => array( '@type' => 'Person', 'name' => $author_name ),
        'itemReviewed'  => $item_reviewed,
        'reviewRating'  => $review_rating,
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'awbase_output_review_schema' );

// Citation Schema Meta Box HTML
function awbase_citation_schema_html( $post ) {
    wp_nonce_field( 'awbase_save_citation_schema', 'awbase_citation_schema_nonce' );

    $enable      = get_post_meta( $post->ID, 'awbase_citation_enable', true );
    $article_type = get_post_meta( $post->ID, 'awbase_citation_article_type', true ) ?: 'ScholarlyArticle';
    $abstract    = get_post_meta( $post->ID, 'awbase_citation_abstract', true );
    $keywords    = get_post_meta( $post->ID, 'awbase_citation_keywords', true );
    $publisher   = get_post_meta( $post->ID, 'awbase_citation_publisher', true );
    $doi         = get_post_meta( $post->ID, 'awbase_citation_doi', true );
    $citations   = get_post_meta( $post->ID, 'awbase_citation_list', true );
    ?>
    <table class="form-table">
        <tr>
            <th>論文・引用スキーマ</th>
            <td>
                <label><input type="checkbox" name="awbase_citation_enable" value="1" <?php checked('1', $enable); ?>> Citation構造化データを出力する</label>
            </td>
        </tr>
        <tr>
            <th><label>記事タイプ</label></th>
            <td>
                <select name="awbase_citation_article_type">
                    <option value="ScholarlyArticle" <?php selected('ScholarlyArticle', $article_type); ?>>学術論文 (ScholarlyArticle)</option>
                    <option value="Article" <?php selected('Article', $article_type); ?>>一般記事 (Article)</option>
                    <option value="TechArticle" <?php selected('TechArticle', $article_type); ?>>技術記事 (TechArticle)</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>Abstract / 要旨</label></th>
            <td><textarea name="awbase_citation_abstract" rows="4" class="large-text"><?php echo esc_textarea($abstract); ?></textarea></td>
        </tr>
        <tr>
            <th><label>キーワード</label></th>
            <td>
                <input type="text" name="awbase_citation_keywords" value="<?php echo esc_attr($keywords); ?>" class="large-text">
                <span class="description">カンマ区切りで入力（例: デジタル通信, メディア論, AI）</span>
            </td>
        </tr>
        <tr>
            <th><label>発行者 / 組織</label></th>
            <td><input type="text" name="awbase_citation_publisher" value="<?php echo esc_attr($publisher); ?>" class="regular-text" placeholder="例: 株式会社サンプル"></td>
        </tr>
        <tr>
            <th><label>DOI / 識別子</label></th>
            <td><input type="url" name="awbase_citation_doi" value="<?php echo esc_url($doi); ?>" class="regular-text" placeholder="https://doi.org/..."></td>
        </tr>
        <tr>
            <th><label>参考文献リスト</label></th>
            <td>
                <textarea name="awbase_citation_list" rows="6" class="large-text" placeholder="1行に1件。URLを含む場合はそのまま入力。"><?php echo esc_textarea($citations); ?></textarea>
                <span class="description">1行1文献。例: 著者名. 論文タイトル. 雑誌名, 年. https://...</span>
            </td>
        </tr>
    </table>
    <?php
}

// Save Citation Schema
add_action( 'save_post', 'awbase_save_citation_schema' );
function awbase_save_citation_schema( $post_id ) {
    if ( ! isset( $_POST['awbase_citation_schema_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['awbase_citation_schema_nonce'], 'awbase_save_citation_schema' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta( $post_id, 'awbase_citation_enable', isset($_POST['awbase_citation_enable']) ? '1' : '0' );
    update_post_meta( $post_id, 'awbase_citation_article_type', sanitize_text_field( $_POST['awbase_citation_article_type'] ?? 'ScholarlyArticle' ) );
    update_post_meta( $post_id, 'awbase_citation_abstract', sanitize_textarea_field( $_POST['awbase_citation_abstract'] ?? '' ) );
    update_post_meta( $post_id, 'awbase_citation_keywords', sanitize_text_field( $_POST['awbase_citation_keywords'] ?? '' ) );
    update_post_meta( $post_id, 'awbase_citation_publisher', sanitize_text_field( $_POST['awbase_citation_publisher'] ?? '' ) );
    update_post_meta( $post_id, 'awbase_citation_doi', esc_url_raw( $_POST['awbase_citation_doi'] ?? '' ) );
    update_post_meta( $post_id, 'awbase_citation_list', sanitize_textarea_field( $_POST['awbase_citation_list'] ?? '' ) );
}

// Output Citation JSON-LD
add_action( 'wp_head', 'awbase_output_citation_schema' );
function awbase_output_citation_schema() {
    if ( ! is_singular() ) return;
    $post_id = get_the_ID();
    if ( get_post_meta( $post_id, 'awbase_citation_enable', true ) !== '1' ) return;

    $article_type = get_post_meta( $post_id, 'awbase_citation_article_type', true ) ?: 'ScholarlyArticle';
    $abstract     = get_post_meta( $post_id, 'awbase_citation_abstract', true );
    $keywords_raw = get_post_meta( $post_id, 'awbase_citation_keywords', true );
    $publisher    = get_post_meta( $post_id, 'awbase_citation_publisher', true );
    $doi          = get_post_meta( $post_id, 'awbase_citation_doi', true );
    $citations_raw = get_post_meta( $post_id, 'awbase_citation_list', true );

    $schema = array(
        '@context'      => 'https://schema.org',
        '@type'         => $article_type,
        'headline'      => get_the_title(),
        'url'           => get_permalink(),
        'datePublished' => get_the_date('Y-m-d'),
        'dateModified'  => get_the_modified_date('Y-m-d'),
        'author'        => array( '@type' => 'Person', 'name' => get_the_author() ),
    );

    if ( $abstract ) {
        $schema['abstract'] = $abstract;
    }
    if ( $keywords_raw ) {
        $schema['keywords'] = array_map( 'trim', explode( ',', $keywords_raw ) );
    }
    if ( $publisher ) {
        $schema['publisher'] = array( '@type' => 'Organization', 'name' => $publisher );
    }
    if ( $doi ) {
        $schema['identifier'] = $doi;
        $schema['url'] = $doi; // prefer DOI as canonical URL
    }
    if ( $citations_raw ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", $citations_raw ) ) );
        $schema['citation'] = array_values( array_map( function( $line ) {
            // If line contains URL, extract it
            if ( preg_match( '/(https?:\/\/\S+)/', $line, $m ) ) {
                return array( '@type' => 'CreativeWork', 'name' => trim( preg_replace( '/(https?:\/\/\S+)/', '', $line ) ), 'url' => $m[1] );
            }
            return array( '@type' => 'CreativeWork', 'name' => $line );
        }, $lines ) );
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
