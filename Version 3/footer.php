<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 */
?>
    </div><!-- #content -->
    <footer id="colophon" class="site-footer" role="contentinfo">
            <div class="site-footer__wrap">
                <?php
                // Make sure there is a social menu to display.
                if ( has_nav_menu( 'social' ) ) { ?>
                <nav class="social-menu">
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'social',
                            'menu_class'     => 'social-links-menu',
                            'depth'          => 1,
                            'link_before'    => '<span class="screen-reader-text">',
                            'link_after'     => '</span>' . humescores_get_svg( array( 'icon' => 'chain' ) ),
                        ) );
                    ?>
                </nav><!-- .social-menu -->
                <?php } ?>

                <div class="site-info">

				    <div><?php printf('To find out more, please contact:'); ?></div>
				    <div><?php  printf( 'Phone:(+65)6709 3888' ); ?></div>
                    <div><?php  printf( 'Email:admissions-singapore@jcu.edu.au' ); ?></div>
                    <div><?php  printf( 'Located in: 149 Sims Drive 387380' ); ?></div>


                </div><!-- .site-info -->
            </div><!-- .site-footer__wrap -->
        </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
