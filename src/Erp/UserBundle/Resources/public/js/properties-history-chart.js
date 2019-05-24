$(document).ready(function () {
    var ctx = $('#properties-history-chart');
    var labels = ctx.data('labels'),
            availableProperties = ctx.data('available-properties'),
            rentedProperties = ctx.data('rented-properties')
            ;
    var total = parseInt(availableProperties) + parseInt(rentedProperties);
    var rate = (total > 0) ? (100 * Math.round(100 * rentedProperties / total) / 100) : 0;
    var chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                    data: [availableProperties, rentedProperties],
                    backgroundColor: ["#edecfe", '#a4a1fb'],
                    borderWidth: [1, 1]
                }]
        },
        options: {
            responsive: true,
            cutoutPercentage: 80,
            title: {
                display: false,
                position: "top",
                text: rate + "% Rented",
                fontSize: 31,
                fontColor: "#4ad991"
            },
            legend: {
                display: false
            }
        },
        plugins: [{
                id: 'plugin-write-into-center',
                afterDraw: function (chart, option) {
                    chart.ctx.fillStyle = '#4ad991';
                    chart.ctx.textBaseline = 'middle';
                    chart.ctx.textAlign = 'center';
                    chart.ctx.font = '31px Montserrat, Arial, sans-serif';
                    chart.ctx.width = '282px';
                    chart.ctx.height = '282px';
                    chart.ctx.fillText(rate + '% Rented', chart.canvas.width / 2, chart.canvas.height / 2);
                }
            }]
    });
});