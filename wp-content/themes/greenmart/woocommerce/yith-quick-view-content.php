<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


while ( have_posts() ) : the_post(); ?>

	<div class="product">

	<?php if ( !post_password_required() ) { ?>

		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php
					/**
					 * woocommerce_before_single_product_summary hook
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */

					wc_get_template( 'single-product/product-image-carousel.php' );
					do_action( 'yith_wcqv_after_product_image_carousel' ); 
				?>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php
					$summary_class = '';
					if ( intval( greenmart_tbay_get_config('enable_buy_now', false) ) ) {
			            $summary_class = ' has-buy-now';
			        }
				?>
                <div class="summary entry-summary <?php echo esc_attr($summary_class); ?>">
                    <?php 

                    	do_action( 'yith_wcqv_product_summary' ); 

                    ?>
                </div> 
			</div>
            <?php do_action( 'yith_wcqv_after_product_summary' ); ?>
		</div>

		<?php

	} else {
		echo get_the_password_form();
	}
	?>

	</div>
 

<?php endwhile; // end of the loop.