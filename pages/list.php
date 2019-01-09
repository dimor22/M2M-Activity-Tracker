<div class="toggle-filters">
    <p class="filter-btn show-filters">Ver Filtros</p>
</div>

<div class="filters-section">
    <div id="filters-wrapper">
        <div class="direct-filters">
            <button>Ultimos 7 Dias</button>
            <button>Ultimos 15 Dias</button>
            <button>Ultimos 30 Dias</button>
        </div>

        <div class="advanced-filters">
            <div>
                <label for="date">Desde: </label>
                <input type="date" name="date" id="date-from">
            </div>
            <div>
                <label for="date">Hasta: </label>
                <input type="date" name="date" id="date-to">
            </div>
            <div>
                <button>Filtrar</button>
            </div>
        </div>
    </div>
</div>


<ul class="show-list-wrapper">

    <div class="toggle-leyend">
        <p class="leyend-btn show-leyend">Ver Leyenda</p>
    </div>
    <div class="leyend">
        <div id="leyend-wrapper">
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


    <div class="show-list-results">

        <?php

        $user_id = 0;
        $i = 0;
        $date = '';

        foreach( $interactions as $int) {
            $i++;

            if ($i > 1 && $user_id != $int->id) {
                echo "</div></li>";
            }

            $class = get_image_class($int->with_friend, $int->activity_id);

            if ( ! is_null($int->date) ) {
                $date = gmdate("j/n/Y, g:i a", $int->date);
            }

            if ($int->id != $user_id) {
                echo '<li>';
                echo "<div class='user-info'>";
                echo "<span class='user-info-name'>$int->username</span>";
                echo "<span class='user-info-phone'>$int->phone</span>";
                echo "<span class='user-info-email'>$int->email</span>";
                echo "</div>";
                echo "<div class='user-visits'>";
                if ( ! is_null($class) ) {
                    echo "<span class='visit-indicator tooltip $class' data-id='$int->pa_id'><div class='tooltiptext'><span class='date'>$date</span><span class='friend-name'>Amig@: <strong>$int->friend_name</strong></span></div></span>";
                }
            } else {
                if ( ! is_null($class) ) {
                    echo "<span class='visit-indicator tooltip $class' data-id='$int->pa_id'><div class='tooltiptext'><span class='date'>$date</span><span class='friend-name'>Amig@: <strong>$int->friend_name</strong></span></div></span>";
                }
            }

            $user_id = $int->id;

        } ?>

    </div>

</ul>


