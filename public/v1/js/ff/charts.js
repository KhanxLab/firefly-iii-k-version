/*
 * charts.js
 * Copyright (c) 2019 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
var allCharts = {};


/*
 Make some colours:
 */
var colourSet = [
    [30, 101, 129], // accent #1E6581
    [232, 163, 61], // amber #E8A33D
    [15, 118, 110], // teal #0F766E
    [194, 65, 107], // rose #C2416B
    [79, 70, 229], // indigo #4F46E5
    [21, 128, 61], // green #15803D
    [194, 65, 12], // orange #C2410C
    [100, 116, 139], // slate #64748B
    [14, 116, 144], // cyan #0E7490
    [126, 34, 206], // purple #7E22CE
    [101, 163, 13], // lime #65A30D
    [180, 83, 9], // brown #B45309
    [37, 99, 235], // blue #2563EB
    [219, 39, 119], // pink #DB2777
    [71, 85, 105], // dark slate #475569
    [5, 150, 105], // emerald #059669
    [161, 98, 7] // ochre #A16207
];

var fillColors = [];

for (var i = 0; i < colourSet.length; i++) {
    fillColors.push("rgba(" + colourSet[i][0] + ", " + colourSet[i][1] + ", " + colourSet[i][2] + ", 0.5)");
}

Chart.defaults.global.legend.display = false;
Chart.defaults.global.animation.duration = 0;
Chart.defaults.global.responsive = true;
Chart.defaults.global.maintainAspectRatio = false;

// Typography and grid colors that match the application theme.
(function () {
    "use strict";
    var dark = 'dark' === document.documentElement.getAttribute('data-bs-theme');
    Chart.defaults.global.defaultFontFamily = '"Inter Variable", Inter, system-ui, sans-serif';
    Chart.defaults.global.defaultFontColor = dark ? '#9aa4b2' : '#5b6474';
    Chart.defaults.scale.gridLines.color = dark ? 'rgba(255, 255, 255, 0.06)' : '#eef0f3';
    Chart.defaults.scale.gridLines.zeroLineColor = dark ? 'rgba(255, 255, 255, 0.12)' : '#e4e7ec';
    Chart.defaults.global.elements.rectangle.borderWidth = 0;
    Chart.defaults.global.elements.line.borderWidth = 2;
    Chart.defaults.global.elements.point.radius = 0;
    Chart.defaults.global.elements.point.hoverRadius = 4;
})();

/**
 *
 * @param data
 * @returns {{}}
 */
function colorizeData(data) {
    var newData = {};
    newData.datasets = [];

    for (var loop = 0; loop < data.count; loop++) {
        newData.labels = data.labels;
        var dataset = data.datasets[loop];
        dataset.fill = false;
        dataset.backgroundColor = dataset.borderColor = fillColors[loop];
        newData.datasets.push(dataset);
    }
    return newData;
}

/**
 * Function to draw a line chart:
 * @param URL
 * @param container
 */
function lineChart(URL, container) {
    "use strict";

    var colorData = true;
    var options = $.extend(true, {}, defaultChartOptions);
    var chartType = 'line';

    drawAChart(URL, container, chartType, options, colorData);
}

/**
 * Function to draw a line chart that doesn't start at ZERO.
 * @param URL
 * @param container
 */
function lineNoStartZeroChart(URL, container) {
    "use strict";

    var colorData = true;
    var options = $.extend(true, {}, defaultChartOptions);
    var chartType = 'line';
    options.scales.yAxes[0].ticks.beginAtZero = false;

    drawAChart(URL, container, chartType, options, colorData);
}

/**
 * Overrules the currency the line chart is drawn in.
 *
 * @param URL
 * @param container
 */
function otherCurrencyLineChart(URL, container, currencySymbol) {
    "use strict";

    var colorData = true;

    var newOpts = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        // break ticks when too long.
                        callback: function (value, index, values) {
                            return formatLabel(value, 20);
                        }
                    }
                }
            ],
            yAxes: [{
                display: true,
                //hello: 'fresh',
                ticks: {
                    callback: function (tickValue) {
                        "use strict";
                        // use first symbol or null:
                        return accounting.formatMoney(tickValue);

                    },
                    beginAtZero: true
                }
            }]
        },
    };

    //var options = $.extend(true, newOpts, defaultChartOptions);
    var options = $.extend(true, defaultChartOptions, newOpts);

    // console.log(options);
    var chartType = 'line';

    drawAChart(URL, container, chartType, options, colorData);
}

/**
 * Function to draw a chart with double Y Axes and stacked columns.
 *
 * @param URL
 * @param container
 */
function doubleYChart(URL, container) {
    "use strict";

    var colorData = true;
    var options = $.extend(true, {}, defaultChartOptions);
    options.scales.yAxes = [
        // y axis 0:
        {
            display: true,
            ticks: {
                callback: function (tickValue) {
                    "use strict";
                    return accounting.formatMoney(tickValue);

                },
                beginAtZero: true
            },
            position: "left",
            "id": "y-axis-0"
        },
        // and y axis 1:
        {
            display: true,
            ticks: {
                callback: function (tickValue) {
                    "use strict";
                    return accounting.formatMoney(tickValue);

                },
                beginAtZero: true
            },
            position: "right",
            "id": "y-axis-1"
        }

    ];
    options.stacked = true;
    options.scales.xAxes[0].stacked = true;

    var chartType = 'bar';

    drawAChart(URL, container, chartType, options, colorData);
}

/**
 * Function to draw a chart with double Y Axes and non stacked columns.
 *
 * @param URL
 * @param container
 */
