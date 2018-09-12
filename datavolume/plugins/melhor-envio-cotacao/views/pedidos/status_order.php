<strong>Status WooCommerce:</strong>
<span><?php echo wpmelhorenvio_getHumanTitle($status_wc, 'wc'); ?></span></br>
            
<?php if (!is_null($status_me)) { ?>
    <strong>Status Etiqueta:</strong>
    <span><?php echo wpmelhorenvio_getHumanTitle($status_me, 'me'); ?></span></br>
<?php } ?>