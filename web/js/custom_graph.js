jQuery(document).ready(() => {
    const noticeRows = jQuery('#main td.graphable');

    const $modal = jQuery('#modal').modal({show: false});
    const $modalContent = $modal.find('.modal-content')
        .css('margin', '4% auto 0');

    const chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };

    const datasetBaseConfig = {
        fill: false,
        lineTension: 0,
    };

    function makeDatasetConfig(label, color, data) {
        return Object.assign({}, datasetBaseConfig, {
            label, data,
            backgroundColor: Chart.helpers.color(color).alpha(0.5).rgbString(),
            borderColor: color,
        });
    }

    const baseConfig = {
        type: 'line',
        options: {
            title: {
                text: 'Statistiques'
            },
            scales: {
                xAxes: [{
                    type: 'time',
                    time: {
                        parser: 'YYYY-MM-DD',
                        unit: 'day',
                        min: moment().subtract(3, 'months'),
                        max: moment()
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Date'
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'value'
                    }
                }]
            },
        }
    };

    function makeConfig(labels, datasets) {
        return Object.assign({}, baseConfig, {data: {labels, datasets}});
    }

    noticeRows.on('click', event => {
        const noticeId = jQuery(event.target).closest('tr').data('id');
        $modalContent.load(Routing.generate('notice_graph', {'id': noticeId}), () => {
            $modal.modal('show');

            const $canvas = $modalContent.find('#canvas');

            const labels_data = $canvas.data('labels');
            const display_data = $canvas.data('display');
            const unfold_data = $canvas.data('unfold');
            const click_data = $canvas.data('click');
            const like_data = $canvas.data('like');
            const dislike_data = $canvas.data('dislike');
            const dismiss_data = $canvas.data('dismiss');

            const config = makeConfig(labels_data, [
                makeDatasetConfig('Affichages', chartColors.grey, display_data),
                makeDatasetConfig('Dépliées', chartColors.green, unfold_data),
                makeDatasetConfig('Clics', chartColors.red, click_data),
                makeDatasetConfig('Likes', chartColors.blue, like_data),
                makeDatasetConfig('Dislikes', chartColors.purple, dislike_data),
                makeDatasetConfig('Ignorés', chartColors.orange, dismiss_data),
            ]);

            const ctx = $canvas.get(0).getContext('2d');
            window.noticeChart = new Chart(ctx, config);
        });
    });

});