function doubleYNonStackedChart(URL, container) {
    "use strict";

    var colorData = true;
    var options = $.extend(true, {}, defaultChartOptions);
    options.scales.yAxes = [
        // y axis 0:
        {
            display: true,
            ticks: {
                callback: function (tickValue) {
                    "use strict";
                    return accounting.formatMoney(tickValue);

                },
                beginAtZero: true
            },
            position: "left",
            "id": "y-axis-0"
        },
        // and y axis 1:
        {
            display: true,
            ticks: {
                callback: function (tickValue) {
                    "use strict";
                    return accounting.formatMoney(tickValue);

                },
                beginAtZero: true
            },
            position: "right",
            "id": "y-axis-1"
        }

    ];
    var chartType = 'bar';

    drawAChart(URL, container, chartType, options, colorData);
}


/**
 *
 * @param URL
 * @param container
 */
function columnChart(URL, container) {
    "use strict";
    var colorData = true;
    var options = $.extend(true, {}, defaultChartOptions);
    var chartType = 'bar';

    drawAChart(URL, container, chartType, options, colorData);
}


/**
 *
 * @param URL
 * @param container
 */
function columnChartCustomColours(URL, container) {
    "use strict";
    var colorData = false;
    var options = $.extend(true, {}, defaultChartOptions);
    var chartType = 'bar';

    drawAChart(URL, container, chartType, options, colorData);

}

/**
 *
 * @param URL
 * @param container
 */
function stackedColumnChart(URL, container) {
    "use strict";
    console.log('stackedColumnChart(' + URL + ', ' + container + ')');

    var colorData = true;
    var options = $.extend(true, {}, defaultChartOptions);

    options.stacked = true;
    options.scales.xAxes[0].stacked = true;
    options.scales.yAxes[0].stacked = true;

    var chartType = 'bar';

    drawAChart(URL, container, chartType, options, colorData);
}

/**
 *
 * @param URL
 * @param container
 */
function pieChart(URL, container) {
    "use strict";

    var colorData = false;
    var options = $.extend(true, {}, defaultPieOptions);
    var chartType = 'pie';

    drawAChart(URL, container, chartType, options, colorData);

}

/**
 *
 * @param URL
 * @param container
 */
function multiCurrencyPieChart(URL, container) {
    "use strict";

    var colorData = false;
    var options = $.extend(true, {}, pieOptionsWithCurrency);
    var chartType = 'pie';

    drawAChart(URL, container, chartType, options, colorData);

}

/**
 * @param URL
 * @param container
 * @param chartType
 * @param options
 * @param colorData
 * @param today
 */
function drawAChart(URL, container, chartType, options, colorData) {
    console.log('drawAChart(' + URL + ', ' + container + ')');
    var containerObj = document.getElementById(container);
    if (null === containerObj) {
        console.log('Return because NULL');
        return;
    }
    if (containerObj.length === 0) {
        console.log('Return because 0');
        return;
    }
    window.axios.get(URL).then(function (response) {
        //console.log('GET drawAChart('+URL+', '+container+')');
        containerObj.classList.remove('general-chart-error');
        var data = response.data;
        // if result is empty array, or the labels array is empty, show error.
        // console.log(URL);
        // console.log(data.length);
        // console.log(typeof data.labels);
        // console.log(data.labels.length);
        if (
            // is undefined
            typeof data === 'undefined' ||
            // is empty
            0 === data.length ||
            // isn't empty but contains no labels
            (typeof data === 'object' && typeof data.labels === 'object' && 0 === data.labels.length)
        ) {
            // console.log('NODATA drawAChart('+URL+', '+container+')');
            // console.log(data);
            // remove the chart container + parent
            var holder = document.getElementById(container).parentNode.parentNode;
            if (holder.classList.contains('card') || holder.classList.contains('card-body')) {
                // console.log('found holder');
                // find box-body:
                var boxBody;
                if (!holder.classList.contains('card-body')) {
                    // console.log('Look for card body', holder.querySelector('.card-body').length);
                    boxBody = holder.querySelector('.card-body');
                } else {
                    boxBody = holder;
                }
                // console.log('found box body', boxBody);
                // boxBody.innerHtml = '<p><em>'+noDataForChart+'</em></p>';
                boxBody.innerHTML = '<p><em>' + noDataForChart + '</em></p>';
                //boxBody.empty().append($('<p>').append($('<em>').text(noDataForChart)));
            }
            // console.log('return');
            return;
        }

        if (colorData) {
            data = colorizeData(data);
        }

        if (allCharts.hasOwnProperty(container)) {
            allCharts[container].data.datasets = data.datasets;
            allCharts[container].data.labels = data.labels;
            allCharts[container].update();
        } else {
            // new chart!
            var ctx = document.getElementById(container).getContext("2d");
            var chartOpts = {
                type: chartType,
                data: data,
                options: options,
                lineAtIndex: [],
                annotation: {},
            };
            if (typeof drawVerticalLine !== 'undefined') {
                if (drawVerticalLine !== '') {
                    // draw line using annotation plugin.
                    chartOpts.options.annotation = {
                        annotations: [{
                            type: 'line',
                            id: 'a-line-1',
                            mode: 'vertical',
                            scaleID: 'x-axis-0',
                            value: drawVerticalLine,
                            borderColor: lineColor,
                            borderWidth: 1,
                            label: {
                                backgroundColor: 'rgba(0,0,0,0)',
                                fontFamily: "sans-serif",
                                fontSize: 12,
                                fontColor: lineTextColor,
                                position: "right",
                                xAdjust: -20,
                                yAdjust: -125,
                                enabled: true,
                                content: todayText
                            }
                        }]
                    };
                }
            }
            allCharts[container] = new Chart(ctx, chartOpts);
        }

    }).catch(function (reason) {
        // console.error('Add error because error with chart.', reason);
        document.getElementById(container).classList.add('general-chart-error');
    });
}
