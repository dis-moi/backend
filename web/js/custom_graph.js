let $modal = $('#modal');

window.chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)'
};

$(document).on('click','td[data-label="Vues"], td[data-label="Cliqués"], td[data-label="Approuvés"], td[data-label="Ignorés"]',function () {
    let _id = $(this).closest('tr').data('id');
    $.ajax({url: Routing.generate('notice_graph',{'id':_id})}).done(function(data) {
        $modal.find('.modal-content').html(data);
        $modal.show();
        let $canvas = $('#canvas');
        let labels_data = $canvas.data('labels');
        let display_data = $canvas.data('display');
        let click_data = $canvas.data('click');
        let approve_data = $canvas.data('approve');
        let dismiss_data = $canvas.data('dismiss');

        let labels = [];
        $.each(labels_data, function( index, value ) {
            labels.push(moment(value+"T00:00:00").toDate());
        });

        let color = Chart.helpers.color;
        let config = {
            type: 'line',
            data: {
                labels: labels_data,
                datasets: [{
                    label: 'Vues',
                    backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.red,
                    fill: false,
                    data: display_data,
                },{
                    label: 'Cliqués',
                    backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.blue,
                    fill: false,
                    data: click_data,
                },{
                    label: 'Approuvés',
                    backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.green,
                    fill: false,
                    data: approve_data,
                },{
                    label: 'Ignorés',
                    backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.orange,
                    fill: false,
                    data: dismiss_data,
                }]
            },
            options: {
                title: {
                    text: 'Statistiques'
                },
                scales: {
                    xAxes: [{
                        type: 'time',
                        time: {
                            format: 'YYYY-MM-DD'
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

        let ctx = document.getElementById('canvas').getContext('2d');
        window.myLine = new Chart(ctx, config);

    });
});

/* MODAL */
$(document).on('click','.close',function () {
    $modal.hide();
});