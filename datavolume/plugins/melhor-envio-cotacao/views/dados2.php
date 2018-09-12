<?php

include_once WC_ABSPATH.'/includes/wc-order-functions.php';
include_once plugin_dir_path(__FILE__). '../classes/ME/orders.php';  
include_once plugin_dir_path(__FILE__). '../classes/ME/docs.php'; 
include_once plugin_dir_path(__FILE__). '../classes/ME/args.php'; 
include_once plugin_dir_path(__FILE__). '../classes/ME/tracking.php'; 

$args           = wpmelhorenvio_mountArgsGetOrders($_GET);
$orders         = wc_get_orders($args);
$cotacoesAll    = getNewQuotation($orders);

$infosTrackings = wpmelhorenvio_getAllInfoTrackings($orders, $args);
$tags           = wpmelhorenvio_getStatusTags();

$documents      = wpmelhorenvio_getDocumentsApi();

var_dump($documents['document']);

?>

<style>
    .imgBtnSmall {
        width: 20px!important;
        height: 20px!important;
    }
</style>

<div id="app">

    <div class="loader" style="display:none;">
    </div>

    <div class="content" style="display:block;">
        <div class="wpme_nothing">
        </div>

        <div v-else class="table-pedidos">
            <table>
                <thead>
                    <tr class="action-line">
                        <td colspan="5">
                            <span>SELECIONADOS:</span>
                            <a href="javascript:void(0);" class="btn filter-advance"> Filtro avançado </a>
                            <a href="javascript:void(0);" class="btn comprar-hard addManyToCart"> Adicionar</a>
                        </td>
                        <td>
                            Período </br>
                            <select class="filter-time">
                                <option>Selecione uma opção</option>
                                <option <?php if ($_GET['time'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                <option <?php if ($_GET['time'] == 'day' || !isset($_GET['time'])) { echo 'selected'; } ?> value="day">Hoje</option>
                                <option <?php if ($_GET['time'] == 'week') { echo 'selected'; } ?> value="week">Última semana</option>
                                <option <?php if ($_GET['time'] == 'month') { echo 'selected'; } ?> value="month">Último mês</option>
                                <option <?php if ($_GET['time'] == 'year') { echo 'selected'; } ?> value="year">último ano</option>
                            </select>
                        </td>
                        <td>
                        </td>
                        <td>
                            Status
                            <select class="filter-status">
                                <option>Selecione um status</option>
                                <option <?php if ($_GET['status'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                <?php foreach (wc_get_order_statuses() as $status => $name ) { $status = str_replace('wc-', '', $status);  ?>
                                    <option 
                                        <?php 
                                            if ($_GET['status'] == $status) { echo 'selected'; } 
                                            if (!isset($_GET['status']) && $status == 'processing') { echo 'selected';  }
                                        ?>  
                                            value="<?php echo $status ?>"><?php echo $name ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        
                    </tr>

                    <tr class="header-line">
                        <th width="10px"><input class="mark-all-radios" type="checkbox"></th>
                        <th width="50px"><span>Pedido</span></th>
                        <th width="50px"><span>Data</span></th>
                        <th width="150px"><span>Destinatário</span></th>
                        <th width="75px"><span>Transportadora</span></th>
                        <th width="75px"><span>Status</span></th>
                        <th width="75px"><span>Dados adicionais</span></th>
                        <th width="250px"><span>Opções</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $saved_optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
                        $declaracao_jadlog = false;
                        if($saved_optionals->declaracao_jadlog){
                            $declaracao_jadlog = true;
                        }
                    ?>
                    <?php foreach ($orders as $index => $order) { ?>
                        <?php

                            if (isset($_GET['statusme']) && $infosTrackings[$order->get_id()]['status_me'] != $_GET['statusme'] && $_GET['statusme'] != 'all') {
                                continue;
                            }

                            if (!$cotacoesAll[$order->get_id()]) {
                                continue;
                            }

                            $wcOrder = new WC_Order($order->get_id());
                            $lineItems = htmlspecialchars(json_encode(wpmelhorenvio_getLineItems($wcOrder)));    

                            $client_document = null;
                            if ($wcOrder->billing_cpf){
                                $client_document = $wcOrder->billing_cpf;
                            }

                            if ($wcOrder->billing_cnpj){
                                $client_document = $wcOrder->billing_cnpj;
                            }

                            $phone = null;
                            if ($wcOrder->get_billing_phone()) {
                                $phone = $wcOrder->get_billing_phone();
                            }
                        ?>
                        <input type="hidden" class="order_id_index_<?php echo $index; ?>" value="<?php echo $order->get_id(); ?>" />
                        <input type="hidden" class="order_customer_document_<?php echo $index; ?>" value="<?php echo $client_document; ?>" />
                        <input type="hidden" class="order_price_declared_<?php echo $index; ?>" value="<?php echo $wcOrder->get_total(); ?>" />
                        <input type="hidden" class="order_price_declared_<?php echo $index; ?>" value="<?php echo $wcOrder->get_total(); ?>" />
                        <input type="hidden" class="client_phone_<?php echo $index; ?>" value="<?php echo $phone; ?>" />
                        <input type="hidden" class="client_email_<?php echo $index; ?>" value="<?php echo $wcOrder->get_billing_email(); ?>" />
                        <input type="hidden" class="client_document_<?php echo $index; ?>" value="<?php echo $wcOrder->billing_cpf; ?>" />
                        <input type="hidden" class="client_company_document_<?php echo $index; ?>" value="<?php echo $wcOrder->billing_cnpj; ?>" />
                        <input type="hidden" class="client_state_register_<?php echo $index; ?>" value="<?php echo $wcOrder->billing_ie; ?>" />
                        <input type="hidden" class="client_note_<?php echo $index; ?>" value="<?php echo $wcOrder->get_customer_note; ?>" />
                        <input type="hidden" class="products_<?php echo $index; ?>" value="<?php echo $lineItems; ?>" />
                        
                        <?php
                            $infoPackage = $cotacoesAll[$order->get_id()][0]['packages'][0];
                        ?>

                        <input type="hidden" class="package_weight__<?php echo $index; ?>" value="<?php echo $infoPackage['weight']; ?>" />
                        <input type="hidden" class="package_width__<?php echo $index; ?>" value="<?php echo $infoPackage['dimensions']['width']; ?>" />
                        <input type="hidden" class="package_height__<?php echo $index; ?>" value="<?php echo $infoPackage['dimensions']['height']; ?>" />
                        <input type="hidden" class="package_length__<?php echo $index; ?>" value="<?php echo $infoPackage['dimensions']['length']; ?>" />
                        
                        <?php
                            $id          = $order->get_id();
                            $status_wc   = $infosTrackings[$id]['status_wc'];
                            $status_me   = $infosTrackings[$id]['status_me'];
                            $tracking_id = $infosTrackings[$id]['tracking_id'];
                            $tracking_mr = $infosTrackings[$id]['tracking_mr'];
                        ?>

                        <tr>
                            <td>
                                <input type="checkbox" class="check-order" data-tracking="<?php echo $tracking_id; ?>" data-order="<?php echo $id; ?>" data-status="<?php echo $status_me ?>" data-index="<?php echo $index; ?>">
                            </td>
                            <td>
                                <?php $link = '/wp-admin/post.php?post='.$order->get_id().'&action=edit' ?>
                                <a target="_blank" href="<?php echo $link; ?>"><?php echo $order->get_id(); ?></a>
                            </td>
                            <td>
                                <?php 
                                    $date = $wcOrder->get_date_modified(); 
                                    echo date("d/m/Y", strtotime($date));
                                ?>
                            </td>
                            <td>
                                <ul>
                                    <?php $shippingAddress = $order->get_address('shipping'); ?>

                                    <input type="hidden" class="order_first_name_<?php echo $index; ?>" value="<?php echo $shippingAddress['first_name']; ?>" />
                                    <input type="hidden" class="order_last_name_<?php echo $index; ?>" value="<?php echo $shippingAddress['last_name']; ?>" />
                                    <input type="hidden" class="order_address1_<?php echo $index; ?>" value="<?php echo $shippingAddress['address_1']; ?>" />
                                    <input type="hidden" class="order_number_<?php echo $index; ?>" value="<?php echo $shippingAddress['number']; ?>" />
                                    <input type="hidden" class="order_country_<?php echo $index; ?>" value="<?php echo $shippingAddress['country']; ?>" />
                                    <input type="hidden" class="order_address2_<?php echo $index; ?>" value="<?php echo $shippingAddress['address_2']; ?>" />
                                    <input type="hidden" class="order_neighborhood_<?php echo $index; ?>" value="<?php echo $shippingAddress['neighborhood']; ?>" />
                                    <input type="hidden" class="order_postcode_<?php echo $index; ?>" value="<?php echo $shippingAddress['postcode']; ?>" />
                                    <input type="hidden" class="order_city_<?php echo $index; ?>" value="<?php echo $shippingAddress['city']; ?>" />
                                    <input type="hidden" class="order_state_<?php echo $index; ?>" value="<?php echo $shippingAddress['state']; ?>" />
                                    
                                    <li><strong><?php echo $shippingAddress['first_name'] . ' ' . $shippingAddress['last_name'] ?> </strong></li>
                                    <li><?php echo $shippingAddress['address_1'] . ' ' . $shippingAddress['address_2'] . ' - ' . $shippingAddress['postcode'] ?></li>
                                    <li><?php echo $shippingAddress['neighborhood'] . ' - ' . $shippingAddress['city'] . '/'. $shippingAddress['state']  ?></li>    
                                </ul>
                            </td>
                            <td>
                                <?php if (!is_null($status_me) && $status_me != 'removed') { ?>
                                    <strong>Ordem ID:</strong></br>
                                    <?php echo $tracking_id; ?>
                                <?php }else { ?>
                                    <?php 
                                        $cots = $cotacoesAll[$order->get_id()];
                                    ?>
                                    <select class="select select-index-<?php echo $index; ?>">
                                        <?php foreach ($cots as $cot) { ?>
                                            <option value="<?php echo $cot['id'] ?>"  <?php if ($cot['selected'] == true){ echo 'selected'; } ?> > 
                                            <?php  echo $cot['name'] . ' | ' . $cot['delivery_time'] . ' dias | ' . $cot['currency'] . ' ' . ($cot['price'] - $cot['taxe_extra']); ?>
                                            </option> 
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </td>

                            <td>
                                <strong>Status WooCommerce:</strong>
                                <span><?php echo wpmelhorenvio_getHumanTitle($status_wc, 'wc'); ?></span></br>
                                            
                                <?php if (!is_null($status_me)) { ?>
                                    <strong>Status Etiqueta:</strong>
                                    <span><?php echo wpmelhorenvio_getHumanTitle($status_me, 'me'); ?></span></br>
                                <?php } ?>
                            </td>

                            <td width="75px">
                                <?php $docs = wpmelhorenvio_getDocsOrder($order->get_id()); ?>

                                <?php if (!$declaracao_jadlog) { ?>
                                    <label>
                                        <strong>Chave-NF:</strong>
                                        <span class="spn-key-nf-<?php echo $index; ?>"><?php echo $docs['key_nf']; ?></span>
                                        <input type="hidden" class="docs_key_nf_<?php echo $index; ?>"  value="<?php echo $docs['key_nf']; ?>">
                                    </label> </br>
                                    <label>
                                        <strong>NF:</strong>
                                        <span class="spn-nf-<?php echo $index; ?>"><?php echo $docs['nf']; ?></span>
                                        <input  type="hidden" class="docs_nf_<?php echo $index; ?>" value="<?php echo $docs['nf']; ?>">
                                    </label>
                                <?php } ?>
                                <label >
                                    <strong>CNPJ:</strong>
                                    <span class="spn-cnpj-<?php echo $index; ?>"><?php echo $docs['cnpj']; ?></span>
                                    <input  type="hidden" class="docs_cnpj_<?php echo $index; ?>" value="<?php echo $documents['cnpj']; ?>">
                                </label>

                                <label>
                                    <strong>IE:</strong>
                                    <span class="spn-ie-<?php echo $index; ?>"><?php echo $docs['ie']; ?></span>
                                    <input  type="hidden" class="docs_ie_<?php echo $index; ?>" value="<?php echo $docs['ie']; ?>">
                                </label>
                            </td>
                            <td>
                                <!-- Botão de editar o pedido -->
                                <?php if (is_null($status_me) || $status_me == 'removed') { ?>
                                    <a href="javascript:void(0)" class="btnTable comprar toogleFormModal" data-index="<?php echo $index; ?>">
                                        <img class="imgBtnSmall"  alt="Editar informações" title="Editar informações" src="<?=plugins_url("assets/img/editar.svg",__DIR__ )?>" />
                                    </a>
                                <?php } ?>
                                
                                <!-- Botão de adiconar ao carrinho -->
                                <?php if ( is_null($status_me) || $status_me == 'removed') { ?>
                                    <a href="javascript:void(0);" class="btnTable comprar addToCart" data-index="<?php echo $index; ?>" >
                                        <img class="imgBtnSmall" alt="Adicionar ao carrinho" title="Adicionar ao carrinho" src="<?=plugins_url("assets/img/cart-add.svg",__DIR__ )?>" /> 
                                    </a>
                                <?php } ?>
                                
                                <!-- Botão de pagar etiqueta -->
                                <?php if ($status_me == 'cart') {  ?>
                                    <a href="javascript:void(0);" data-tracking="<?php echo $tracking_id; ?>" data-order="<?php echo $order->get_id(); ?>" data-index="<?php echo $index; ?>" class="btnTable melhorenvio openSinglePaymentSelector">
                                        <img class="imgBtnSmall" alt="Pagar" title="Pagar" src="<?=plugins_url("assets/img/pagar.svg",__DIR__ )?>" />
                                    </a>
                                <?php } ?>
                    
                                <!-- Botão de imprimir etiqueta -->
                                <?php if ($staus_me == 'released' || $status_me == 'printed' || $status_me == 'paid') { ?>
                                    <a href="javascript:void(0);" class="btnTable imprimir printTicket" data-order="<?php echo $order->get_id(); ?>" data-tracking="<?php echo $tracking_id; ?>">
                                        <img class="imgBtnSmall" alt="Imprimir etiqueta" title="Imprimir etiqueta" src="<?=plugins_url("assets/img/imprimir.svg",__DIR__ )?>" /> 
                                    </a>
                                <?php } ?>

                                <!-- Botão de rastreio -->
                                <?php if ($status_me == 'printed' && is_null($tracking_mr)) { ?>
                                    <a href="javascript:void(0);" class="btnTable getTrackingMR" data-tracking="<?php echo $tracking_id; ?>" data-order="<?php echo $order->get_id(); ?>">
                                        <img class="imgBtnSmall" alt="Ver rastreio" title="Ver rastreio" src="<?=plugins_url("assets/img/map2.png",__DIR__ )?>" /> 
                                    </a>
                                <?php } ?>

                                <?php if (!is_null($tracking_mr)) { ?>
                                    <a href="<?php echo 'https://www.melhorrastreio.com.br/rastreio/' . $tracking_mr;  ?>" class="btnTable" target="_blank">
                                        <img class="imgBtnSmall" alt="Ver rastreio" title="Ver rastreio" src="<?=plugins_url("assets/img/map2.png",__DIR__ )?>" /> 
                                    </a>
                                <?php  } ?>
                                    
                                <!-- Botão de atualizar cotação -->
                                <?php //if (is_null($status_me) ) { ?>
                                    <!--<a href="javascript:void(0);" class="btnTable updateQuotation" data-order="<?php echo $order->get_id(); ?>" data-order="<?php echo $order->get_id(); ?>">
                                        <img class="imgBtnSmall" alt="Atualizar cotação" title="Atualizar cotação" src="<?=plugins_url("assets/img/ico_refresh.png",__DIR__ )?>" /> 
                                    </a>-->
                                <?php //} ?>

                                <!-- Botão de excluir o item -->
                                <?php if ( ($status_me == 'cart' || $status_me == 'waiting' || $status_me != 'printed') && (!is_null($status_me) && $status_me != 'removed' ) ) {  ?>
                                    <a href="javascript:void(0);" data-order="<?php echo $order->get_id(); ?>"  data-tracking="<?php echo $tracking_id; ?>" class="btnTable cancelar removeFromCart">
                                        <img class="imgBtnSmall" alt="Excluir" title="Excluir" src="<?=plugins_url("assets/img/excluir.svg",__DIR__ )?>" />
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr class="action-line">
                        <td colspan="5">
                        <span>SELECIONADOS:</span>
                            <a href="javascript:void(0);" class="btn filter-advance"> Filtro avançado </a>
                            <a href="javascript:void(0);" class="btn comprar-hard addManyToCart"> Adicionar</a>
                        </td>
                        <td>
                            Período </br>  
                            <select class="filter-time">
                                <option>Selecione uma opção</option>
                                <option <?php if ($_GET['time'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                <option <?php if ($_GET['time'] == 'day' || !isset($_GET['time'])) { echo 'selected'; } ?> value="day">Hoje</option>
                                <option <?php if ($_GET['time'] == 'week') { echo 'selected'; } ?> value="week">Última semana</option>
                                <option <?php if ($_GET['time'] == 'month') { echo 'selected'; } ?> value="month">Último mês</option>
                                <option <?php if ($_GET['time'] == 'year') { echo 'selected'; } ?> value="year">último ano</option>
                            </select>
                        </td>
                        <td>
                        </td>
                        <td>
                            Status
                            <select class="filter-status">
                                <option>Selecione um status</option>
                                <option <?php if ($_GET['status'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                <?php foreach (wc_get_order_statuses() as $status => $name ) { $status = str_replace('wc-', '', $status);  ?>
                                    <option 
                                        <?php 
                                            if ($_GET['status'] == $status) { echo 'selected'; } 
                                            if (!isset($_GET['status']) && $status == 'processing') { echo 'selected';  }
                                        ?>  
                                            value="<?php echo $status ?>"><?php echo $name ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="modal modal_payments" style="display:none; z-index:2;">
            <a href="javascript:void(0);" class="close-modal close-modal-payments"> &times </a>
            <h1 >Escolha seu método de pagamento</h1>
            <radiogroup class="select">
                <label>
                    <input name="selected_payment_method" type="radio" value="moip">
                    <img src="<?=plugins_url("assets/img/moip.png",__DIR__ )?>">
                </label>
                <label>
                    <input  name="selected_payment_method" type="radio"  value="mercado-pago">
                    <img src="<?=plugins_url("assets/img/mpago.png",__DIR__ )?>">
                </label>

                <label>
                    <input  name="selected_payment_method" type="radio"  checked="checked" value="99">
                    <div class="pgsaldo">
                        <h4>Pagar com Saldo</h4>
                        <p>Saldo <strong class="user-balance"></strong></p>
                    </div>
                </label>
            </radiogroup>
            <input type="hidden" class="pay-tracking" />
            <input type="hidden" class="pay-order" />
            <a href="javascript:void(0);" class="btn pagar payTicketMe"> Pagar </a>
        </div>

        <div class="modal modal-confirm" style="display:none; z-index:2;">
            <a href="javascript:void(0);"  class="close-modal close-modal-confirm"> &times </a>
            <h1 class="wpme_error">Você tem certeza que deseja cancelar?</h1>
            <p>Ao clicar em "Quero Cancelar" a etiqueta se torna inutilizavel.</p>
            <input type="hidden" class="cancel-order" />
            <input type="hidden" class="cancel-tracking" />

            <a href="javascript:void(0);" class="btn cancelar btnConfirm">Quero cancelar</a>  <a href="javascript:void(0);"  class="btn fechar close-modal-confirm">Fechar</a>
        </div>

        <div class="mask">
        </div>

        <div class="modal modalDocs" style="height:auto; display:none">
            <h1>Inserir os dados</h1>
            <div class="wpme_wrapper_center">
                <form  class="update-order">
                    <input type="hidden" name="id" value="" class="order_selected_id_modal" />
                    <div class="form">

                        <?php if (!$declaracao_jadlog) {  ?>
                            <fieldset>
                                <label>Chave da nota fiscal</label>
                                <input type="text" name="key-nf" class="order_selected_key_nf"  />
                            </fieldset>

                            <fieldset>
                                <label>Número da nota fiscal</label></br>
                                <input type="text" name="nf" class="order_selected_nf" />
                            </fieldset>
                        <?php } ?>

                        <fieldset> 
                            <label>CNPJ</label></br>
                            <input type="text" value="<?php echo $documents['document']; ?>" name="cnpj" class="order_selected_cnpj"  />
                        </fieldset>

                        <fieldset> 
                            <label>Inscrição estadual</label></br>
                            <input type="text" name="ie" class="order_selected_ie" />
                        </fieldset>
                    </div>
                    <div class="buttons">
                        <a class="close-form-docs" href="javascript:void();">Fechar</a>
                        <button type="submit" class="btn getFormValues" >Atualizar</button>
                    </div>
                </form>                
            </div>
        </div>

        <div class="modal modalFilterAdvance" style="height:auto; display:none">
            <h1>Filtro de pedidos</h1>
            <div class="wpme_wrapper_center">
                
                <div class="form update-order">
                    <fieldset>
                        <label>Status do Pedido (WooCommerce)</label>
                        <select class="status-wc-advance input-advance ">
                            <?php foreach (wc_get_order_statuses() as $status => $name ) { $status = str_replace('wc-', '', $status);  ?>
                                <option 
                                    <?php 
                                        if ($_GET['status'] == $status) { echo 'selected'; } 
                                    ?>  
                                        value="<?php echo $status ?>"><?php echo $name ?></option>
                            <?php } ?>
                            <option <?php if ($_GET['status'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                        </select>
                    </fieldset>

                    <fieldset>
                        <label>Status da Etiqueta (Melhor Envio)</label></br>
                        <select class="status-me-advance input-advance ">
                            <?php foreach ($tags as $status => $name ) {  if ( empty($name) ) { continue; }  ?>
                                <option 
                                    value="<?php echo $status ?>"><?php echo $name ?>
                                </option>
                            <?php } ?>
                            <option  <?php if ($_GET['statusme'] == 'all') { echo 'selected'; } ?>   value="all">Todos</option>
                        </select>
                    </fieldset>

                    <fieldset> 
                        <label>Data início</label></br>
                        <?php
                            $date_start = date('Y-m-d');
                            if (isset($_GET['datestart'])) {
                                $date_start = $_GET['datestart'];
                            }
                        ?>
                        <input type="date"  name="date_start" class="date-start-advance input-advance" value="<?php echo $date_start ?>" />
                    </fieldset>

                    <fieldset> 
                        <label>Data término</label></br>
                        <?php
                            $date_end = date('Y-m-d');
                            if (isset($_GET['dateend'])) {
                                $date_end = $_GET['dateend'];
                            }
                        ?>
                        <input type="date"  name="date_end" class="date-end-advance" value="<?php echo $date_end; ?>" />
                    </fieldset>

                    <fieldset> 
                        <label>Limite</label></br>

                        <?php
                            $limit = 20;
                            if (isset($_GET['limit'])) {
                                $limit = $_GET['limit'];
                            }
                        ?>
                        <input type="number"  name="limit-advance" class="limit-advance" value="<?php echo $limit; ?>" />
                    </fieldset>
                    <a class="btn btn-filter-advance" style="cursor:pointer;">Buscar</a>
                </div>
                <a class="close-form-filter-advance" href="javascript:void(0);">Fechar</a>
            </div>
        </div>

        
        <div class="wpme_message" style="display:none;">
            <div class="wpme_message_header"></div>
            <div class="wpme_message_body"></div>
            <div class="wpme_wrapper_center">
                <div class="wpme_message_action"><a href="javascript:void(0);" class="closeError closeReload">Fechar</a></div>
            </div>
        </div>

        <div class="wpme_message_many" style="display:none;">
            <div class="wpme_message_header"></div>
            <div class="wpme_message_body"></div>
            <div class="wpme_wrapper_center">
                <div class="wpme_message_action"><a href="javascript:void(0);" class="closeError closeReload">Fechar</a></div>
            </div>
        </div>

    </div>
</div>
<input type="hidden" class="shop_name" value="<?php echo wpmelhorenvio_getBlogName(); ?>" />
<input type="hidden" class="agency" value="<?php echo wpmelhorenvio_getAgency(); ?>" />
<input type="hidden" class="declaracao_jadlog" value="<?php echo $declaracao_jadlog; ?>" />

<script>
    jQuery(document).ready(function() {

        getLimit();
        toggleLoader();

        jQuery('.filter-status').change(function(){
            var status = jQuery(this).val();
            createUrlRedirect(status, 'status');
        });
        
        jQuery('.filter-time').change(function(){
            var time = jQuery(this).val();
            createUrlRedirect(time, 'time');
        });

        jQuery('.toogleFormModal').click(function() {
            var index = jQuery(this).data('index');
            openModalDocs(index);
        });

        jQuery('.close-modal-payments').click(function() {
            closeModalPayment();
        });

        jQuery('.close-form-docs').click(function() {
            closeModalDocs();
        });

        jQuery('.update-order').submit(function(event) {
            updateDocs();
            event.preventDefault();
        });
        
        jQuery('.addToCart').click(function() {
            var index = jQuery(this).data('index');
            addCart(index);
        });

        jQuery('.removeFromCart').click(function(){
            var tracking_id = jQuery(this).data('tracking');
            var order = jQuery(this).data('order');
            openModalConfirm(order, tracking_id);
        });
        
        jQuery('.btnConfirm').click(function() {
            var order_id = jQuery('.cancel-order').val();
            var tracking_id = jQuery('.cancel-tracking').val();

            removeCart(order_id, tracking_id);
            jQuery('.modal-confirm').hide();
        });

        jQuery('.openSinglePaymentSelector').click(function() {
            
            var order_id = jQuery(this).data('order');
            var tracking_id = jQuery(this).data('tracking');
            jQuery('.pay-tracking').val(tracking_id);
            jQuery('.pay-order').val(order_id);
            var index = jQuery(this).data('index');
            payTicket(order_id, tracking_id);
        });
        
        jQuery('.printTicket').click(function() {
            toggleLoader();
            var tracking_id = jQuery(this).data('tracking');
            var order_id = jQuery(this).data('order');
            printTicket(order_id, tracking_id);
        });

        jQuery('.payTicketMe').click(function() {
            var tracking_id = jQuery('.pay-tracking').val();
            var order_id = jQuery('.pay-order').val();
            payTicketApi(order_id, tracking_id);
        });

        jQuery('.openCancelTicketConfirmer').click(function() {
            var tracking_id = jQuery(this).data('tracking');
            var order_id = jQuery(this).data('order');
            deleteItemMe(order_id, tracking_id);
        });

        jQuery('.updateQuotation').click(function() {
            var order_id = jQuery(this).data('order')
            updateQuotation(order_id);
        });

        jQuery('.close-modal-confirm').click(function() {
            closeModalConfirm();
        });

        jQuery('.closeError').click(function() {
            closeError()
        });
        
        jQuery('.closeReload').click(function() {
            location.reload();
        });

        jQuery('.filter-advance').click(function() {
            openModalFilterAdvance();
        });

        jQuery('.close-form-filter-advance').click(function() {
            jQuery('.mask').hide();
            jQuery('.modalFilterAdvance').hide();
        });
        
        jQuery('.btn-filter-advance').click(function() {
            runFilterAdvanced();
        });

        jQuery('.getTrackingMR').click(function() {
            toggleLoader();
            var tracking_id = jQuery(this).data('tracking');
            var order_id = jQuery(this).data('order');
            getTrackingMr(order_id, tracking_id);
        });

        jQuery('.addManyToCart').click(function() {
            console.log('Click on button add many orders');
            addManyOrders();
        });
        
        jQuery('.mark-all-radios').click(function() {
            jQuery('input:checkbox').not(this).prop('checked', this.checked);
        });

        jQuery('.openMultiplePaymentSelector').click(function() {
            openModalPayment();
            payManyOrders();
        });

        function toggleLoader() {
            var visib = jQuery('.mask').css('display');
            if (visib == 'none') {
                jQuery('.mask').css('display', 'block');
                jQuery('.ico').css('display', 'block');
            } else {
                jQuery('.mask').css('display', 'none');
                jQuery('.ico').css('display', 'none');
            }
        }
        
        function openModalPayment() {
            jQuery('.modal_payments').show();
        }

        function closeModalPayment() {
            jQuery('.modal_payments').hide();
            toggleLoader();
        }

        function showMessageModal(title, message, reload = null) {

            jQuery('.wpme_message').show();
            jQuery('.mask').show();
            jQuery('.wpme_message_header').text(title);
            jQuery('.wpme_message_body').text(message);
            jQuery('.ico').hide();

        }

        function openModalConfirm(order_id, tracking_id) {
            jQuery('.cancel-order').val(order_id);
            jQuery('.cancel-tracking').val(tracking_id);
            jQuery('.modal-confirm').show();
            jQuery('.mask').show();
        }

        function closeModalConfirm() {
            jQuery('.modal-confirm').hide();
            jQuery('.mask').hide();
        }

        function closeError() {
            jQuery('.wpme_message').hide();
            jQuery('.mask').hide();
        }

        function createUrlRedirect(param, type) {

            var url  = window.location.href + '';
            var urlSplited = url.split('?page=wpmelhorenvio_melhor-envio-dados&');

            if (urlSplited.length == 1) {
                window.location = urlSplited[0] + '&' + type + '=' + param;
            }

            var params = urlSplited[1].split('&');
            var numberParams = params.length;
            var findParam = null;
            var extractValue = null;

            if (type == 'time')   { findParam = 'status'; }
            if (type == 'status') {  findParam = 'time';  }

            for (var i=0; i<params.length; i++) {   
                if (params[i].indexOf(findParam + '=') > -1) {
                    var val = params[i].split(findParam + '=');
                    extractValue = val[1];
                }
            }
            window.location = urlSplited[0] + '?page=wpmelhorenvio_melhor-envio-dados&' + findParam + '=' +extractValue + '&' + type + '=' + param;
        }
        
        function openModalDocs(index) {
            jQuery('.order_selected_id_modal').attr('value', index);
            
            var nf       = jQuery('.docs_nf_' + index).val();
            var nf_key   = jQuery('.docs_key_nf_' + index).val();
            var ie       = jQuery('.docs_ie_' + index).val();
            var cnpj     = jQuery('.docs_cnpj_' + index).val();

            jQuery('.order_selected_key_nf').val(nf_key);
            jQuery('.order_selected_nf').val(nf);
            jQuery('.order_selected_ie').val(ie);
            jQuery('.order_selected_cnpj').val(cnpj);

            jQuery('.modalDocs').show();
            jQuery('.mask').show();
        }

        function closeModalDocs() {
            jQuery('.modalDocs').hide();
            jQuery('.mask').hide();
        }

        function updateDocs() {
            var index  = jQuery('.order_selected_id_modal').val();
            data = {
                action:'wpmelhorenvio_ajax_update_info_order',
                security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>',
                id: jQuery('.order_id_index_' + index).val(),
                key_nf: jQuery('.order_selected_key_nf').val(),
                nf: jQuery('.order_selected_nf').val(),
                cnpj: jQuery('.order_selected_cnpj').val(),
                ie: jQuery('.order_selected_ie').val()
            };

            jQuery.post(ajaxurl, data, function(response) {

                jQuery('.docs_key_cnpj_' + index).val(response.id);
                jQuery('.docs_ie_' + index).val(response.ie);
                jQuery('.docs_key_nf_' + index).val(response.key_nf);
                jQuery('.docs_nf_' + index).val(response.nf);
                jQuery('.docs_cnpj_' + index).val(response.cnpj);
                jQuery('.spn-key-nf-' + index).text(response.key_nf);
                jQuery('.spn-nf-' + index).text(response.nf);
                jQuery('.spn-cnpj-' + index).text(response.cnpj);
                jQuery('.spn-ie-' + index).text(response.ie);
                
                toggleLoader();
                closeModalDocs();
                showMessageModal('Sucesso!', 'Documentos atualizados', true);
            });
        }

        function getLastName(index) {
            return jQuery('.order_last_name_' + index).val();
        }        

        function getAgency() {
            var agency = JSON.parse(jQuery('.agency').val());
            return agency;    
        }

        function getProducts(index) {
            var content = jQuery('.products_' + index).val();
            return content;
        }

        function addCart(index) {

            toggleLoader();
            var data = getDataToSendCart(index);
            jQuery.post(ajaxurl, data, function(response) {

                resposta = JSON.parse(response);

                toggleLoader();

                if (resposta.errors) {
                    if (resposta.errors.agency) {
                        showMessageModal('Erro', 'Agência invalida', true);
                        return;
                    }
                }

                if (resposta.error) {
                    showMessageModal('Erro', resposta.error, true);
                    return;
                }

                if(typeof resposta.id != 'undefined'){
                    updateStatus(data.id, resposta.id, 'cart');
                    showMessageModal('Sucesso!', 'Envio adicionado ao carrinho', true);
                    return;
                }
                else {
                    if(resposta.errors && typeof resposta.errors['options.invoice.key'] !== 'undefined') {
                        showMessageModal('Não foi possível adicionar item ao carrinho!', 'Verificar o número da chave da NF', true);
                        return;
                    }

                    if(resposta.errors &&  typeof resposta.errors['options.invoice.number']  !== 'undefined') {
                        showMessageModal('Não foi possível adicionar item ao carrinho!', 'Infelizmente não foi possível adicionar este item ao seu carrinho', true);
                        return;
                    }
                    else {
                        showMessageModal('Não foi possível adicionar item ao carrinho!', 'Infelizmente não foi possível adicionar este item ao seu carrinho', true);
                        return;
                    }
                }
            });
            
        }
        
        // Pega as informações do cliente para enviar na requisição de inserir no carrinho de compras
        function getInfoTo(index) {

            var to = {
                name:             jQuery('.order_first_name_' + index).val()+" "+jQuery('.order_last_name_' + index).val(),
                phone:            jQuery('.client_phone_' + index).val(),
                email:            jQuery('.client_email_' + index).val(),
                document:         jQuery('.client_document_' + index).val(),
                company_document: jQuery('.client_company_document_' + index).val(),
                state_register:   jQuery('.client_state_register_' + index).val(),
                address:          jQuery('.order_address1_' + index).val(),
                complement:       jQuery('.order_address2_' + index).val(),
                number:           jQuery('.order_number_' + index).val(),
                district:         jQuery('.order_neighborhood_' + index).val(),
                city:             jQuery('.order_city_' + index).val(),
                state_abbr:       jQuery('.order_state_' + index).val(),
                country_id:       jQuery('.order_country_' + index).val(),
                postal_code:      jQuery('.order_postcode_' + index).val(),
                note:             jQuery('.client_note_' + index).val(),
            }
            return to;
        }

        // Monta as informações para enviar o pedido para a API da Melhor Envio
        function getDataToSendCart(index) {

            var to = getInfoTo(index);
            var shippingSelected  = jQuery('.select-index-' + index).find('option:selected').val();
            var order_id          = jQuery('.order_id_index_' + index).val();
            var document          = jQuery('.docs_key_cnpj_' + index).val();
            var customer_document = jQuery('.order_customer_document_' + index).val();
            var nf                = jQuery('.docs_nf_' + index).val();
            var key_nf            = jQuery('.docs_key_nf_' + index).val();
            var cnpj              = jQuery('.docs_cnpj_' + index).val();
            var ie                = jQuery('.docs_ie_' + index).val();
            var price_declared    = jQuery('.order_price_declared_' + index).val();
            var shopname          = jQuery('.shop_name').val();
            var agency            = getAgency();         
            var products          = getProducts(index);
            var non_com  = 0;
            
            // Se valor for 1, se trata de não comercial, ou seja, não será necessário passar nota fiscal e inscrição estadual.
            if (jQuery('.declaracao_jadlog').val() == '1') {
                non_com = 1;
            }
            
            if(typeof shippingSelected === 'undefined'){
                showMessageModal('Envio não foi efetuado', 'Tipo de transporte não selecionado. Selecione o tipo de transporte.');
                return;
            }

            if(typeof cnpj === 'undefined' && shippingSelected > 2){
                showMessageModal('Dados incompletos', 'Documento CPF/CNPJ não informados, Adicione essas informaçoes no pedido');
                return;
            }

            if(customer_document == ''){
                showMessageModal('Dados incompletos', 'Documento do cliente não informado. Adicione junto ao painel de pedidos do WooCommerce');
                return;
            }
            
            if(shippingSelected > 4 && (typeof nf === 'undefined' || typeof cnpj === 'undefined' || typeof ie === 'undefined')  ){
                showMessageModal('Dados incompletos', 'Para utilizar essa transportadora, informe a nota fiscal (NF) e os dados da empresa (CNPJ/IE)');
                return;
            }
        
            if( (shippingSelected == 3 || shippingSelected == 4 ) && jQuery('.declaracao_jadlog').val() === "" && ( cnpj === "" || ie === "" )) {
                showMessageModal('Dados incompletos', 'Para utilizar essa transportadora, informe a nota fiscal (NF) e os dados da empresa (CNPJ/IE)');
                return;
            }
            else {
                if (shippingSelected < 3) {
                    var data = {
                        id: order_id,
                        security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>',
                        action: "wpmelhorenvio_ajax_ticketAcquirementAPI",
                        valor_declarado: price_declared,
                        service_id: shippingSelected,
                        from_name: shopname,
                        nf: nf,
                        key_nf: key_nf,
                        to: to,
                        line_items: products,
                        package: null
                    }
                } 
                else {
                    var data = {
                        id: order_id,
                        security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>',
                        action: "wpmelhorenvio_ajax_ticketAcquirementAPI",
                        valor_declarado: price_declared,
                        service_id: shippingSelected,
                        from_name: shopname,
                        from_company_document : document,
                        from_company_state_register: ie,
                        nf: nf,
                        key_nf: key_nf,
                        to: to,
                        line_items: products,
                        package: null,
                        company_document: cnpj,
                        company_state_register: ie,
                        agency: agency.agency,
                        non_com: non_com
                    }
                }
            }
            return data;
        }

        function addManyOrders() {
        }

        function removeCart(order_id, tracking_id) {
            var data = {
                action: 'wpmelhorenvio_ajax_removeTrackingAPI',
                tracking:tracking_id,
                security:'<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>'
            }
            jQuery.post(ajaxurl,data,function (response) {
                resposta = JSON.parse(response);
                if(resposta.succcess == true){
                    updateStatus(order_id, tracking_id, 'removed');
                    jQuery('.mask').show();
                    showMessageModal('Sucesso', 'Item removido com sucesso!');
                }  
            });
        }   

        function deleteItemMe(order_id, tracking_id) {
            toggleLoader();
            data = {
                action: 'wpmelhorenvio_ajax_cancelTicketAPI',
                tracking: tracking_id,
                security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>'
            };
            jQuery.post(ajaxurl,data,function (response) {
                resposta = JSON.parse(response);
                if (resposta.error) {
                    showMessageModal('Não remover a etiqueta', resposta.error, false);
                    return;
                }
                if(resposta.succcess == true){
                    updateStatus(order_id, tracking_id, 'canceled')
                    location.reload();
                }
                toggleLoader();
            });
        }

        function cancelTicket(tracking_id) {
            toggleLoader();
            data = {
                action: 'wpmelhorenvio_ajax_cancelTicketAPI',
                tracking: tracking_id,
                security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>'
            };
            jQuery.post(ajaxurl,data,function (response) {
                resposta = JSON.parse(response);
                if(resposta.succcess == true){
                    location.reload();
                }
                toggleLoader();
            });
        }

        function printTicket(order_id, tracking_id) {
            data = {
                action: 'wpmelhorenvio_ajax_ticketPrintingAPI',
                tracking: [tracking_id],
                security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>'
            };
            jQuery.post(ajaxurl,data,function(response){
                resposta = JSON.parse(response);
                if (resposta.error) {
                    showMessageModal('Erro', resposta.error);
                    return;
                }
                updateStatus(order_id, tracking_id, 'printed')
                toggleLoader();
                window.open(resposta.url,'_blank');
            });
        }
        
        function getTrackingMr(order_id, tracking_id) {

            var data = {
                action: 'wpmelhorenvio_ajax_getTrackingApiMR',
                tracking: tracking_id,
                order_id: order_id,
                security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>'
            };
            jQuery.post(ajaxurl,data,function(response){
                resposta = JSON.parse(response);
                toggleLoader();
                if (resposta.error) {
                    showMessageModal("Ocorreu um erro",  resposta.message);
                    return;
                }

                window.open('https://www.melhorrastreio.com.br/rastreio/' + resposta,'_blank');
            });
            toggleLoader();
        }

        function payTicket(order_id, tracking_id) {
            openModalPayment();
            toggleLoader();
            jQuery('.ico').hide();
        }

        function payTicketApi(order_id, tracking_id) {

            jQuery('.modal_payments').hide();
            var data = {
                security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>',
                action:'wpmelhorenvio_ajax_payTicketAPI',
                orders: [tracking_id],
                gateway: getMethodPaymentselected()
            };

            jQuery.post(ajaxurl, data, function(response) {
                
                resposta = JSON.parse(response);

                if(resposta.error) {
                    showMessageModal("Pagamento não efetuado",  resposta.error);
                    return;
                }
                
                if(resposta.errors) {
                    if(resposta.errors.gateway) {
                        showMessageModal("Pagamento não efetuado",  'Ocorreu um erro no meio de pagamento');
                        return;
                    }
                }

                if(typeof resposta.error !== 'undefined'){
                    showMessageModal("Pagamento não efetuado", resposta.error);
                    return
                } else {
                    if(resposta.redirect != null){
                        updateStatus(order_id, tracking_id, 'waiting');
                        showMessageModal("Esperando confirmação do meio de pagamento", "Esperando confirmação do meio de pagamento");
                        window.open(resposta.redirect,'_blank');
                        return;
                    } else {
                        updateStatus(order_id, tracking_id, 'paid');
                        showMessageModal("Pagamento feito com sucesso", "Seu pagamento foi efetuado com sucesso");
                        return;
                    }
                }
            });                

        }

        function updateQuotation(order_id) {
            toggleLoader();
            data = {
                action:'wpmelhorenvio_ajax_update_quotation_order',
                id: order_id,
                security:'<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>'
            };
            jQuery.post(ajaxurl,data,function(response){
                location.reload();
            });
        }

        function updateStatus(order_id, tracking_id, status) {

            var data = {
                security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>',
                action: "wpmelhorenvio_ajax_updateStatusData",
                tracking_code:tracking_id,
                order_id: order_id,
                status:status
            };

            jQuery.post(ajaxurl, data, function(response) {
                return true;
            });
        }

        function getMethodPaymentselected() {
            return jQuery('input[name="selected_payment_method"]:checked').val();
        }

        function getLimit() {

            var data = {
                action:'wpmelhorenvio_ajax_getBalanceAPI',
                security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>'
            };

            jQuery.post(ajaxurl, data, function(response) {
                resposta = JSON.parse(response);
                jQuery('.user-balance').text('R$ ' + resposta.balance);
            });
        }

        function openModalFilterAdvance() {
            jQuery('.mask').show();
            jQuery('.modalFilterAdvance').show();
        }

        function runFilterAdvanced() {

            var statusWc  = jQuery('.status-wc-advance').val();
            var statusMe  = jQuery('.status-me-advance').val();
            var dateStart = jQuery('.date-start-advance').val();
            var dateEnd   = jQuery('.date-end-advance').val();
            var limit     = jQuery('.limit-advance').val();

            var url  = window.location.href + '';
            var urlSplited = url.split('?page=wpmelhorenvio_melhor-envio-dados');
            var url = urlSplited[0] + '?page=wpmelhorenvio_melhor-envio-dados&status=' + statusWc + '&statusme=' + statusMe + '&datestart=' + dateStart + '&dateend=' + dateEnd + '&limit=' + limit;
            
            window.location = url
        }
    });
</script>