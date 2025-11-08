import Chart from 'chart.js/auto';

// Fungsi untuk efek fade-in pada canvas
function fadeInCanvas(canvas) {
    canvas.style.opacity = 0;
    canvas.style.transition = 'opacity 0.8s ease-in-out';
    setTimeout(() => {
        canvas.style.opacity = 1;
    }, 50);
}

window.renderSlaChart = (labels, dataValues) => {
    const canvas = document.getElementById('slaChart');
    if (!canvas) return;

    // Efek fade-in tiap kali chart dirender ulang
    fadeInCanvas(canvas);

    const ctx = canvas.getContext('2d');

    // Hapus chart lama (jika ada)
    if (window.slaChartInstance) {
        window.slaChartInstance.destroy();
    }

    // Inisialisasi Chart.js
    window.slaChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.length > 0 ? labels : ['Tidak Ada Data'],
            datasets: [{
                label: 'Rata-rata Durasi (menit)',
                data: dataValues.length > 0 ? dataValues : [0],
                backgroundColor: ['#10b981', '#3b82f6', '#facc15', '#ef4444', '#a855f7'],
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart',
                onProgress: (animation) => {
                    // Sedikit efek bounce saat bar tumbuh
                    const chart = animation.chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        meta.data.forEach((bar) => {
                            bar.y += Math.sin(animation.currentStep / animation.numSteps * Math.PI) * 2;
                        });
                    });
                }
            },
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Durasi (menit)' },
                    ticks: { stepSize: 10 }
                }
            }
        }
    });
};
