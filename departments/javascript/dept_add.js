document.addEventListener('DOMContentLoaded', fetchColleges);

function cancelAdd() {
    window.location.href = 'dept_dashboard.php';
}

function fetchColleges() {
    axios.get('dept_add.php?action=fetch')
        .then(response => {
            const data = response.data;

            if (data.error) {
                console.error(data.error);
                return;
            }

            const colleges = data.colleges || [];
            const collegeSelect = document.getElementById('dept_collid');
            collegeSelect.innerHTML = '<option value="">----- Select College -----</option>';
            colleges.forEach(college => {
                collegeSelect.innerHTML += `<option value="${college.collid}">${college.collfullname}</option>`;
            });
        })
        .catch(error => {
            console.error("There was an error fetching the colleges data:", error);
        });
}

function validateInputs() {
    const deptId = document.getElementById('dept_id').value;
    const deptName = document.getElementById('dept_fullname').value;
    const deptSName = document.getElementById('dept_shortname').value;
    const deptColl = document.getElementById('dept_collid').value;
    const addField = /^[A-Za-z\s]+$/;

    if (!deptId || !deptName || !deptColl) {
        Swal.fire('Error!', 'All fields must be filled.', 'error');
        return false;
    }

    if (!addField.test(deptName)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Department Full Name should not contain numbers or special characters.'
        });
        return false;
    }

    return true;
}

function submitForm(event) {
    event.preventDefault();

    if (!validateInputs()) {
        return;
    }

    const dept_id = document.getElementById('dept_id').value;
    const dept_fullname = document.getElementById('dept_fullname').value;
    const dept_shortname = document.getElementById('dept_shortname').value;
    const dept_collid = document.getElementById('dept_collid').value;

    const formData = JSON.stringify({
        dept_id: dept_id,
        dept_fullname: dept_fullname,
        dept_shortname: dept_shortname,
        dept_collid: dept_collid
    });

    axios.post('dept_add.php', formData, {
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        const data = response.data;

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Department successfully added!'
            }).then(() => {
                window.location.href = 'dept_dashboard.php';
            });
        } else if (data.error === "Department ID already exists") {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Department ID already exists!'
            });
        } else {
            console.error(data.error);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'There was an error submitting the form: ' + error
        });
    });
}



