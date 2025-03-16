document.addEventListener('DOMContentLoaded', function() {
    const progId = document.getElementById('progid').value;

    if (progId) {
        fetchProgram(progId);
    }
});

function cancelDelete() {
    window.location.href = 'prog_dashboard.php';
}

function fetchProgram(progId) {
    axios.get('prog_delete.php?prog_id=' + progId)
        .then(response => {
            console.log(response.data);
            if (response.data.success) {
                const program = response.data.data;
                document.getElementById('progfullname').value = program.progfullname;
                document.getElementById('progshortname').value = program.progshortname;
                document.getElementById('progcollname').value = program.progcollname;
                document.getElementById('progdeptname').value = program.progdeptname;
                document.getElementById('progid').value = program.progid;
            } else {
                console.error(response.data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching program:', error);
        });
}

function confirmDelete(event) {
    event.preventDefault();

    Swal.fire({
        title: 'Confirmation',
        text: "Do you want to delete this program?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteProgram();
        }
    });
}

function deleteProgram() {
    const formData = new FormData(document.getElementById('delete_form'));

    axios.post('prog_delete.php', formData)
        .then(response => {
            if (response.data.success) {
                Swal.fire(
                    'Deleted!',
                    'The program has been deleted.',
                    'success'
                ).then(() => {
                    window.location.href = 'prog_dashboard.php';
                });
            } else {
                console.error(response.data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting program:', error);
        });
}
