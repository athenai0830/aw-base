<?php
// AW-Base SEO Features
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Pagination Canonical URL fix (Make page/2 canonical to itself, not page 1)
function awbase_canonical_url_fix( $canonical_url, $post ) {
    if ( is_paged() ) {
        // WordPress natively points paged canonical to itself starting from some ver, but ensuring here
        global $wp_query;
        $page = get_query_var( 'paged' );
        if ( $page >= 2 ) {
            $link = get_pagenum_link( $page );
            return $link;
        }
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'awbase_canonical_url_fix', 10, 2 );

// 1.1 Override default wp_head canonical if needed for term archives
function awbase_term_canonical() {
    if ( is_category() || is_tag() || is_tax() ) {
        if ( is_paged() ) {
            $page = get_query_var( 'paged' );
            $link = get_pagenum_link( $page );
            echo '<link rel="canonical" href="' . esc_url($link) . '" />' . "\n";
            // remove default
            remove_action( 'wp_head', 'rel_canonical' );
        }
    }
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
