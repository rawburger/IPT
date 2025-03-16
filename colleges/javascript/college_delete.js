function cancelDelete() {
    window.location.href = 'college_dashboard.php';
}

function confirmDelete(event) {
    event.preventDefault();
    const collegeId = document.getElementById('college_id').value;

    Swal.fire({
        title: 'Confirmation',
        text: 'Do you want to delete this college?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('college_delete.php', { college_id: collegeId })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire(
                            'Deleted!',
                            'College has been deleted.',
                            'success'
                        ).then(() => {
                            window.location.href = 'college_dashboard.php';
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to delete the college: ' + (response.data.message || 'Unknown error'),
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'Error deleting the college: ' + error,
                        'error'
                    );
                });
        }
    });
}
