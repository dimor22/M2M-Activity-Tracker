


(function($) {


    /* search box */
    $("#add-activity-page-search-box").keyup(function (e) {

        if ($(this).val().length > 2) {
            // only a-z and 0-9 and backspace
            if (event.keyCode >= 48 && event.keyCode <= 90 || event.keyCode == 8) {
                var that = $(this);
                setTimeout(function () {
                    var searchQuery = that.val();

                    $('.search-container ul').html('Buscando...');

                    $.ajax({
                        url : mmat_ajax.ajax_url,
                        type : 'post',
                        data : {
                            action : 'search_box_q',
                            search_q : searchQuery
                        },
                        success : function( response ) {

                            $('.search-container ul').html(response);

                            if (response.length < 1) {
                                $('.search-container ul').html('<p class="no-results">No se encontraron resultados :(</p>');
                            }
                        },
                        fail: function () {
                            $('.search-container ul').html('Ocurrio un problema en la busqueda. Intentelo mas tarde.');
                        }
                    });
                }, 500);
            }
        }
    })

    // show hide search box
    $("#people-search-btn").click(function () {
        $("#add-activity-page-search-results").show();
        $("#add-activity-page-search-box").focus();
    })

    $("#add-activity-page-search-results .exit-search").click(function () {
        $("#add-activity-page-search-results").hide();
        $("#add-activity-page-search-box").val('');
        $('.search-container ul').html(' <p>Escribe el nombre o telefono o email de la persona que guieras encontrar. Minimo 3 caracteres.</p>');
    })

    $('#add-activity-page-search-results').on("click", ".search-result-item", function () {
        $('#people-id').val($(this).data('user-id'));
        $("#add-activity-page-search-results").hide();
        $("#add-activity-page-search-box").val('');
        $('.search-container ul').html(' <p>Escribe el nombre o telefono o email de la persona que guieras encontrar. Minimo 3 caracteres.</p>');
        $(".selected-family").html($(this).data('user-name'));
    })

    // toggle display filters

    var filtersH = $('#filters-wrapper').outerHeight();
    $('#filters-wrapper').css('margin-top', '-' + filtersH + 'px');

    $('.toggle-filters').on("click", ".show-filters", function () {
        $('#filters-wrapper').css('margin-top', '15px');
        $(this).text('Cerrar Filtros');
        $(this).toggleClass('show-filters hide-filters');
    })

    $('.toggle-filters').on("click", ".hide-filters", function () {
        $('#filters-wrapper').css('margin-top', '-' + filtersH + 'px')
        $(this).text('Ver Filtros');
        $(this).toggleClass('show-filters hide-filters');
    })

    // toggle leyend

    var leyendH = $('#leyend-wrapper').outerHeight();
    $('#leyend-wrapper').css('margin-top', '-' + leyendH + 'px');

    $('.toggle-leyend').on("click", ".show-leyend", function () {
        $('#leyend-wrapper').css('margin-top', '15px');
        $(this).text('Cerrar Leyenda');
        $(this).toggleClass('show-leyend hide-leyend');
    })

    $('.toggle-leyend').on("click", ".hide-leyend", function () {
        $('#leyend-wrapper').css('margin-top', '-' + leyendH + 'px');
        $(this).text('Ver Leyenda');
        $(this).toggleClass('show-leyend hide-leyend');

    })

    $('.user-info').click(function () {
        $('.user-info.showing-user-buttons .edit-buttons').toggle();
        $('.user-info.showing-user-buttons').removeClass('showing-user-buttons');
        $(this).find('.edit-buttons').toggle();
        $(this).addClass('showing-user-buttons');
    })

    $('.edit-buttons button').click( function (e) {
        e.stopPropagation();
    })

    $('.edit-buttons .edit-user-btn').click( function () {
        var userId = $(this).parents('.user-info').data('user-id');
        var userFullName = $(this).parents('.user-info').find('.user-info-name').text();
        var userFname = userFullName.split(", ")[1];
        var userLname = userFullName.split(", ")[0];
        var userPhone = $(this).parents('.user-info').find('.user-info-phone').text();
        var userEmail = $(this).parents('.user-info').find('.user-info-email').text();

        $('#people-id').val(userId);
        $('#people-name').val(userFname);
        $('#people-lname').val(userLname);
        $('#people-phone').val(userPhone);
        $('#people-email').val(userEmail);

        $('#edit-user-modal').css('display', 'flex');
    })
    $('#edit-user-modal .close-modal').click(function (e) {
        e.preventDefault();
        $('#edit-user-modal').hide();
    })

    $('.edit-buttons .delete-user-btn').click( function () {
        var userId = $(this).parents('.user-info').data('user-id');
        var userFullName = $(this).parents('.user-info').find('.user-info-name').text();
        var userFname = userFullName.split(",")[1];
        var userLname = userFullName.split(",")[0];

        $('#modal-delete-user-id').val(userId);

        $('#user-name-delete-modal').text(userLname);

        $('#delete-user-modal').css('display', 'flex');
    })
    $('#delete-user-modal .close-modal').click(function () {
        $('#delete-user-modal').hide();
    })

    $('#delete-user-modal .delete-user-btn-modal').click( function () {
        console.log( $('#modal-delete-user-id').val() );

        var userId = $('#modal-delete-user-id').val();

        $('#deleting-status p').text('Borrando ...');

        $.ajax({
            url : mmat_ajax.ajax_url,
            type : 'post',
            data : {
                action : 'delete_user',
                user_id : userId
            },
            success : function( response ) {

                console.log( 'User deleted ' + response);

                $('.show-list-results td[data-user-id="' + userId + '"]').parent().remove();

                $('#delete-user-modal').hide();

                $('#deleting-status p').text('');

            },
            fail: function () {
                //$('.search-container ul').html('Ocurrio un problema en la busqueda. Intentelo mas tarde.');

                console.log( 'There was a problem deleting this user');
            }
        });

    })


})( jQuery );