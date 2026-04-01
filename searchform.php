<?php
// AW-Base Search Form Template
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input
        type="search"
        class="search-field"
        placeholder="<?php echo esc_attr_x( '検索...', 'placeholder', 'aw-base' ); ?>"
        value="<?php echo esc_attr( get_search_query() ); ?>"
        name="s"
        autocomplete="off"
    >
    <button type="submit" class="search-submit">
        <i class="fa-solid fa-magnifying-glass"></i>
        <span class="screen-reader-text"><?php esc_html_e( '検索', 'aw-base' ); ?></span>
    </button>
</form>
