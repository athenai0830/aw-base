<?php
// AW-Base Widgets
if ( ! defined( 'ABSPATH' ) ) exit;

// Register widget areas
function awbase_widgets_init() {
    register_sidebar( array(
        'name'          => 'サイドバー',
        'id'            => 'sidebar-1',
        'description'   => 'メインのサイドバー領域です。',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => 'サイドバー（追従）',
        'id'            => 'sidebar-sticky',
        'description'   => 'スクロールに追従して表示されるサイドバー領域です。',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar(array(
        'name'          => 'フロントページ 上部',
        'id'            => 'front-page-top',
        'description'   => 'フロントページのFV直下に表示されます。広告やお知らせバナーに使用できます。',
        'before_widget' => '<div id="%1$s" class="front-page-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="fp-widget-title">',
        'after_title'   => '</h3>',
    ));
    register_sidebar(array(
        'name'          => 'フッター下部',
        'id'            => 'footer-bottom',
        'description'   => 'フッターの下に表示されます。動画埋め込みやバナーに使用できます。',
        'before_widget' => '<div id="%1$s" class="footer-bottom-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '',
        'after_title'   => '',
    ));
}
add_action( 'widgets_init', 'awbase_widgets_init' );

// Simple Profile Widget Example
class AWBase_Profile_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'awbase_profile_widget',
            'AW-Base プロフィール',
            array('description' => 'サイト管理者のプロフィールを表示します。')
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        // Just use the first admin user's meta for simplicity, or hardcoded for the template
        $admin_id = 1; 
        $author_desc = get_the_author_meta('description', $admin_id);
        $user_info = get_userdata($admin_id);
        ?>
        <div class="author-box widget-author-box">
            <figure class="author-thumb">
                <?php echo get_avatar( $admin_id, 100 ); ?>
            </figure>
            <div class="author-content">
                <div class="author-name">
                    <?php echo esc_html( $user_info ? $user_info->display_name : 'Admin' ); ?>
                </div>
                <div class="author-description">
                    <p><?php echo wp_kses_post( $author_desc ); ?></p>
                </div>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'プロフィール';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">タイトル:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>※ユーザーID=1のプロフィール情報が表示されます。</p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}

function awbase_register_widgets() {
    register_widget( 'AWBase_Profile_Widget' );
}
add_action( 'widgets_init', 'awbase_register_widgets' );

// Calendar: redirect day links to monthly archive
add_filter( 'get_day_link', function( $daylink, $year, $month, $day ) {
    return get_month_link( $year, $month );
}, 10, 4 );
