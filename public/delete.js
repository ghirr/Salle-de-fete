
    // Get all the delete buttons
    const deleteButtons = document.querySelectorAll('.delete-salle');

    // Add a click event listener to each delete button
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const url = this.dataset.url;

            // Send a DELETE request using Fetch API
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Handle the response data
                    if (data.success) {
                        // Refresh the page or perform any other action
                        location.reload();
                    } else {
                        console.error('Delete request failed:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Delete request failed:', error);
                });
        });
    });
