<?php
if (!defined('ABSPATH')) die();

function divi_child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    wp_enqueue_script( 'isotope','https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js',
        array( 'jquery' )
    );

    // wp_enqueue_script( 'my-scripts', get_stylesheet_directory_uri() . '/js/scripts.js',
    //     array( 'jquery' )
    // );
}
add_action( 'wp_enqueue_scripts', 'divi_child_theme_enqueue_styles' );
//Year

function func_year( $atts ) {
 return date("Y");
}

add_shortcode( 'year','func_year' );

/**
 * Locations
 */
function shortcode_callback_func_locations( $atts = array(), $content = '' ) {


    ob_start(); ?>


        <div class="button-group filter-button-group">
          <button data-filter="*">All Locations</button>

          <?php 

            $terms = get_terms( 'location-category' );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                foreach ( $terms as $term ) {
                    echo '<button data-filter=".'.strtolower($term->name).'">' . $term->name . '</button>';
                }
            }

          ?>
        </div>

        <?php

            $args = array(
                'post_type' => 'location',
                'posts_per_page' => -1
            );
            $the_query = new WP_Query( $args );

            // The Loop
            if ( $the_query->have_posts() ) {
                echo '<div class="grid">';

                while ( $the_query->have_posts() ) {
                    $the_query->the_post();
                        

                       $terms = get_the_terms( get_the_ID(), 'location-category' );
                       $terms = join(', ', wp_list_pluck( $terms , 'name') );
                    ?>

                    <div class="element-item <?php echo strtolower($terms); ?> ">
                        <h3 class="title"><?php the_title(); ?></h3>

                        <div class="address"><?php the_field('address'); ?></div>

                        <div class="icon-area">
                            <?php 

                                $tel = str_replace("(", "", get_field('phone'));
                                $tel = str_replace(")", "", $tel);
                                $tel = str_replace("-", "", $tel);
                                $tel = str_replace(" ", "", $tel);

                            ?>

                            <a href="tel:<?php the_field('phone'); ?>" class="phone"></a>
                            <a href="<?php the_field('url'); ?>" target="_blank" class="url"></a>
                        </div>
                        
                      </div>

                    <?php
                }

                echo '</div>';
            } else {
                // no posts found
            }
            /* Restore original Post Data */
            wp_reset_postdata();

        ?> 

    <?php

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}
add_shortcode( 'location_grid', 'shortcode_callback_func_locations' );