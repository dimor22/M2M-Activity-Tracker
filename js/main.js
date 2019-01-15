


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
        $('#leyend-wrapper').css('margin-top', '-' + leyendH + 'px')
        $(this).text('Ver Leyenda');
        $(this).toggleClass('show-leyend hide-leyend');

    })


})( jQuery );