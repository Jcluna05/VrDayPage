<?php
namespace Hurrytimer;
?>

<div id="hurrytimer-actions" class="hurrytimer-tabcontent">
    <?php $i = 0;
    foreach ( $campaign->actions as $action ): ?>
        <div class="hurrytimer-action-block ">
            <div>
                <div class="hurrytimer-action-block-choices">
                    <div class="hurrytimer-action-block-label">
                        <label for="active"><?php _e( "Action", "hurrytimer" ) ?></label>
                    </div>
                    <div class="hurrytimer-action-block-input">
                        <select name="actions[<?php echo $i ?>][id]"
                                class="hurrytimer-action-select">
                            <?php if ( hurryt_is_woocommerce_activated() ): ?>

                            <optgroup label="General">
                                <?php endif; ?>
                                <option value="<?php echo C::ACTION_NONE ?>"
                                        data-subfields="hurrytimer-action-none-subfields"
                                    <?php echo selected( $action[ 'id' ], C::ACTION_NONE ) ?>>
                                    <?php _e( "None", "hurrytimer" ) ?>
                                </option>
                                <option value="<?php echo C::ACTION_HIDE ?>"
                                        data-subfields="hurrytimer-action-hide-subfields"
                                    <?php echo selected( $action[ 'id' ], C::ACTION_HIDE ) ?>>
                                    <?php _e( "Hide countdown timer", "hurrytimer" ) ?>
                                </option>
                                <option value="<?php echo C::ACTION_REDIRECT ?>"
                                        data-subfields="hurrytimer-action-redirect-subfields"
                                    <?php echo selected( $action[ 'id' ], C::ACTION_REDIRECT ) ?>>
                                    <?php _e( "Redirect to...", "hurrytimer" ) ?>
                                <option value="<?php echo C::ACTION_DISPLAY_MESSAGE ?>"
                                        data-subfields="hurrytimer-action-display-message-subfields"
                                    <?php echo selected( $action[ 'id' ],
                                        C::ACTION_DISPLAY_MESSAGE ) ?>>
                                    <?php _e( "Display message...", "hurrytimer" ) ?>
                                </option>
                                <?php if ( hurryt_is_woocommerce_activated() ): ?>
                            </optgroup>
                        <?php endif; ?>

                            <?php if ( hurryt_is_woocommerce_activated() ): ?>

                                <optgroup label="WooCommerce">
                                    <option value="<?php echo C::ACTION_EXPIRE_COUPON ?>"
                                            data-pro-feat [PRO]
                                            data-subfields="hurrytimer-action-expire-coupon-subfields"
                                        <?php echo selected( $action[ 'id' ],
                                            C::ACTION_EXPIRE_COUPON ) ?>>
                                        <?php _e( "Expire Coupon...", "hurrytimer" ) ?>
                                    </option>
                                    <option value="<?php echo C::ACTION_HIDE_ADD_TO_CART_BUTTON ?>"
                                            data-subfields="hurrytimer-action-hide-addtocart-subfields"
                                        <?php echo selected( $action[ 'id' ],
                                            C::ACTION_HIDE_ADD_TO_CART_BUTTON ) ?>>

                                        Hide "Add to cart" button
                                    </option>
                                    <option value="<?php echo C::ACTION_CHANGE_STOCK_STATUS ?>"
                                            data-subfields="hurrytimer-action-stockstatus-subfields"
                                        <?php echo selected( $action[ 'id' ],
                                            C::ACTION_CHANGE_STOCK_STATUS ) ?>>
                                        Change stock status
                                    </option>

                                </optgroup>

                            <?php endif; ?>
                        </select>
                        <div class="hurryt-pro-feat hidden description" style="padding-top: 5px">
                            Expire Coupon is a pro feature. <a
                                    href="http://hurrytimer.com/#pricing?utm_source=plugin&utm_medium=actions&utm_campaign=expire_coupon">Upgrade
                                now</a>
                        </div>

                        <div class="hurryt-compat-info description hidden">
                            One-time and recurring modes only.
                        </div>
                    </div>
                </div>
                <div class="hurrytimer-action-block-subfields hurrytimer-action-expire-coupon-subfields hidden">
                    <div class="hurrytimer-action-block-label">
                        <label for=""><?php _e( "Coupon Code", "hurrytimer" ) ?></label>
                    </div>
                    <div class="hurrytimer-action-block-input">
                        <select name="actions[<?php echo $i ?>][coupon]" id="">
                            <?php foreach ( hurryt_get_wc_coupons() as $coupon ): ?>
                                <option value="<?php echo $coupon->post_title ?>"><?php
                                    echo $coupon->post_title;
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="hurrytimer-action-block-subfields hurrytimer-action-redirect-subfields hidden">
                    <div class="hurrytimer-action-block-label">

                        <label for=""><?php _e( "Redirect URL", "hurrytimer" ) ?></label>
                    </div>
                    <div class="hurrytimer-action-block-input">
                        <input type="text"
                               id="hurrytimer-redirect-url"
                               placeholder="http://"
                               name="actions[<?php echo $i ?>][redirectUrl]"
                               value="<?php echo $action[ 'redirectUrl' ] ?>"
                               class="hurrytimer-redirect-url"/>
                    </div>
                </div>
                <div class="hurrytimer-action-block-subfields hurrytimer-action-display-message-subfields hidden">
                    <div class="hurrytimer-action-block-label">

                        <label for=""><?php _e( "Message", "hurrytimer" ) ?></label>
                    </div>
                    <div class="hurrytimer-action-block-input">
                        <?php echo wp_editor( $action[ 'message' ],
                            'hurrytimer-action-message-' . $i
                            , [
                                'tinymce' => array(
                                    'toolbar1' => 'fontsizeselect,forecolor,backcolor,bold,italic,numlist,bullist,blockquote,removeformat,alignleft,aligncenter,alignright,link,indent,outdent,hr,undo,redo,fullscreen',
                                    'fontsize_formats' => '11px 12px 14px 16px 18px 24px 36px 48px'
                                ),
                                'textarea_name' => "actions[" . $i . "][message]",
                            ] ) ?>
                        <p class="description">Supports shortcodes.</p>
                    </div>
                </div>
                <div class="hurrytimer-action-block-subfields hurrytimer-action-stockstatus-subfields hidden">
                    <div class="hurrytimer-action-block-label">
                        <label for=""><?php _e( "Stock status", "hurrytimer" ) ?></label>
                    </div>
                    <div class="hurrytimer-action-block-input">
                        <select name="actions[<?php echo $i ?>][wcStockStatus]">
                            <option value="<?php echo C::WC_IN_STOCK ?>" <?php echo selected( $action[ 'wcStockStatus' ],
                                C::WC_IN_STOCK ) ?>>In stock
                            </option>
                            <option value="<?php echo C::WC_OUT_OF_STOCK ?>"
                                    p<?php echo selected( $action[ 'wcStockStatus' ],
                                C::WC_OUT_OF_STOCK ) ?>>Out of stock
                            </option>
                            <option value="<?php echo C::WC_ON_BACKORDER ?>"
                                    p<?php echo selected( $action[ 'wcStockStatus' ],
                                C::WC_ON_BACKORDER ) ?>>On backorder
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div>
                <button class="button button-default hurrytimer-delete-action <?php echo count( $campaign->actions ) === 1 ? 'hidden' : '' ?>"
                        type="button">
                    Delete
                </button>
            </div>
        </div>

        <?php $i++; endforeach; ?>
    <div class="hurryt-add-action">
        <button
            <?php //removeIf(pro)
            ?>
                disabled
            <?php //endRemoveIf(pro)
            ?>

                class="button button-default" type="button" id="hurrytimer-new-action">Add another
            action
        </button>
        <br>
        <?php //removeIf(pro)
        ?>
        <p class="description"><span class="dashicons dashicons-lock" style="margin-top:-2px"></span>Adding multiple
            actions is a pro feature. <a
                    href="http://hurrytimer.com/#pricing?utm_source=plugin&utm_medium=actions&utm_campaign=learn_more">Upgrade
                now</a>
        </p>
        <?php //endRemoveIf(pro)
        ?>
    </div>
</div>


