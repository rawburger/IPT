document.addEventListener('DOMContentLoaded', fetchCollegeDetails);

function fetchCollegeDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const collegeId = urlParams.get('college_id');

    axios.get(`college_edit.php?action=fetch&college_id=${collegeId}`)
        .then(response => {
            const data = response.data;
            if (data.college) {
                const college = data.college;
                document.getElementById('college_id').value = college.collid;
                document.getElementById('college_fullname').value = college.collfullname;
                document.getElementById('college_shortname').value = college.collshortname;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'College not found!'
                });
            }
        })
        .catch(error => {
            console.error("Error fetching college details:", error);
        });
}

function cancelEdit() {
    window.location.href = 'college_dashboard.php';
}

function validateInputs(college_fullname, college_shortname) {
    const editField = /^[A-Za-z\s]+$/;

    if (!college_fullname || !college_shortname) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Fields must not be empty!'
        });
        return false;
    }

    if (!editField.test(college_fullname) || !editField.test(college_shortname)) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Names cannot contain numbers, special characters, or be empty!'
        });
        return false;
    }

    return true;
}

function submitForm(event) {
    event.preventDefault();
    const form = event.target;

    const college_fullname = form['college_fullname'].value;
    const college_shortname = form['college_shortname'].value;

    if (!validateInputs(college_fullname, college_shortname)) {
        return;
    }

    const formData = new FormData(form);

    axios.post('college_edit.php', formData)
        .then(response => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'College successfully updated!'
            }).then(() => {
                window.location.href = 'college_dashboard.php';
            });
        })
        .catch(error => {
            Swal.fire('Error!', 'There was an error submitting the form.', 'error');
            console.error("Error submitting the form:", error);
        });
}
