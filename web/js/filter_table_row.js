jQuery(document).ready(() => {
    const rows = jQuery.map(jQuery('#main table.table tbody tr:first-child td'), td => jQuery(td).data('label'));

    // build row selector

    const $modal = jQuery('<div id="modalFilterTableRow" class="modal"><div class="modal-content"></div></div>');
    $('body').append($modal);
    $modal.modal({show: false});

    const $modalContent = $modal.find('.modal-content')
        .css('margin', '4% auto 0').css('width', '30%');

    // build row selector
    const inputs = rows.map(label => '<label><input type="checkbox" value="'+ label +'" /> '+ label + '</label>').join('<br/>');
    $modalContent.append('<h4>Sélectionnez les colonnes à afficher :</h4>');
    $modalContent.append('<form>'+ inputs + '</form>');

    // add row selector modal control
    const $modalOpener = $('<div class="button-action"><a class="btn btn-secondary">Gérer les colonnes</a></div>');
    jQuery('.global-actions').prepend($modalOpener);

    // init checkboxes
    rows.map(label => {
        if(jQuery('#main table.table tbody tr:first-child td[data-label="'+ label +'"]').is(':visible')) {
            jQuery('#modalFilterTableRow input[value="'+ label +'"]').prop('checked', true);
        }
    })

    // actions
    $modalOpener.on('click', e => $modal.modal('show'));

    jQuery('#modalFilterTableRow input').on('change', e => {
        const $input = $(e.target);
        const label = $input.val();
        if($input.prop('checked')) {
            $('#main table.table thead tr th a:contains("'+ label +'")').parent('th').show();
            $('#main table.table thead tr th span:contains("'+ label +'")').parent('th').show();

            $('#main table.table tbody tr td[data-label="'+ label +'"]').show();
            return;
        }

        $('#main table.table thead tr th a:contains("'+ label +'")').parent('th').hide();
        $('#main table.table thead tr th span:contains("'+ label +'")').parent('th').hide();

        $('#main table.table tbody tr td[data-label="'+ label +'"]').hide();

    })
});
