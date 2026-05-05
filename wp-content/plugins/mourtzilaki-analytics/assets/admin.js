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
    var grad = ctx.createLinearGradient(0, 0, 0, 320);
    grad.addColorStop(0, 'rgba(197,165,114,0.35)');
    grad.addColorStop(1, 'rgba(197,165,114,0)');

    Chart.defaults.color = 'rgba(245,236,217,0.55)';
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size = 11;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Προβολές',
                data: values,
                borderColor: '#c5a572',
                borderWidth: 2,
                fill: true,
                backgroundColor: grad,
                tension: 0.35,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#c5a572',
                pointHoverBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(13,12,10,0.95)',
                    borderColor: 'rgba(197,165,114,0.35)',
                    borderWidth: 1,
                    titleColor: '#fff',
                    bodyColor: '#c5a572',
                    titleFont: { weight: '500', size: 12 },
                    bodyFont: { weight: '600', size: 14 },
                    padding: 12,
                    cornerRadius: 6,
                    displayColors: false,
                    callbacks: {
                        title: function (items) { return items[0].label; },
                        label: function (item) { return item.parsed.y + ' προβολές'; }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { maxRotation: 0, autoSkipPadding: 18, color: 'rgba(245,236,217,0.4)' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                    border: { display: false },
                    ticks: {
                        precision: 0,
                        color: 'rgba(245,236,217,0.4)',
                        padding: 8
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });

    // Animated counters
    var counters = document.querySelectorAll('.mz-an-kpi-val');
    counters.forEach(function (el) {
        var raw = el.textContent.replace(/[^\d.]/g, '');
        if (!raw) { return; }
        var target = parseFloat(raw);
        if (isNaN(target) || target === 0) { return; }
        var hasPercent = el.textContent.indexOf('%') !== -1;
        var duration = 800;
        var start = performance.now();
        var ease = function (t) { return 1 - Math.pow(1 - t, 3); };
        function step(now) {
            var progress = Math.min(1, (now - start) / duration);
            var current = target * ease(progress);
            var formatted;
            if (hasPercent) {
                formatted = current.toFixed(1).replace('.', ',') + '%';
            } else {
                formatted = Math.round(current).toLocaleString('el-GR');
            }
            el.textContent = formatted;
            if (progress < 1) { requestAnimationFrame(step); }
        }
        requestAnimationFrame(step);
    });
})();
