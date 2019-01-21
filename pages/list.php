<div class="toggle-filters">
    <p class="filter-btn show-filters">Ver Filtros</p>
</div>

<div class="filters-section">
    <div id="filters-wrapper">
        <div class="direct-filters">
            <button id="last-7">Ultimos 7 Dias</button>
            <button id="last-15">Ultimos 15 Dias</button>
            <button id="last-30">Ultimos 30 Dias</button>
            <button>Ultimos 90 Dias</button>
        </div>

<!--        <div class="advanced-filters">-->
<!--            <div>-->
<!--                <label for="date">Desde: </label>-->
<!--                <input type="date" name="date" id="date-from">-->
<!--            </div>-->
<!--            <div>-->
<!--                <label for="date">Hasta: </label>-->
<!--                <input type="date" name="date" id="date-to">-->
<!--            </div>-->
<!--            <div>-->
<!--                <button>Filtrar</button>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</div>


<div class="show-list-wrapper">

    <div class="toggle-leyend">
        <p class="leyend-btn show-leyend">Ver Leyenda</p>
    </div>
    <div class="leyend">
        <div id="leyend-wrapper" class="user-visits">
            <div>
                <p><span class='visit-indicator visit'></span> Visita</p>
                <p><span class='visit-indicator visit-friend'></span> Visita <strong>Con Amigo</strong></p>
            </div>
            <div>
                <p><span class='visit-indicator meal'></span> Comida</p>
                <p><span class='visit-indicator meal-friend'></span> Comida <strong>Con Amigo</strong></p>
            </div>
            <div>
                <p><span class='visit-indicator fhe'></span> Noche de Hogar</p>
                <p><span class='visit-indicator fhe-friend'></span> Noche de Hogar <strong>Con Amigo</strong></p>
            </div>
        </div>
    </div>

</div>

<div class="show-list-wrapper">
    <table class="show-list-results sortable">
        <tr>
            <th class="left-column">Familias</th>
            <th>Visitas</th>
        </tr>

        <?php
        foreach( $trs as $tr) {

            echo '<tr>';
            echo "<td class='user-info' data-user-id='" . $tr['info']['id'] . "'>";
            echo "<span class='user-info-name'>" . $tr['info']['name'] . "</span>";
            echo "<span class='user-info-phone'>" . $tr['info']['phone'] . "</span>";
            echo "<span class='user-info-email'>" . $tr['info']['email'] . "</span>";
            echo '<div class="edit-buttons"><button class="edit-user-btn">Editar</button><button class="delete-user-btn">Borrar</button>';
            echo "</td>";
            echo "<td class='user-visits' sorttable_customkey='" . $tr['total'] . "'>";

            foreach( $tr['visits'] as $v) {
                if ( ! empty($v['date']) ) {
                    echo "<span class='visit-indicator tooltip " . $v['class'] . "' data-id='" . $v['pa_id'] . "'><div class='tooltiptext'><span class='activity-name'>" . $v['activity_name'] . "</span><span class='friend-name'>Amig@: <strong>" . $v['friend_name'] . "</strong></span><span class='date'>" . $v['date'] . "</span></div></span>";
                }
            }

            echo "</tr>";
        } ?>

    </table>
</div>


