function cancelAdd() {
    window.location.href = 'college_dashboard.php';
}

function validateInputs() {
    const collegeId = document.getElementById('coll_id').value;
    const collegeName = document.getElementById('coll_name').value;
    const collegeShortname = document.getElementById('coll_shortname').value;
    const addField = /^[a-zA-Z\s]+$/;

    if (!collegeId || !collegeName || !collegeShortname) {
        Swal.fire('Error!', 'All fields must be filled.', 'error');
        return false;
    }

    if (!addField.test(collegeName)) {
        Swal.fire('Error!', 'College Full Name cannot contain numbers or special characters.', 'error');
        return false;
    }

    if (!addField.test(collegeShortname)) {
        Swal.fire('Error!', 'College Short Name cannot contain numbers or special characters.', 'error');
        return false;
    }

    return true;
}

function submitForm(event) {
    event.preventDefault();

    if (!validateInputs()) {
        return;
    }

    
    const collegeId = document.getElementById('coll_id').value;
    const collegeName = document.getElementById('coll_name').value;
    const collegeShortname = document.getElementById('coll_shortname').value;
   
    const formData = { 
        coll_id: collegeId,
        coll_name: collegeName,
        coll_short: collegeShortname 
    }

    axios.post('college_add.php', formData)
    .then(response => {
        if (response.data.success) {
            Swal.fire(
                'Added!',
                'College has been added successfully.',
                'success'
            ).then(() => {
                window.location.href = 'college_dashboard.php';
            });
        } else {
            Swal.fire(
                'Error!',
                response.data.message,
                'error'
            );
        }
    })
    .catch(error => {
        Swal.fire(
            'Error!',
            'Error adding college: ' + error,
            'error'
        );
    });
}
