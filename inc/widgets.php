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
