const getContributorFormEntityId = () => parseInt($('form#edit-contributor-form').data('entity-id'))

const getRawPinsSelect = () => $('#edit-contributor-form #contributor_pinnedNotices_autocomplete')

const getRenderedPinsSelect = () => getRawPinsSelect()
    .parent()
    .find('ul.select2-selection__rendered')

const moveElementToEndOfParent = (element) => {
    const parent = element.parent()
    element.detach()
    parent.append(element)
}

const getPinIdFromPinLi = (pinLi) => pinLi.text().match(/\(id:(?<pinId>\d+)\)/).groups.pinId

const orderPinnedNotices = () => {
    getRenderedPinsSelect()
        .children('li[title]')
        .each(function () {
            moveElementToEndOfParent(
                getRawPinsSelect().children(`option[value="${getPinIdFromPinLi($(this))}"]`)
            )
        })
}

const reinstallPinnedNoticesSelect2 = (pinnedNoticesSelect) => {
    const contributorId = getContributorFormEntityId();
    const url = pinnedNoticesSelect.data('easyadmin-autocomplete-url')
        + (contributorId ? `&contributor_id=${contributorId}` : '')

    // From vendor/easycorp/easyadmin-bundle/assets/js/app.js
    pinnedNoticesSelect.select2({
        theme: 'bootstrap',
        ajax: {
            url,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 'query': params.term, 'page': params.page };
            },
            // to indicate that infinite scrolling can be used
            processResults: function (data, params) {
                return {
                    results: data.results,
                    pagination: {
                        more: data.has_next_page
                    }
                };
            },
            cache: true
        },
        placeholder: '',
        allowClear: true,
        minimumInputLength: 1,
        maximumSelectionLength: 5
    });

}

let pinSortingInitialized = false
const initPinnedNoticesField = () => {
    const pinnedNoticesSelect = getRawPinsSelect();
    if (!pinSortingInitialized) {
        reinstallPinnedNoticesSelect2(pinnedNoticesSelect)

        const renderedSelect = getRenderedPinsSelect()

        if (renderedSelect.length) {
            renderedSelect.sortable({
                containment: 'parent',
                update: orderPinnedNotices
            })

            pinSortingInitialized = true
        }
    }
}

jQuery(() => {
    new MutationObserver(() => {
        $('[data-widget=select2]:not(.select2-hidden-accessible)').select2()

        initPinnedNoticesField();
    }).observe(document, {
        childList: true,
        subtree: true
    })
})
