<?php
    $nf     = null;
    $key_nf = null;

    if (isset($docsOrders[$id]['nf'])) {
        $nf = $docsOrders[$id]['nf'];
        if(strlen($nf)<=16){
            $nf = $nf;
        } else {
            $nf=substr($nf,0,16) . '...';
        }
    }

    if (isset($docsOrders[$id]['key_nf'])) {
        $key_nf = $docsOrders[$id]['key_nf'];
        if(strlen($key_nf)<=16){
            $key_nf = $key_nf;
        } else {
            $key_nf=substr($key_nf,0,16) . '...';
        }
    }
?>
<?php if (!$declaracao_jadlog) { ?>
    <strong>Chave-NF:</strong></br>
    <span class="spn-key-nf-<?php echo $index; ?>"><?php echo $key_nf; ?></span></br>
    <strong>NF:</strong></br>
    <span class="spn-nf-<?php echo $index; ?>"><?php echo $nf; ?></span>
<?php } ?>

<input type="hidden" class="documents_nf_<?php echo $index ?>" value="<?php echo $nf; ?>" />
<input type="hidden" class="documents_key_nf_<?php echo $index ?>" value="<?php echo $key_nf; ?>" />
<input type="hidden" class="documents_document_<?php echo $index ?>" value="<?php echo $documents['document']; ?>" />
<input type="hidden" class="documents_state_register_<?php echo $index ?>" value="<?php echo $documents['state_register']; ?>" />
