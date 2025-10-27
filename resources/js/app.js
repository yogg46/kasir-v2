// import './bootstrap';
import './chart.umd.min.js'; // import Chart.js versi lokal

// Chart.js otomatis terpasang ke window.Chart (karena UMD build)
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('myChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['A', 'B', 'C', 'D'],
                datasets: [{
                    label: 'Contoh Data',
                    data: [12, 19, 3, 5],
                    borderWidth: 1,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                }]
            },
            options: {
                responsive: true,
            }
        });
    }
});
