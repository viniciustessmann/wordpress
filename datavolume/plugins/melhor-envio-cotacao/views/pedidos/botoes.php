    <!-- Botão de editar o pedido -->
    <?php if (is_null($status_me) || $status_me == 'removed') { ?>
        <a href="javascript:void(0)" class="btnTable comprar toogleFormModal" data-index="<?php echo $index; ?>">
            <img class="imgBtnSmall"  alt="Editar informações" title="Editar informações" src="<?=plugins_url("../assets/img/editar.svg",__DIR__ )?>" />
        </a>
    <?php } ?>
    
    <!-- Botão de adiconar ao carrinho -->
    <?php if ( is_null($status_me) || $status_me == 'removed') { ?>
        <a href="javascript:void(0);" class="btnTable comprar addToCart" data-index="<?php echo $index; ?>" >
            <img class="imgBtnSmall" alt="Adicionar ao carrinho" title="Adicionar ao carrinho" src="<?=plugins_url("../assets/img/cart-add.svg",__DIR__ )?>" /> 
        </a>
    <?php } ?>
    
    <!-- Botão de pagar etiqueta -->
    <?php if ($status_me == 'cart') {  ?>
        <a href="javascript:void(0);" data-tracking="<?php echo $tracking_id; ?>" data-order="<?php echo $order->get_id(); ?>" data-index="<?php echo $index; ?>" class="btnTable melhorenvio openSinglePaymentSelector">
            <img class="imgBtnSmall" alt="Pagar" title="Pagar" src="<?=plugins_url("../assets/img/pagar.svg",__DIR__ )?>" />
        </a>
    <?php } ?>

    <!-- Botão de imprimir etiqueta -->
    <?php if ($staus_me == 'released' || $status_me == 'printed' || $status_me == 'paid') { ?>
        <a href="javascript:void(0);" class="btnTable imprimir printTicket" data-order="<?php echo $order->get_id(); ?>" data-tracking="<?php echo $tracking_id; ?>">
            <img class="imgBtnSmall" alt="Imprimir etiqueta" title="Imprimir etiqueta" src="<?=plugins_url("../assets/img/imprimir.svg",__DIR__ )?>" /> 
        </a>
    <?php } ?>

    <!-- Botão de rastreio -->
    <?php if ($status_me == 'printed' && is_null($tracking_mr)) { ?>
        <a href="javascript:void(0);" class="btnTable getTrackingMR" data-tracking="<?php echo $tracking_id; ?>" data-order="<?php echo $order->get_id(); ?>">
            <img class="imgBtnSmall" alt="Ver rastreio" title="Ver rastreio" src="<?=plugins_url("../assets/img/map2.png",__DIR__ )?>" /> 
        </a>
    <?php } ?>

    <?php if (!is_null($tracking_mr)) { ?>
        <a href="<?php echo 'https://www.melhorrastreio.com.br/rastreio/' . $tracking_mr;  ?>" class="btnTable" target="_blank">
            <img class="imgBtnSmall" alt="Ver rastreio" title="Ver rastreio" src="<?=plugins_url("../assets/img/map2.png",__DIR__ )?>" /> 
        </a>
    <?php  } ?>
        
    <!-- Botão de atualizar cotação -->
    <?php //if (is_null($status_me) ) { ?>
        <!--<a href="javascript:void(0);" class="btnTable updateQuotation" data-order="<?php echo $order->get_id(); ?>" data-order="<?php echo $order->get_id(); ?>">
            <img class="imgBtnSmall" alt="Atualizar cotação" title="Atualizar cotação" src="<?=plugins_url("../assets/img/ico_refresh.png",__DIR__ )?>" /> 
        </a>-->
    <?php //} ?>

    <!-- Botão de excluir o item -->
    <?php if ( ($status_me == 'cart' || $status_me == 'waiting' || $status_me != 'printed') && (!is_null($status_me) && $status_me != 'removed' ) ) {  ?>
        <a href="javascript:void(0);" data-order="<?php echo $order->get_id(); ?>"  data-tracking="<?php echo $tracking_id; ?>" class="btnTable cancelar removeFromCart">
            <img class="imgBtnSmall" alt="Excluir" title="Excluir" src="<?=plugins_url("../assets/img/excluir.svg",__DIR__ )?>" />
        </a>
    <?php } ?>