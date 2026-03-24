<?php
// AW-Base Shortcodes
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Balloon Shortcode
function awbase_balloon_shortcode( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'type' => 'left',
        'name' => '',
        'icon' => get_template_directory_uri() . '/assets/img/default-user.jpg',
    ), $atts, 'balloon' );

    $type_class = $atts['type'] === 'right' ? 'balloon-right' : 'balloon-left';

    ob_start();
    ?>
    <div class="balloon-wrap <?php echo esc_attr($type_class); ?>">
        <div class="balloon-icon">
            <img src="<?php echo esc_url($atts['icon']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
            <?php if ( !empty($atts['name']) ): ?>
                <span class="balloon-name"><?php echo esc_html($atts['name']); ?></span>
            <?php endif; ?>
        </div>
        <div class="balloon-text">
            <?php echo do_shortcode( wpautop( wp_kses_post( $content ) ) ); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'balloon', 'awbase_balloon_shortcode' );

// 2. Add minimal CSS for balloon to wp_head or an enqueued file
function awbase_shortcode_css() {
    ?>
    <style>
    .balloon-wrap { display: flex; align-items: flex-start; margin-bottom: 2em; }
    .balloon-right { flex-direction: row-reverse; }
    .balloon-icon { text-align: center; width: 60px; flex-shrink: 0; }
    .balloon-icon img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
    .balloon-name { font-size: 10px; display: block; margin-top: 5px; }
    .balloon-text { 
        position: relative; 
        padding: 15px; 
        background: #fff; 
        border-radius: 10px; 
        margin: 0 15px; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        max-width: 70%;
    }
    .balloon-text::before {
        content: ""; position: absolute; top: 20px;
        border: 10px solid transparent;
    }
    .balloon-left .balloon-text::before {
        left: -20px; border-right-color: #fff;
    }
    .balloon-right .balloon-text::before {
        right: -20px; border-left-color: #fff;
    }
    .balloon-text p:last-child { margin-bottom: 0; }
    </style>
    <?php
}
add_action('wp_head', 'awbase_shortcode_css');

// 3. Cocoon Compatible Shortcodes [new_list] and [popular_list]
function awbase_new_list_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'count'   => 5,
        'cats'    => '',
        'type'    => 'default', // not fully mapped yet
        'snippet' => 1,
        'arrow'   => 0
    ), $atts, 'new_list' );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => intval( $atts['count'] ),
        'post_status'    => 'publish',
    );

    if ( ! empty( $atts['cats'] ) ) {
        $args['category__in'] = wp_parse_id_list( $atts['cats'] );
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        echo '<div class="entries-list shortcode-new-list ' . ($atts['snippet'] == 0 ? 'no-snippet' : '') . '">';
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/entry-card' );
        }
        echo '</div>';
    } else {
        echo '<p>記事が見つかりませんでした。</p>';
    }
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode( 'new_list', 'awbase_new_list_shortcode' );
// popular_list simply maps to new_list temporarily to prevent shortcode layout breaking, until pageview tracking is built
add_shortcode( 'popular_list', 'awbase_new_list_shortcode' );
