
<div id="add-activity-page">

    <div id="selected-famlily" class="left-orange-line">
        <p>Famlia Selectionada: <p>
        <p><strong class="selected-family"></strong></p>
        <p><a id="people-search-btn" href="#">Buscar Familia</a></p>
    </div>

    <form method='post' action='/wp-admin/admin-post.php' id="add-activity-form">
        <input name='action' type="hidden" value='add_activity'>

        <input id="people-id" type="hidden" name="people-id" value="">

        <div id="activity" class="left-orange-line">
            <p>Actividad:</p>
            <?php foreach( $activities as $activity) {
                echo '<div>';
                echo "<input type='radio' id='user-$activity->id' name='activity-id' value='$activity->id'>";
                echo "<label for='user-$activity->id'>$activity->name</label>";
                echo '</div>';
            } ?>
        </div>

        <div class="left-orange-line">
            <input type="checkbox" name="with-friend" id="with-friend">
            <label for="with-friend">Con Amig@</label>
        </div>

        <div class="left-orange-line">
            <label for="friend-name">Nombre de Amig@: </label>
            <input type="text" placeholder="Nombre de Amig@" name="friend-name" id="friend-name">
        </div>

        <div class="left-orange-line">
            <label for="date">Fecha: </label>
            <input type="date" name="date" id="date" required>
        </div>

        <div class="left-orange-line">
            <label for="time">Hora: </label>
            <input name="time" type="time" id="time" required>
        </div>

        <div>
            <input type="submit" value="Guardar">
        </div>

    </form>

</div>

<div id="add-activity-page-search-results">
    <div class="search-container">
        <h3 class="search-title">Buscador</h3>
        <button class="exit-search">Atras</button>
        <input type="text" id="add-activity-page-search-box" placeholder="ej. David Lopez">
        <ul>
            <p class="no-results">Escribe el nombre o telefono o email de la persona que guieras encontrar. Minimo 3 caracteres.</p>
        </ul>
    </div>

</div>