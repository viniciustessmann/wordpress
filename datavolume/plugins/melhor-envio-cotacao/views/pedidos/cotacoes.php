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