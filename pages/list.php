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

            echo "<tr data-status='"  . $tr['info']['status'] . "' >";
            echo "<td class='user-info status-" . $tr['info']['status'] . "' data-user-id='" . $tr['info']['id'] . "'>";
            echo "<span class='user-info-name'>" . $tr['info']['lname'] . ", " . $tr['info']['name'] . "</span>";
            echo "<span class='user-info-phone'>" . $tr['info']['phone'] . "</span>";
            echo "<span class='user-info-email'>" . $tr['info']['email'] . "</span>";
            echo '<div class="edit-buttons"><button class="edit-user-btn"><span class="dashicons dashicons-edit"></span></button><button class="delete-user-btn bg-blood"><span class="dashicons dashicons-no"></span></button>';
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


<div id="edit-user-modal" class="modal">
    <div class="modal-content">
        <form method='post' action='/wp-admin/admin-post.php' id="add-people-form">
            <input name='action' type="hidden" value='update_people'>
            <input name='user-id' id="people-id" type="hidden" value=''>
            <div class="left-orange-line">
                <label for="people-name">Nombre: </label>
                <input id="people-name" type="text" name="name" required>
            </div>

            <div class="left-orange-line">
                <label for="people-lname">Apedillos: </label>
                <input id="people-lname" type="text" name="lname" required>
            </div>

            <div class="left-orange-line">
                <label for="people-phone">Telefono: </label>
                <input id="people-phone" type="text" name="phone">
            </div>

            <div class="left-orange-line">
                <label for="people-email">Email: </label>
                <input id="people-email" type="text" name="email">
            </div>

            <div id="status" class="left-orange-line">
                <p>Status:</p>
                <?php foreach( $status as $s) {
                    echo '<div>';
                    echo "<input type='radio' id='status-$s->id' name='status' value='$s->id'>";
                    echo "<label for='status-$s->id'>$s->name</label>";
                    echo '</div>';
                } ?>
            </div>

            <button class="close-modal">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
            </button>

            <button type="submit" class="bg-green">
                <span class="dashicons dashicons-yes"></span>
            </button>
        </form>
        <div id="updating-status" class="ajax-response">
            <p></p>
        </div>
    </div>
</div>

<div id="delete-user-modal" class="modal">
    <div class="modal-content">
        <span class="dashicons dashicons-warning"></span>
        <p>La familia <strong id="user-name-delete-modal"></strong> y todas sus citas seran borradas.</p>
        <p>Seguro que queires borrar esta familia?</p>
        <button class="close-modal bg-blood">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
        </button>

        <button class="delete-user-btn-modal bg-green">
            <input type="hidden" id="modal-delete-user-id">
            <span class="dashicons dashicons-yes"></span>
        </button>
        <div id="deleting-status" class="ajax-response">
          <p></p>
        </div>
    </div>
</div>