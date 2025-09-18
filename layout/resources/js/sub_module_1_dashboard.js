document.addEventListener('DOMContentLoaded', function() {
    fetch('php/sub_module_1_dashboard_backend.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Check if the data has an error
            if (data.error) {
                console.error('Backend Error:', data.error);
                return;
            }

            // Update the dashboard elements with the fetched data
            // You will need to make sure the IDs match your HTML elements
            document.getElementById('total-receivable').textContent = data.total_receivable.toFixed(2);
            document.getElementById('total-receivable-request').textContent = data.total_receivable_requests;
            document.getElementById('total-debtors').textContent = data.total_debtor_persons;
            document.getElementById('collection-success-rate').textContent = data.collection_success_rate.toFixed(2) + '%';
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
});