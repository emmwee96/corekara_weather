<div class="c-product">
    <div class="row">
        <div class="col-md-12 col-lg-6 col-xl-6 d-flex">
            <div class="c-list">
                <div class="c-backicon">
                    <a href="<?=base_url()?>/main/index">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </div>
                <div class="c-title">
                    <p><?= $lang['collection']?></p>
                </div>
                <div class="nav flex-column">
                    <?php foreach($category as $row) { ?>
                    <a class="nav-link"
                        href="<?= base_url() . "/Main/category/". $row['category_id']?>"><?= $row['category']?></a>

                    <?php }?>
                </div>

                <br>
                <div class="">

                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <?php 
                            foreach($product_image as $key=> $rowImg){

                        ?>
                        <!-- <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                            <div class="c-chooseimg">
                                <img src="<?=base_url()?>/assets/img/main/dress.png" alt="">
                            </div>
                        </a> -->
                        <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill"
                            href="#img<?= $rowImg['product_image_id'] ?>" role="tab"
                            aria-controls="v<?= $rowImg['product_image_id'] ?>" aria-selected="false">
                            <div class="c-chooseimg">
                                <img src="<?=base_url() . "/" . $rowImg['product_image'] ?> " alt="">
                            </div>
                        </a>
                        <!-- <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">
                            <div class="c-chooseimg">
                                <img src="<?=base_url()?>/assets/img/main/07.jpg" alt="">
                            </div>
                        </a>  -->
                        <?php 
                        }
                        ?>
                    </div>

                </div>
            </div>
            <div class="c-change">
                <div class="tab-content" id="v-pills-tabContent">
                    <?php 
                        foreach($product_image as $key=> $rowImg){
                            if($key == 0){

                            
                    ?>

                    <div class="tab-pane fade show active" id="img<?= $rowImg['product_image_id'] ?>" role="tabpanel"
                        aria-labelledby="v-pills-home-tab">
                        <div class="c-productimg">
                            <img src="<?=base_url() . "/" . $rowImg['product_image'] ?> " alt="">
                        </div>
                    </div>
                    <?php 
                            }else{
                                ?>
                    <div class="tab-pane fade" id="img<?= $rowImg['product_image_id'] ?>" role="tabpanel"
                        aria-labelledby="v-pills-home-tab">
                        <div class="c-productimg">
                            <img src="<?=base_url() . "/" . $rowImg['product_image'] ?> " alt="">
                        </div>
                    </div>


                    <?php
                            }
                        }
                    ?>
                    <!-- <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        <div class="c-productimg">
                            <img src="<?=base_url()?>/assets/img/main/03.jpg" alt="">
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                        <div class="c-productimg">
                            <img src="<?=base_url()?>/assets/img/main/07.jpg" alt="">
                        </div>
                    </div> -->

                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 col-xl-6 d-flex">
            <div class="c-productdetail">


                <div class="c-productdescrip">
                    <p class="thename"><?= $product['product']?></p>
                    <p class="thecolor">
                        <?php foreach($product_color as $key => $rowC){ ?>
                            <?= ($key != 0)? ", " : " "?><?= $rowC['color'] ?>
                        <?php } ?>
                    </p>
                    <p class="theprice" id="product_price">RM <?= $product['selling_price']?></p>
                    <!-- <p class="thesub">DOUBLE-BREASTED, FOUR-BUTTON LONG COAT WITH A PEAKED LAPEL AND SQUARE-CUT FRONT.</p> -->
                    <div class="">
                        <!-- <p> • 99% CASHMERE, 1% WOOL</p>
                        <p> • CUPRO AND COTTON LINING</p>
                        <p> • DOUBLE-BREASTED, FOUR-BUTTON CLOSURE</p>
                        <p> • TWO JETTED POCKETS AT THE FRONT, ONE WELT POCKET AT THE CHEST</p>
                        <p> • TWO INNER JETTED POCKETS</p>
                        <p> • PEAKED LAPEL</p>
                        <p> • FOUR-BUTTON CUFFS</p>
                        <p> • SINGLE BACK VENT</p>
                        <p> • PADDED SHOULDERS</p> -->
                        <p><?= $product['long_description']?></p>
                    </div>
                    <div class="form-group row mb-4">
                        <!-- <select name="" class="form-control" id="chooseColor">
                            <option value="FF0000">Red</option>
                            <option value="FFFFFF">White</option>
                            <option value="000000">Black</option>
                        </select> -->
                        <!-- <div id="theColor"></div> -->
                        <label for="" class="col-sm-2 col-form-label"><?= $lang['color']?></label>
                        <div class="col-sm-10">
                            <select class="form-control" id="color_id" onchange="get_product_price()">
                                <?php foreach($product_color as $rowC){ ?>
                                <option value="<?= $rowC['color_id'] ?>"><?= $rowC['color'] ?></option>

                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="c-sizeguide">
                        <p>L.CELEBS <?= $lang['size']?>:</p>
                        <a href="">
                            <?= $lang['size_guide']?> <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                    <div class="form-group mb-4">
                        <select class="form-control thecustom-control" id="size_id" onchange="get_product_price()">
                            <?php foreach($product_size as $rowS){ ?>
                                <option value="<?= $rowS['size_id'] ?>"><?= $rowS['size'] ?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="form-group row mb-4">
                        <label for="" class="col-sm-8 col-form-label"><?= $lang['quantity']?></label>
                        <div class="col-sm-4">
                            <input id="quantity" type="number" min="1" class="form-control" name="quantity" value="1">
                        </div>
                    </div>
                    <div class="c-thedelivery">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="c-icon_">
                                <img src="<?=base_url()?>/assets/img/main/delivery.png" alt="">
                            </div>
                            <div class="c-policydetail_">
                                <p>2 - 4 <?= $lang['workingdays']?></p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center">
                            <div class="c-icon_">
                                <img src="<?=base_url()?>/assets/img/main/return.png" alt="">
                            </div>
                            <div class="c-policydetail_">
                                <!-- <p>Shipping and Returns Policy</p> -->
                                <a href=""><?= $lang['shippingreturn_policy']?></a>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 mb-4">
                        <div class="c-btn">
                            <a onclick="addToCart()" class="btn btn-shopnow"><?= $lang['addto_cart']?></a>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        get_product_price();
    });

    $(document).on("change", "#color_id, #size_id", function (e) {
        get_product_price();
    });

    function get_product_price() {

        var color_id = $('#color_id').val();
        var size_id = $('#size_id').val();

        var postParam = {
            product_id: <?= $product['product_id'] ?> ,
            color_id: color_id,
            size_id: size_id,
        };

        $.ajax({
            type: "POST",
            url: "<?= base_url() . '/Main/get_product_price/' ?>",
            data: postParam,
            success: function (data) {

                data = jQuery.parseJSON(data);
                if (data.status) {
                    // window.location.reload();
                    $('#product_price').text('RM ' + data.price[0].price);

                } else {
                    alert(data.message);
                }
            }
        });
    }

    function addToCart(){
        var product_id = <?= $product['product_id'] ?>;
        var color_id = $('#color_id').val();
        var size_id = $('#size_id').val();
        var quantity = $('#quantity').val();

        var postParam = {
            product_id: product_id,
            color_id: color_id,
            size_id: size_id,
            quantity: quantity,
        };

        $.post("<?= base_url('main/add_to_cart') ?>", postParam, function(data){
            data = jQuery.parseJSON(data);
            if(data.status){
                window.location.replace("<?= base_url('main/cart') ?>");
            } else {
                alert("ADD TO CART FAILED");
            }
        });
    }
</script>