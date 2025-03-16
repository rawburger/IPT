document.addEventListener('DOMContentLoaded', fetchDepartmentDetails);

function cancelEdit() {
    window.location.href = 'dept_dashboard.php';
}

function fetchDepartmentDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const deptId = urlParams.get('dept_id');
   // const deptId = document.getElementById('dept_id').value;

    axios.get(`dept_edit.php?action=fetch&deptid=${deptId}`)
        .then(response => {
            const data = response.data;
            const department = data.department;
            const colleges = data.colleges || [];
            const collegeSelect = document.getElementById('dept_collid');

            document.getElementById('dept_id').value = department.deptid;
            document.getElementById('dept_fullname').value = department.deptfullname;
            document.getElementById('dept_shortname').value = department.deptshortname;

            collegeSelect.innerHTML = '<option value="">----- Select College -----</option>';
            colleges.forEach(college => {
                const selected = college.collid == department.deptcollid ? 'selected' : '';
                collegeSelect.innerHTML += `<option value="${college.collid}" ${selected}>${college.collfullname}</option>`;
            });
        })
        .catch(error => {
            console.error("There was an error fetching the department details:", error);
        });
}

function validateInputs() {
    const deptId = document.getElementById('dept_id').value;
    const deptName = document.getElementById('dept_fullname').value;
    const deptColl = document.getElementById('dept_collid').value;
    const editField = /^[A-Za-z\s]+$/;

    if (!deptId || !deptName || !deptColl) {
        Swal.fire('Error!', 'All fields must be filled.', 'error');
        return false;
    }

    if (!editField.test(deptName)) {
        Swal.fire('Error!', 'Department Full Name should not contain numbers or special characters.', 'error');
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

    const formData = {
        dept_id: dept_id,
        dept_fullname: dept_fullname,
        dept_shortname: dept_shortname,
        dept_collid: dept_collid
    };

    axios.post('dept_edit.php', formData)
        .then(response => {
            const data = response.data;

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Department successfully updated!'
                }).then(() => {
                    window.location.href = 'dept_dashboard.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'There was an issue updating the department.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error submitting the form.'
            });
            console.error("There was an error submitting the form:", error);
        });
}


