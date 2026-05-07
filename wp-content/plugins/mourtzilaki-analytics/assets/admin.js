(function () {
    'use strict';

    if (typeof Chart === 'undefined' || !window.MZ_ANALYTICS) { return; }

    var canvas = document.getElementById('mz-views-chart');
    if (!canvas) { return; }

    var labels = MZ_ANALYTICS.series_labels.map(function (d) {
        var parts = d.split('-');
        return parts[2] + '/' + parts[1];
    });
    var values = MZ_ANALYTICS.series_values.map(function (v) { return parseInt(v, 10) || 0; });

    var ctx = canvas.getContext('2d');
    var grad = ctx.createLinearGradient(0, 0, 0, 240);
    grad.addColorStop(0, 'rgba(180,138,58,0.18)');
    grad.addColorStop(1, 'rgba(180,138,58,0)');

    Chart.defaults.color = '#6b7280';
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    Chart.defaults.font.size = 11;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Προβολές',
                data: values,
                borderColor: '#b48a3a',
                borderWidth: 2,
                fill: true,
                backgroundColor: grad,
                tension: 0.3,
                pointRadius: 0,
                pointHoverRadius: 4,
                pointHoverBackgroundColor: '#b48a3a',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f1a14',
                    borderColor: '#1f1a14',
                    borderWidth: 0,
                    titleColor: '#fff',
                    bodyColor: '#f6f3ec',
                    padding: 10,
                    cornerRadius: 6,
                    displayColors: false,
                    callbacks: {
                        title: function (items) { return items[0].label; },
                        label:  function (item)  { return item.parsed.y + ' προβολές'; }
                    }
                }
            },
            scales: {
                x: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks:  { maxRotation: 0, autoSkipPadding: 18 }
                },
                y: {
                    beginAtZero: true,
                    grid:   { color: '#f0f1f3' },
                    border: { display: false },
                    ticks:  { precision: 0, padding: 6 }
                }
            },
            animation: { duration: 400, easing: 'easeOutQuart' }
        }
    });
})();
