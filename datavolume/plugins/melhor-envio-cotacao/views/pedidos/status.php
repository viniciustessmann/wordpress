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