function cancelEdit() {
    window.location.href = 'prog_dashboard.php';
}

function validateInputs(prog_name, prog_short) {
    const editField = /^[A-Za-z\s]+$/;

    if (!prog_name || !editField.test(prog_name)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Names cannot contain numbers, special characters / is empty!'
        });
        return false;
    }

    if (!prog_short || !editField.test(prog_short)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Names cannot contain numbers, special characters / is empty!'
        });
        return false;
    }

    return true;
}

function submitForm(event) {
    event.preventDefault();
    const form = event.target;

    const prog_name = form['prog_name'].value;
    const prog_short = form['prog_short'].value;

    if (!validateInputs(prog_name, prog_short)) {
        return;
    }

    const formData = {
        prog_id: form['prog_id'].value,
        prog_name: prog_name,
        prog_short: prog_short,
        prog_collid: form['prog_collid'].value,
        prog_deptid: form['prog_deptid'].value
    };

    axios.post('prog_edit.php', formData)
        .then(response => {
            const data = response.data;

            if (data.success) {
                Swal.fire('Updated!', 'The program has been updated successfully.', 'success').then(() => {
                    window.location.href = 'prog_dashboard.php';
                });
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'There was an error submitting the form.', 'error');
            console.error("There was an error submitting the form:", error);
        });
}
