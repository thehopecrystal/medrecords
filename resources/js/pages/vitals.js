import { Chart, CategoryScale, LinearScale, PointElement, LineElement, Filler, Legend } from 'chart.js';

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Legend);

document.addEventListener('DOMContentLoaded', function () {
  const dataEl = document.getElementById('bp-chart-data');
  const canvas  = document.getElementById('bpChart');
  if (!dataEl || !canvas) return;

  const chartData = JSON.parse(dataEl.textContent);

  new Chart(canvas, {
    type: 'line',
    data: {
      labels: chartData.map(d => d.label),
      datasets: [
        {
          label: 'Systolic',
          data: chartData.map(d => d.sys),
          borderColor: '#B23A48',
          backgroundColor: 'rgba(178,58,72,0.08)',
          borderWidth: 2,
          pointRadius: 2,
          tension: 0.3,
          fill: true,
        },
        {
          label: 'Diastolic',
          data: chartData.map(d => d.dia),
          borderColor: '#1E3A5F',
          backgroundColor: 'rgba(30,58,95,0.05)',
          borderWidth: 2,
          pointRadius: 2,
          tension: 0.3,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { boxWidth: 10, font: { size: 11 } },
        },
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { maxTicksLimit: 8, font: { size: 10 } },
        },
        y: {
          beginAtZero: false,
          min: 40,
          grid: { color: '#F1F5F9' },
          ticks: { font: { size: 11 } },
        },
      },
    },
  });
});
