const getLabelFromId = (id) => jQuery(`#main table.table thead th.column-${id}`).text()

jQuery(document).ready(() => {
    const rows = jQuery.map(jQuery('#main table.table thead tr:first-child th'), th => {
        const classes = jQuery(th).attr('class');
        if (!classes) return undefined

        return classes.split(' ').filter(c => c.startsWith('column-')).map(c => c.replace('column-',''));
    });

    // build row selector
    const $modal = jQuery('<div id="modalFilterTableRow" class="modal"><div class="modal-content"></div></div>');
    $('body').append($modal);
    $modal.modal({show: false});

    const $modalContent = $modal.find('.modal-content')
        .css('margin', '4% auto 0').css('width', '30%');

    // build row selector
    const inputs = rows.map(id => '<label><input type="checkbox" value="'+ id +'" /> '+ getLabelFromId(id) + '</label>').join('<br/>');
    $modalContent.append('<h4>Sélectionnez les colonnes à afficher :</h4>');
    $modalContent.append('<form>'+ inputs + '</form>');

    // add row selector modal control
    const $modalOpener = $('<div class="button-action"><a class="btn btn-secondary">Gérer les colonnes</a></div>');
    jQuery('.global-actions').prepend($modalOpener);

    // init checkboxes
    rows.map(id => {
        if(jQuery(`#main table.table thead tr:first-child th.column-${id}`).is(':visible')) {
            jQuery('#modalFilterTableRow input[value="'+ id +'"]').prop('checked', true);
        }
    })

    // actions
    $modalOpener.on('click', e => $modal.modal('show'));

    jQuery('#modalFilterTableRow input').on('change', e => {
        const $input = $(e.target);
        const id = $input.val();

        const columnHead = $(`#main table.table thead tr th.column-${id}`);
        const columnBody = $(`#main table.table tbody tr td.column-${id}`);

        if($input.prop('checked')) {
            columnHead.show();
            columnBody.show();
        } else {
            columnHead.hide();
            columnBody.hide();
        }
    })
});
