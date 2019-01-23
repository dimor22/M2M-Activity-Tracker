<form method='post' action='/wp-admin/admin-post.php' id="add-people-form">
    <input name='action' type="hidden" value='add_people'>
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

    <input type="submit" value="Guardar">
</form>