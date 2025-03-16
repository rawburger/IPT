function cancelDelete() {
    window.location.href = 'dept_dashboard.php';
}

function confirmDelete() {
    event.preventDefault();
    const deptId = document.getElementById('deptid').value;

    Swal.fire({
        title: 'Confirmation',
        text: "Do you want to delete this department?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('dept_delete.php', { deptid: deptId })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire(
                            'Deleted!',
                            'Department has been deleted.',
                            'success'
                        ).then(() => {
                            window.location.href = 'dept_dashboard.php';
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to delete the department: ' + response.data.message,
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'Error deleting the department: ' + error,
                        'error'
                    );
                });
        }
    });
}