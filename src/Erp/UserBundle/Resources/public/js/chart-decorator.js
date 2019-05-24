(function () {
    var updateElement = Chart.controllers.bar.prototype.updateElement;
    Chart.controllers.bar.prototype.updateElement = function (rectangle, index, reset) {
        updateElement.call(this, rectangle, index, reset);
        var me = this;
        var chart = me.chart;

        if (typeof chart.data.datasets[me.index].xType !== 'undefined') {
            rectangle._xType = chart.data.datasets[me.index].xType;
        }

        if (typeof chart.data.datasets[me.index].xValues !== 'undefined') {
            rectangle._xValue = chart.data.datasets[me.index].xValues[index];
        }
    };
})();