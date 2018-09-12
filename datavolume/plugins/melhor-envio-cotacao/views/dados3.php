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
    $docsOrders     = wpmelhorenvio_getDocsOrdes($orders);
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
        <div class="table-pedidos">
            <table>
                <thead>
                    <tr class="action-line">
                        <td colspan="5">
                            <span>SELECIONADOS:</span>
                            <a href="javascript:void(0);" class="btn filter-advance"> Filtro avançado </a>
                            <!-- <a href="javascript:void(0);" class="btn comprar-hard addManyToCart"> Adicionar</a>
                            <a href="javascript:void(0);" class="btn comprar-hard removeManyToCart"> Remover</a> -->
                        </td>
                        <td>
                            <?php include_once plugin_dir_path(__FILE__). '../views/pedidos/periodos.php';  ?>
                        </td>
                        <td>
                        </td>
                        <td>
                            <?php include_once plugin_dir_path(__FILE__). '../views/pedidos/status.php';  ?>
                        </td>
                    </tr>
                    <tr class="header-line">
                        <th width="10px"><input class="mark-all-radios" type="checkbox"></th>
                        <th width="10px"><span>Pedido</span></th>
                        <th width="50px"><span>Data</span></th>
                        <th width="50px"><span>Destinatário</span></th>
                        <th width="75px"><span>Transportadora</span></th>
                        <th width="75px"><span>Status</span></th>
                        <th width="50px"><span>Dados adicionais</span></th>
                        <th width="150px"><span>Opções</span></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        $saved_optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
                        $declaracao_jadlog = false;
                        if($saved_optionals->declaracao_jadlog){
                            $declaracao_jadlog = true;
                        }
                    
                        foreach ($orders as $index => $order) { 
                        
                            if (isset($_GET['statusme']) && $infosTrackings[$order->get_id()]['status_me'] != $_GET['statusme'] && $_GET['statusme'] != 'all') {
                                continue;
                            }
                        
                            if (!$cotacoesAll[$order->get_id()]) {
                                continue;
                            }

                            include plugin_dir_path(__FILE__). '../views/pedidos/info_pedido.php';  
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
                                <?php include plugin_dir_path(__FILE__). '../views/pedidos/cliente_endereco.php';  ?>
                            </td>
                            <td>
                                <?php include plugin_dir_path(__FILE__). '../views/pedidos/cotacoes.php';  ?>
                            </td>
                            <td>
                                <?php include  plugin_dir_path(__FILE__). '../views/pedidos/status_order.php';  ?>
                            </td>
                            <td>
                                <?php include plugin_dir_path(__FILE__). '../views/pedidos/documentos.php';  ?>
                            </td>
                            <td>
                                <?php include plugin_dir_path(__FILE__). '../views/pedidos/botoes.php';  ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr class="action-line">
                        <td colspan="5">
                        <span>SELECIONADOS:</span>
                            <a href="javascript:void(0);" class="btn filter-advance"> Filtro avançado </a>
                            <!-- <a href="javascript:void(0);" class="btn comprar-hard addManyToCart"> Adicionar</a>
                            <a href="javascript:void(0);" class="btn comprar-hard removeManyToCart"> Remover</a> -->
                        </td>
                        <td>
                            <?php include_once plugin_dir_path(__FILE__). '../views/pedidos/periodos.php';  ?>
                        </td>
                        <td>
                        </td>
                        <td>
                            <?php include_once plugin_dir_path(__FILE__). '../views/pedidos/status.php';  ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php include_once plugin_dir_path(__FILE__). '../views/pedidos/modais.php';  ?>
    </div>
</div>

<input type="hidden" class="shop_name" value="<?php echo wpmelhorenvio_getBlogName(); ?>" />
<input type="hidden" class="agency" value="<?php echo wpmelhorenvio_getAgency(); ?>" />
<input type="hidden" class="declaracao_jadlog" value="<?php echo $declaracao_jadlog; ?>" />
<input type="hidden" class="infos_shipping_clients" value="<?php echo htmlspecialchars(json_encode($infoShippingClient)); ?>" />
<input type="hidden" class="infos_order_clients" value="<?php echo htmlspecialchars(json_encode($infoOrderClient)); ?>" />

<script>

jQuery(document).ready(function() {

    getLimit();
    toggleLoader();

    // Aplica o filtro de status
    jQuery('.filter-status').change(function(){
        var status = jQuery(this).val();
        createUrlRedirect(status, 'status');
    });

    // Aplica o filtro de periodo
    jQuery('.filter-time').change(function(){
        var time = jQuery(this).val();
        createUrlRedirect(time, 'time');
    });

    // Abre o modal de documentos
    jQuery('.toogleFormModal').click(function() {
        var index = jQuery(this).data('index');
        openModalDocs(index);
    });

    // Fecha o modal de documentos
    jQuery('.close-form-docs').click(function() {
        closeModalDocs();
    });

    // Fecha modal de pagamentos
    jQuery('.close-modal-payments').click(function() {
        closeModalPayment();
    });

    // Adiciona o pedido no carrinho  da Melhor Envio
    jQuery('.addToCart').click(function() {
        var index = jQuery(this).data('index');
        addCart(index);
    });

    jQuery('.removeFromCart').click(function(){
        var tracking_id = jQuery(this).data('tracking');
        var order = jQuery(this).data('order');
        openModalConfirm(order, tracking_id);
    });

    jQuery('.update-order').submit(function(event) {
        updateDocs();
        event.preventDefault();
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

    jQuery('.mark-all-radios').click(function() {
        jQuery('input:checkbox').not(this).prop('checked', this.checked);
    });

    jQuery('.openMultiplePaymentSelector').click(function() {
        openModalPayment();
        payManyOrders();
    });

    // Abre o loading de carregamento.
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

    // Função de enviar o pedido para a API da Melhor Envio
    function addCart(index) {
        toggleLoader();
        var data = getDataToSendCart(index);
        jQuery.post(ajaxurl, data, function(response) {
            resposta = JSON.parse(response);
            toggleLoader();
            validateReturnAddCart(resposta, data);
        });
    }

    // Monta as informações para enviar o pedido para a API da Melhor Envio
    function getDataToSendCart(index) {

        var info = JSON.parse(jQuery('.infos_order_clients').val());
        var info = info[index];

        // Se valor for 1, se trata de não comercial, ou seja, não será necessário passar nota fiscal e inscrição estadual.
        var non_com  = 0;
        if (jQuery('.declaracao_jadlog').val() == '1') {
            non_com = 1;
        }

        var shippingSelected  = jQuery('.select-index-' + index).find('option:selected').val();
        var nf                = jQuery('.docs_nf_' + index).val();
        var key_nf            = jQuery('.docs_key_nf_' + index).val();
        
        validation = validateShippingMethod(shippingSelected, nf, key_nf, getInfoFrom(index));
        if (validation['error']) {
            showMessageModal('Erro', validation['message'], true);
            return;
        }

        var data = {
            id: info['order_id'],
            security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>',
            action: "wpmelhorenvio_ajax_ticketAcquirementAPI",
            valor_declarado: info['price_declared'],
            service_id: shippingSelected,
            nf: nf,
            key_nf: key_nf,
            to: getInfoTo(index),
            from: getInfoFrom(index),
            line_items: info['products'],
            package: info['packages']
        }
        return data;
    }

    // Valida os campos e documentos para os meios de envios
    function validateShippingMethod(shippingSelected, nf, key_nf, from) {

        var declaracaoJadLog = jQuery('.declaracao_jadlog').val();
        if (declaracaoJadLog.length == 0 && (typeof nf == 'undefined' || typeof key_nf == 'undefined') && (shippingSelected == 3 || shippingSelected == 4)) {
            return {
                error: true,
                message: 'Por favor, inserir nota fiscal e a chave da nota fiscal'
            };
        }

        if ((typeof nf == 'undefined' || typeof key_nf == 'undefined') && shippingSelected > 4) {
            return {
                error: true,
                message: 'Por favor, inserir nota fiscal e a chave da nota fiscal pra usar as transportadoras'
            };
        }

        return {
            error: false
        };
    }

    // Pega as informações do cliente para enviar na requisição de inserir no carrinho de compras
    function getInfoTo(index) {
        var info = JSON.parse(jQuery('.infos_shipping_clients').val());
        return info[index];
    }

    // Pega as informações do vendedor para enviar na requisição de inserir no carrinho de compras
    function getInfoFrom(index) {
        var info = JSON.parse(jQuery('.infos_order_clients').val());
        var data = info[index];
        var from = {
            'document': data['document'],
            'cnpj': jQuery('.docs_cnpj_' + index).val(),
            'ie': jQuery('.docs_ie_' + index).val(),
            'shopname': jQuery('.shop_name').val(),
            'agency': getAgency()
        };
        return from;
    }

    // Função que valida o retorno da inserção de pedido na API da Melhor Envio.
    function validateReturnAddCart(resposta, data) {
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
    }

    function addManyOrders() {
        var data = [];
        jQuery('.check-order').each(function(index) {
            if (jQuery(this).is(':checked')) {
                data.push(getDataToSendCart(index));
            }
        });
        
        var responses = [];
        var count = data.length;
        for (var i=0; i<=count; i++) {
            // jQuery.post(ajaxurl, data[i], function(response) {
            //     responses = JSON.parse(response);
            // });
        }

        // jQuery.post(ajaxurl, data, function(response) {
        //     resposta = JSON.parse(response);
        //     toggleLoader();
        //     validateReturnAddCart(resposta, data);
        // });
        // console.log(responses);
    }

    function removeManyOrders() {
        var data = [];
        jQuery('.check-order').each(function(index) {
            if (jQuery(this).is(':checked') && jQuery(this).data('tracking') != "") {

                var tracking_id = jQuery(this).data('tracking');
                var data = {
                    action: 'wpmelhorenvio_ajax_removeTrackingAPI',
                    tracking:tracking_id,
                    security:'<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>'
                }

                jQuery.post(ajaxurl,data,function (response) {
                    resposta = JSON.parse(response);
                    if(resposta.succcess == true){

                        console.log(resposta);
                        // updateStatus(order_id, tracking_id, 'removed');
                        // jQuery('.mask').show();
                        // showMessageModal('Sucesso', 'Item removido com sucesso!');
                    }  
                });
            }
        });

        // console.log(data);
    }

    jQuery('.addManyToCart').click(function() {
        addManyOrders();
    });

    jQuery('.removeManyToCart').click(function() {
        removeManyOrders();
    });

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

    // Abre modal para editar documentos.
    function openModalDocs(index) {

        jQuery('.order_selected_id_modal').attr('value', index);
        var id     = jQuery('.order_id_index_' + index).val();
        jQuery('.order_selected_key_nf').val(jQuery('.documents_key_nf_' + index).val());
        jQuery('.order_selected_nf').val(jQuery('.documents_nf_' + index).val());
        jQuery('.order_selected_ie').val(jQuery('.documents_state_register_' + index).val());
        jQuery('.order_selected_cnpj').val(jQuery('.documents_document_' + index).val());
        jQuery('.order_selected_id_modal').val(id);

        jQuery('.modalDocs').show();
        jQuery('.mask').show();
    }

    // Enviar requesição para salvar os documentos do pedido
    function updateDocs() {
        var index  = jQuery('.order_selected_id_modal').val();
        data = {
            action:   'wpmelhorenvio_ajax_update_info_order',
            security: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>',
            id:      jQuery('.order_selected_id_modal').val(),
            key_nf:   jQuery('.order_selected_key_nf').val(),
            nf:       jQuery('.order_selected_nf').val(),
            cnpj:     jQuery('.order_selected_cnpj').val(),
            ie:       jQuery('.order_selected_ie').val()
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

    function closeModalDocs() {
        jQuery('.modalDocs').hide();
        jQuery('.mask').hide();
    }

function getAgency() {
    var agency = JSON.parse(jQuery('.agency').val());
    return agency['id'];    
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