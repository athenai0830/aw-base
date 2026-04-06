<?php
// AW-Base SEO Features
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Pagination Canonical URL logic
// canonical_paged_to_p1 = '1' → ページ2以降はpage1をcanonicalに
// canonical_paged_to_p1 = '0' (default) → 各ページが自身のURLをcanonicalに
function awbase_canonical_url_fix( $canonical_url, $post ) {
    if ( ! is_paged() ) return $canonical_url;

    $options = get_option( 'awbase_settings', awbase_get_default_settings() );
    $page    = get_query_var( 'paged' );

    if ( ! empty( $options['canonical_paged_to_p1'] ) && $options['canonical_paged_to_p1'] == '1' ) {
        // 2ページ以降はpage1のURLをcanonicalに
        return get_pagenum_link( 1 );
    }

    // デフォルト: 各ページが自身のURLをcanonical
    if ( $page >= 2 ) {
        return get_pagenum_link( $page );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'awbase_canonical_url_fix', 10, 2 );

// 1.1 Override default wp_head canonical for term archives (not covered by get_canonical_url filter)
function awbase_term_canonical() {
    if ( ! ( is_category() || is_tag() || is_tax() ) || ! is_paged() ) return;

    $options = get_option( 'awbase_settings', awbase_get_default_settings() );
    $page    = get_query_var( 'paged' );

    if ( ! empty( $options['canonical_paged_to_p1'] ) && $options['canonical_paged_to_p1'] == '1' ) {
        $link = get_pagenum_link( 1 );
    } else {
        $link = get_pagenum_link( $page );
    }

    echo '<link rel="canonical" href="' . esc_url( $link ) . '" />' . "\n";
    remove_action( 'wp_head', 'rel_canonical' );
}
add_action( 'wp_head', 'awbase_term_canonical', 1 );


// 2. Taxonomy SEO Meta Fields (Category & Tag)
function awbase_taxonomy_add_meta_fields( $taxonomy ) {
    ?>
    <div class="form-field term-group">
        <label for="awbase_seo_title">SEOタイトル</label>
        <input type="text" id="awbase_seo_title" name="awbase_seo_title" value="">
    </div>
    <div class="form-field term-group">
        <label for="awbase_seo_desc">メタディスクリプション</label>
        <textarea id="awbase_seo_desc" name="awbase_seo_desc" rows="3"></textarea>
    </div>
    <div class="form-field term-group">
        <label for="awbase_noindex"><input type="checkbox" id="awbase_noindex" name="awbase_noindex" value="1"> 検索エンジンにインデックスさせない (noindex)</label>
    </div>
    <div class="form-field term-group">
        <label for="awbase_custom_canonical">カスタムCanonical URL</label>
        <input type="url" id="awbase_custom_canonical" name="awbase_custom_canonical" value="">
        <p class="description">別URLを正規化先とする場合に入力。</p>
    </div>
    <?php
}
add_action( 'category_add_form_fields', 'awbase_taxonomy_add_meta_fields', 10, 2 );
add_action( 'post_tag_add_form_fields', 'awbase_taxonomy_add_meta_fields', 10, 2 );

function awbase_taxonomy_edit_meta_fields( $term, $taxonomy ) {
    $title = get_term_meta( $term->term_id, 'awbase_seo_title', true );
    $desc = get_term_meta( $term->term_id, 'awbase_seo_desc', true );
    $noindex = get_term_meta( $term->term_id, 'awbase_noindex', true );
    $canonical = get_term_meta( $term->term_id, 'awbase_custom_canonical', true );
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="awbase_seo_title">SEOタイトル</label></th>
        <td><input type="text" id="awbase_seo_title" name="awbase_seo_title" value="<?php echo esc_attr( $title ); ?>"></td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="awbase_seo_desc">メタディスクリプション</label></th>
        <td><textarea id="awbase_seo_desc" name="awbase_seo_desc" rows="3"><?php echo esc_textarea( $desc ); ?></textarea></td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="awbase_noindex">インデックス設定</label></th>
        <td><label><input type="checkbox" id="awbase_noindex" name="awbase_noindex" value="1" <?php checked('1', $noindex); ?>> 検索エンジンにインデックスさせない (noindex)</label></td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="awbase_custom_canonical">カスタムCanonical URL</label></th>
        <td>
            <input type="url" id="awbase_custom_canonical" name="awbase_custom_canonical" value="<?php echo esc_url( $canonical ); ?>">
            <p class="description">別URLを正規化先とする場合に入力。</p>
        </td>
    </tr>
    <?php
}
add_action( 'category_edit_form_fields', 'awbase_taxonomy_edit_meta_fields', 10, 2 );
add_action( 'post_tag_edit_form_fields', 'awbase_taxonomy_edit_meta_fields', 10, 2 );

function awbase_save_taxonomy_custom_meta( $term_id ) {
    if ( isset( $_POST['awbase_seo_title'] ) ) {
        update_term_meta( $term_id, 'awbase_seo_title', sanitize_text_field( $_POST['awbase_seo_title'] ) );
    }
    if ( isset( $_POST['awbase_seo_desc'] ) ) {
        update_term_meta( $term_id, 'awbase_seo_desc', sanitize_textarea_field( $_POST['awbase_seo_desc'] ) );
    }
    if ( isset( $_POST['awbase_noindex'] ) ) {
        update_term_meta( $term_id, 'awbase_noindex', '1' );
    } else {
        update_term_meta( $term_id, 'awbase_noindex', '0' );
    }
    if ( isset( $_POST['awbase_custom_canonical'] ) ) {
        update_term_meta( $term_id, 'awbase_custom_canonical', esc_url_raw( $_POST['awbase_custom_canonical'] ) );
    }
}
add_action( 'edited_category', 'awbase_save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_category', 'awbase_save_taxonomy_custom_meta', 10, 2 );
add_action( 'edited_post_tag', 'awbase_save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_post_tag', 'awbase_save_taxonomy_custom_meta', 10, 2 );

// 2.5 Output favicon in wp_head
function awbase_output_favicon() {
    $options     = get_option( 'awbase_settings', [] );
    $favicon_url = $options['favicon_url'] ?? '';
    if ( empty( $favicon_url ) ) return;
    $url = esc_url( $favicon_url );
    echo '<link rel="icon" href="' . $url . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . $url . '">' . "\n";
}
add_action( 'wp_head', 'awbase_output_favicon', 1 );

// 3. Output Meta tags in wp_head
function awbase_output_seo_meta_tags() {
    $options = get_option('awbase_settings', awbase_get_default_settings());

    // Basic variables
    $title = '';
    $desc = '';
    $noindex = false;

    if ( is_singular() ) {
        $post_id = get_the_ID();
        $custom_title = get_post_meta($post_id, 'awbase_seo_title', true);
        $custom_desc = get_post_meta($post_id, 'awbase_seo_desc', true);
        $custom_noindex = get_post_meta($post_id, 'awbase_noindex', true);

        $title = $custom_title ? $custom_title : get_the_title();
        $desc = $custom_desc ? $custom_desc : wp_trim_words(get_the_excerpt(), 120, '...');
        if ( $custom_noindex === '1' ) $noindex = true;

    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term_id = get_queried_object_id();
        $custom_title = get_term_meta($term_id, 'awbase_seo_title', true);
        $custom_desc = get_term_meta($term_id, 'awbase_seo_desc', true);
        $custom_noindex = get_term_meta($term_id, 'awbase_noindex', true);

        $title = $custom_title ? $custom_title : single_term_title('', false);
        $desc = $custom_desc ? $custom_desc : term_description();
        if ( $custom_noindex === '1' ) $noindex = true;
    }

    // Checking global noindex settings
    if ( is_search() && $options['noindex_search'] == '1' ) $noindex = true;
    if ( is_404() && $options['noindex_404'] == '1' ) $noindex = true;
    if ( is_date() && $options['noindex_date'] == '1' ) $noindex = true;
    if ( is_author() && $options['noindex_author'] == '1' ) $noindex = true;
    if ( is_tag() && $options['noindex_tag'] == '1' ) $noindex = true;
    if ( is_paged() && $options['noindex_paged'] == '1' ) $noindex = true;

    // Output title (if theme support doesn't handle custom titles well, we might need pre_get_document_title filter)
    
    // Output description
    if ( !empty($desc) ) {
        echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags($desc) ) . '">' . "\n";
    }

    // Output robots
    if ( $noindex ) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }

    // Canonical for term
    if ( (is_category() || is_tag() || is_tax()) && !is_paged() ) {
        $term_id = get_queried_object_id();
        $custom_canon = get_term_meta($term_id, 'awbase_custom_canonical', true);
        if ( $custom_canon ) {
            echo '<link rel="canonical" href="' . esc_url($custom_canon) . '">' . "\n";
        }
    }

    // --- OGP Output ---
    $og_url    = is_singular() ? get_permalink() : ( is_front_page() ? home_url('/') : ( (is_category() || is_tag() || is_tax()) ? get_term_link( get_queried_object_id() ) : '' ) );
    $og_type   = ( is_front_page() || is_home() ) ? 'website' : 'article';
    $site_name = get_bloginfo('name');
    $og_image  = '';

    if ( is_singular() && has_post_thumbnail( $post_id ) ) {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
        if ( $img ) $og_image = $img[0];
    }
    if ( empty($og_image) ) {
        $og_image = ! empty( $options['schema_og_image'] ) ? $options['schema_og_image'] : ( ! empty( $options['logo_image'] ) ? $options['logo_image'] : '' );
    }

    echo '<meta property="og:title" content="' . esc_attr( $title ? $title : $site_name ) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
    if ( $og_url ) {
        echo '<meta property="og:url" content="' . esc_url( $og_url ) . '">' . "\n";
    }
    if ( $og_image ) {
        echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
    }
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    if ( !empty($desc) ) {
        echo '<meta property="og:description" content="' . esc_attr( wp_strip_all_tags($desc) ) . '">' . "\n";
    }
    echo '<meta name="twitter:card" content="' . ( empty($og_image) ? 'summary' : 'summary_large_image' ) . '">' . "\n";
}
add_action( 'wp_head', 'awbase_output_seo_meta_tags', 2 );

