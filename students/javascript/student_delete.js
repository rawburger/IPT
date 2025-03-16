function cancelDelete() {
    window.location.href = 'student_dashboard.php';
}

function confirmDelete(event) {
    event.preventDefault();
    const studid = document.getElementById('studid').value;

    Swal.fire({
        title: 'Confirmation',
        text: "Do you want to delete this student?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('student_delete.php', { studid: studid })
                .then(function (response) {
                    if (response.data.success) {
                        Swal.fire(
                            'Deleted!',
                            'The student has been deleted.',
                            'success'
                        ).then(() => {
                            window.location.href = 'student_dashboard.php';
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.data.message,
                            'error'
                        );
                    }
                })
                .catch(function (error) {
                    Swal.fire(
                        'Error!',
                        'There was an error deleting the student.',
                        'error'
                    );
                    console.error("There was an error deleting the student:", error);
                });
        }
    });
}
