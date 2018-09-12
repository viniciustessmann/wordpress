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

<!-- Documentos -->
<div class="modal modalDocs" style="height:auto; display:none">
    <h1>Inserir os dados</h1>
    <div class="wpme_wrapper_center">
        <form  class="update-order">
            <input type="hidden" name="id" value="" class="order_selected_id_modal" />
            <div class="form">
                    <fieldset>
                        <label>Chave da nota fiscal</label>
                        <input type="text" name="key-nf" class="order_selected_key_nf"  />
                    </fieldset>
                    <fieldset>
                        <label>Número da nota fiscal</label></br>
                        <input type="text" name="nf" class="order_selected_nf" />
                    </fieldset>
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