// Title override
function awbase_override_document_title($title) {
    if ( is_singular() ) {
        $custom_title = get_post_meta(get_the_ID(), 'awbase_seo_title', true);
        if ( $custom_title ) return $custom_title;
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term_id = get_queried_object_id();
        $custom_title = get_term_meta($term_id, 'awbase_seo_title', true);
        if ( $custom_title ) return $custom_title;
    }
    return $title;
}
add_filter('pre_get_document_title', 'awbase_override_document_title');

// ============================================================
// 4. Structured Data / JSON-LD
// ============================================================
function awbase_output_json_ld() {
    $options  = get_option( 'awbase_settings', awbase_get_default_settings() );
    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );
    $site_desc = get_bloginfo( 'description' );

    // --- 運営者・組織情報 ---
    $org_name    = ! empty( $options['schema_org_name'] )    ? $options['schema_org_name']    : $site_name;
    $org_logo    = ! empty( $options['schema_og_image'] )    ? $options['schema_og_image']     :
                   ( ! empty( $options['logo_image'] )       ? $options['logo_image']          : '' );
    $org_address = ! empty( $options['schema_org_address'] ) ? $options['schema_org_address']  : '';
    $org_phone   = ! empty( $options['schema_org_phone'] )   ? $options['schema_org_phone']    : '';
    $org_email   = ! empty( $options['schema_org_email'] )   ? $options['schema_org_email']    : '';

    // --- 著者情報（グローバル設定） ---
    $author_name    = ! empty( $options['schema_author_name'] )    ? $options['schema_author_name']    : '';
    $author_altname = ! empty( $options['schema_author_altname'] ) ? $options['schema_author_altname'] : '';
    $author_url     = ! empty( $options['schema_author_url'] )     ? $options['schema_author_url']     : '';

    // --------------------------------------------------------
    // WebSite schema（全ページ共通）
    // --------------------------------------------------------
    $website = array(
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => $site_name,
        'url'      => $site_url,
    );
    if ( $site_desc ) {
        $website['description'] = $site_desc;
    }
    echo '<script type="application/ld+json">' . wp_json_encode( $website, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

    // --------------------------------------------------------
    // Blog schema（フロントページ / ブログトップページ）
    // --------------------------------------------------------
    if ( is_front_page() || is_home() ) {
        $blog_person = array( '@type' => 'Person', 'name' => $org_name, 'url' => $site_url );
        if ( $author_name ) {
            $blog_person['name'] = $author_name;
            if ( $author_altname ) $blog_person['alternateName'] = $author_altname;
            if ( $author_url )    $blog_person['url']           = $author_url;
        }

        $blog_image = array();
        if ( $org_logo ) {
            $blog_image = array( '@type' => 'ImageObject', 'url' => $org_logo );
        }

        // 最新投稿を blogPost として含める
        $latest = get_posts( array( 'numberposts' => 1, 'post_status' => 'publish' ) );
        $blog_post_obj = null;
        if ( $latest ) {
            $lp          = $latest[0];
            $lp_author   = $blog_person;
            $lp_image    = $blog_image;
            $lp_thumb    = get_post_thumbnail_id( $lp->ID );
            $lp_thumb_src = $lp_thumb ? wp_get_attachment_image_src( $lp_thumb, 'full' ) : null;
            if ( $lp_thumb_src ) {
                $lp_image = array( '@type' => 'ImageObject', 'url' => $lp_thumb_src[0], 'width' => $lp_thumb_src[1], 'height' => $lp_thumb_src[2] );
            }
            $blog_post_obj = array(
                '@type'         => 'BlogPosting',
                'datePublished' => get_the_date( 'c', $lp ),
                'dateModified'  => get_the_modified_date( 'c', $lp ),
                'image'         => $lp_image,
                'editor'        => $lp_author,
                'author'        => $lp_author,
                'creator'       => $lp_author,
                'copyrightHolder' => $lp_author,
            );
        }

        $blog_schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Blog',
        );
        if ( $blog_post_obj ) {
            $blog_schema['blogPost'] = $blog_post_obj;
        }
        echo '<script type="application/ld+json">' . wp_json_encode( $blog_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }

    // --------------------------------------------------------
    // Organization schema（組織情報が1つでも設定されていれば出力）
    // --------------------------------------------------------
    if ( $org_address || $org_phone || $org_email || $author_name ) {
        $publisher_obj = array(
            '@type' => 'Organization',
            'name'  => $org_name,
            'url'   => $site_url,
        );
        if ( $org_logo ) {
            $publisher_obj['logo'] = array( '@type' => 'ImageObject', 'url' => $org_logo );
        }
        if ( $org_address ) {
            $publisher_obj['address'] = array( '@type' => 'PostalAddress', 'streetAddress' => $org_address );
        }
        if ( $org_phone ) {
            $publisher_obj['telephone'] = $org_phone;
        }
        if ( $org_email ) {
            $publisher_obj['email'] = $org_email;
        }
        if ( $author_name ) {
            $founder = array( '@type' => 'Person', 'name' => $author_name );
            if ( $author_altname ) $founder['alternateName'] = $author_altname;
            if ( $author_url )    $founder['url']           = $author_url;
            $publisher_obj['founder'] = $founder;
        }
        echo '<script type="application/ld+json">' . wp_json_encode( $publisher_obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }

    // --------------------------------------------------------
    // BlogPosting schema（投稿ページのみ）
    // --------------------------------------------------------
    if ( is_singular( 'post' ) ) {
        $post_id        = get_the_ID();
        $post_author_id = (int) get_post_field( 'post_author', $post_id );

        // 著者: グローバル設定 → WPユーザー表示名 → 組織名 → サイト名
        $a_name    = $author_name ?: get_the_author_meta( 'display_name', $post_author_id ) ?: $org_name ?: $site_name;
        $a_altname = $author_altname;
        $a_url     = $author_url     ?: get_author_posts_url( $post_author_id );

        $author_obj = array( '@type' => 'Person', 'name' => $a_name );
        if ( $a_altname ) $author_obj['alternateName'] = $a_altname;
        if ( $a_url )     $author_obj['url']           = $a_url;

        $pub = array( '@type' => 'Organization', 'name' => $org_name );
        if ( $org_logo ) {
            $pub['logo'] = array( '@type' => 'ImageObject', 'url' => $org_logo );
        }

        $schema = array(
            '@context'         => 'https://schema.org',
            '@type'            => 'BlogPosting',
            'headline'         => get_the_title(),
            'url'              => get_permalink(),
            'datePublished'    => get_the_date( 'c' ),
            'dateModified'     => get_the_modified_date( 'c' ),
            'author'           => $author_obj,
            'editor'           => $author_obj,
            'creator'          => $author_obj,
            'copyrightHolder'  => $author_obj,
            'publisher'        => $pub,
            'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink() ),
        );

        // description
        $custom_desc = get_post_meta( $post_id, 'awbase_seo_desc', true );
        $desc        = $custom_desc ?: wp_strip_all_tags( get_the_excerpt() );
        if ( $desc ) {
            $schema['description'] = wp_trim_words( $desc, 120, '...' );
        }

        // image（アイキャッチ → フォールバック画像）
        if ( has_post_thumbnail( $post_id ) ) {
            $thumb_id  = get_post_thumbnail_id( $post_id );
            $thumb_src = wp_get_attachment_image_src( $thumb_id, 'full' );
            if ( $thumb_src ) {
                $schema['image'] = array(
                    '@type'  => 'ImageObject',
                    'url'    => $thumb_src[0],
                    'width'  => $thumb_src[1],
                    'height' => $thumb_src[2],
                );
            }
        } elseif ( $org_logo ) {
            $schema['image'] = array( '@type' => 'ImageObject', 'url' => $org_logo );
        }

        // articleSection（カテゴリー）
        $cats = get_the_category( $post_id );
        if ( $cats ) {
            $schema['articleSection'] = array_map( function( $c ) { return $c->name; }, $cats );
        }

        // keywords（タグ）
        $tags = get_the_tags( $post_id );
        if ( $tags ) {
            $schema['keywords'] = implode( ', ', array_map( function( $t ) { return $t->name; }, $tags ) );
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }

    // --------------------------------------------------------
    // BreadcrumbList schema（トップページ以外）
    // --------------------------------------------------------
    if ( ! is_front_page() ) {
        $items = array(
            array(
                '@type'    => 'ListItem',
                'position' => 1,
                'name'     => $site_name,
                'item'     => $site_url,
            ),
        );
        $pos = 2;

        if ( is_singular( 'post' ) ) {
            $cats = get_the_category();
            if ( $cats ) {
                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => $cats[0]->name,
                    'item'     => get_category_link( $cats[0]->term_id ),
                );
            }
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $pos,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $pos,
                'name'     => single_term_title( '', false ),
                'item'     => get_term_link( get_queried_object() ),
            );
        } elseif ( is_page() ) {
            $ancestors = get_post_ancestors( get_the_ID() );
            foreach ( array_reverse( $ancestors ) as $ancestor ) {
                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => get_the_title( $ancestor ),
                    'item'     => get_permalink( $ancestor ),
                );
            }
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $pos,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        }

        if ( count( $items ) > 1 ) {
            $breadcrumb = array(
                '@context'        => 'https://schema.org',
                '@type'           => 'BreadcrumbList',
                'itemListElement' => $items,
            );
            echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        }
    }
}
add_action( 'wp_head', 'awbase_output_json_ld', 3 );
