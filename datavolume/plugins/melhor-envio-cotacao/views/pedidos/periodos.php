Período </br>  
<select class="filter-time">
    <option>Selecione uma opção</option>
    <option <?php if ($_GET['time'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
    <option <?php if ($_GET['time'] == 'day' || !isset($_GET['time'])) { echo 'selected'; } ?> value="day">Hoje</option>
    <option <?php if ($_GET['time'] == 'week') { echo 'selected'; } ?> value="week">Última semana</option>
    <option <?php if ($_GET['time'] == 'month') { echo 'selected'; } ?> value="month">Último mês</option>
    <option <?php if ($_GET['time'] == 'year') { echo 'selected'; } ?> value="year">último ano</option>
</select>