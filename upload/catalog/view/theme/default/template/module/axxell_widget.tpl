<div class="box featured_box">
    <h3><?php echo $heading_title; ?></h3>

    <div class="box-products  featured_carousel " >

        <div id="featured_slide" class="carousel-inner " style="position: initial;">
            <?php foreach ($products as $i => $product ) {  ?>
                <div class="product-thumb transition axxell-item" style="width: <?php echo $width; ?>;">
                    <?php if ($product['thumb']) { ?>
                    <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                    </div>
                    <?php } ?>
                    <div class="caption">
                        <div class="name">
                            <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
                        </div>
                        <div class="description">
                            <?php echo utf8_substr( strip_tags($product['description']),0,58);?>...
                        </div>

                        <?php if ($product['price']) { ?>
                        <p class="price">
                            <?php if (!$product['special']) { ?>
                            <?php echo $product['price']; ?>
                            <?php } else { ?>
                            <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
                            <?php } ?>
                        </p>
                        <?php } ?>

                        <?php if ($product['rating'])  { ?>
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++) { ?>
                            <?php if ($product['rating'] < $i) { ?>
                            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
                            <?php } else { ?>
                            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
                            <?php } ?>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="button-group">
                        <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span></button>
                        <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i><span class="hidden-xs hidden-sm hidden-md">&nbsp;</span></button>
                        <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i><span class="hidden-xs hidden-sm hidden-md">&nbsp;</span></button>

                    </div>
                </div>
            <?php } //endforeach; ?>
        </div>
    </div>
</div>

