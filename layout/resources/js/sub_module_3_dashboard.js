document.addEventListener('DOMContentLoaded', () => {
    // Dummy data - in a real-world scenario, you would fetch this from an API
    const dashboardData = {
        totalRevenue: 125000,
        incomingRevenue: 15000,
        totalDisbursed: 78000,
        totalDepartments: 12
    };

    // Helper function to format currency
    const formatCurrency = (amount) => {
        // Use the Intl.NumberFormat object for locale-sensitive currency formatting
        // 'en-PH' specifies the locale for English as used in the Philippines
        // The currency code 'PHP' stands for Philippine Peso
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(amount);
    };

    // Update the dashboard cards with the data
    document.getElementById('total-revenue').innerText = formatCurrency(dashboardData.totalRevenue);
    document.getElementById('incoming-revenue').innerText = formatCurrency(dashboardData.incomingRevenue);
    document.getElementById('total-disbursed').innerText = formatCurrency(dashboardData.totalDisbursed);
    document.getElementById('total-departments').innerText = dashboardData.totalDepartments.toLocaleString();
});