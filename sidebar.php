<?php
/**
 * AW-Base Sidebar Template
 *
 * @package AW-Base
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$has_normal = is_active_sidebar( 'sidebar-1' );
$has_sticky = is_active_sidebar( 'sidebar-sticky' );

if ( ! $has_normal && ! $has_sticky ) return;
?>
<div class="sidebar-inner">
    <?php if ( $has_normal ) : ?>
        <div class="sidebar-normal">
            <?php dynamic_sidebar( 'sidebar-1' ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $has_sticky ) : ?>
        <div class="sidebar-sticky-wrap">
            <?php dynamic_sidebar( 'sidebar-sticky' ); ?>
        </div>
    <?php endif; ?>
</div>
