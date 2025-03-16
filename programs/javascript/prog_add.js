document.addEventListener('DOMContentLoaded', fetchPrograms);

function cancelAdd() {
    window.location.href = 'prog_dashboard.php';
}

function fetchPrograms() {
    axios.get('prog_add.php?action=fetch')
        .then(response => {
            const data = response.data;
            const colleges = data.colleges;
            const departments = data.departments;
            const collegeSelect = document.getElementById('prog_collid');
            const deptSelect = document.getElementById('prog_deptid');

            collegeSelect.innerHTML = '<option value="">----- Select College -----</option>';
            colleges.forEach(college => {
                collegeSelect.innerHTML += `<option value="${college.collid}">${college.collfullname}</option>`;
            });

            deptSelect.innerHTML = '<option value="">----- Select Department -----</option>';
            collegeSelect.addEventListener('change', () => {
                const selectedCollege = collegeSelect.value;
                deptSelect.innerHTML = '<option value="">----- Select Department -----</option>';
                departments.forEach(department => {
                    if (department.deptcollid == selectedCollege) {
                        deptSelect.innerHTML += `<option value="${department.deptid}">${department.deptfullname}</option>`;
                    }
                });
            });
        })
        .catch(error => {
            console.error("There was an error getting the form data:", error);
        });
}

function validateInputs() {
    const progId = document.getElementById('prog_id').value;
    const progName = document.getElementById('prog_name').value;
    const progSName = document.getElementById('prog_short').value;
    const progCollId = document.getElementById('prog_collid').value;
    const progDeptId = document.getElementById('prog_deptid').value;
    addField = /^[a-zA-Z\s]+$/;

    if (!progId || !progName || !progSName || !progCollId || !progDeptId) {
        Swal.fire('Error!', 'All fields must be filled!', 'error');
        return false;
    }

    if (!addField.test(progName) || !addField.test(progSName)) {
        Swal.fire('Error!', 'Names cannot contain numbers or special characters!', 'error');
        return false;
    }

    return true;
}

function submitForm(event) {
    event.preventDefault();
    const form = event.target;

    if (!validateInputs()) {
        return;
    }

    const progId = document.getElementById('prog_id').value;
    const progName = document.getElementById('prog_name').value;
    const progSName = document.getElementById('prog_short').value;
    const progCollId = document.getElementById('prog_collid').value;
    const progDeptId = document.getElementById('prog_deptid').value;

    const formData = {
        prog_id: progId,
        prog_name: progName,
        prog_short: progSName,
        prog_collid: progCollId,
        prog_deptid: progDeptId,
    };

    axios.post('prog_add.php', formData)
        .then(response => {
            const data = response.data;

            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Program added successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'prog_dashboard.php';
                });
            } else if (data.error === "Program ID already exists") {
                Swal.fire({
                    title: 'Error!',
                    text: 'Program ID already exists!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                console.error(data.error);
            }
        })
        .catch(error => {
            console.error("There was an error submitting the form:", error);
        });
}

