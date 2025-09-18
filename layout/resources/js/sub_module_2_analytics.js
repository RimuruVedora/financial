document.addEventListener('DOMContentLoaded', function() {
    // Function to fetch data from the backend
    async function fetchDashboardData() {
        try {
            const response = await fetch('php/sub_module_2_Analytics_ap.php');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            updateDashboard(data);
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
            document.getElementById('totalPayable').innerText = 'N/A';
            document.getElementById('totalRejected').innerText = 'N/A';
            document.getElementById('totalOverdue').innerText = 'N/A';
            document.getElementById('totalPayableSpend').innerText = 'N/A';
        }
    }

    // Function to update the dashboard with fetched data
    function updateDashboard(data) {
        // Update KPI tiles
        document.getElementById('totalPayable').innerText = data.totalPayable.toLocaleString();
        document.getElementById('totalRejected').innerText = data.totalRejected.toLocaleString();
        document.getElementById('totalOverdue').innerText = data.totalOverdue.toLocaleString();
        document.getElementById('totalPayableSpend').innerText = `₱${data.totalPayableSpend.toLocaleString()}`;

        // Line Chart: Invoice Processing & Late Payment Trends
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: data.invoiceTrendsData.labels,
                datasets: [{
                    label: 'Total Invoices',
                    data: data.invoiceTrendsData.total,
                    borderColor: 'rgb(59, 130, 246)', // Tailwind blue-500
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    fill: true,
                    tension: 0.1
                }, {
                    label: 'Late Invoices',
                    data: data.invoiceTrendsData.late,
                    borderColor: 'rgb(249, 115, 22)', // Tailwind orange-500
                    backgroundColor: 'rgba(249, 115, 22, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Pie Chart: Invoice Exception Reasons
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: data.exceptionReasonsData.labels,
                datasets: [{
                    data: data.exceptionReasonsData.data,
                    backgroundColor: ['#2563eb', '#facc15', '#ef4444'], // blue, yellow, red
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Bar Chart: Top Vendors by Spend
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: data.topVendorsData.labels,
                datasets: [{
                    label: 'Total Spend (₱)',
                    data: data.topVendorsData.data,
                    backgroundColor: '#1d4ed8', // blue-800
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Call the function to start the data fetching process
    fetchDashboardData();
});