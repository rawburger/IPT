document.addEventListener('DOMContentLoaded', fetchStudents);

function cancelAdd() {
    window.location.href = 'student_dashboard.php';
}

function fetchStudents() {
    axios.get('student_add.php?action=fetch')
        .then(response => {
            const data = response.data;
            if (data.error) {
                console.error(data.error);
                return;
            }
            const colleges = data.colleges;
            const programs = data.programs;
            const collegeSelect = document.getElementById('college');
            const programSelect = document.getElementById('program');

            collegeSelect.innerHTML = '<option value="">----- Select College -----</option>';
            colleges.forEach(college => {
                collegeSelect.innerHTML += `<option value="${college.collid}">${college.collfullname}</option>`;
            });

            collegeSelect.addEventListener('change', () => {
                const selectedCollege = collegeSelect.value;
                programSelect.innerHTML = '<option value="">----- Select Program -----</option>';
                programs.forEach(program => {
                    if (program.progcollid == selectedCollege) {
                        programSelect.innerHTML += `<option value="${program.progid}">${program.progfullname}</option>`;
                    }
                });
            });
        })
        .catch(error => {
            console.error("There was an error fetching the form data:", error);
        });
}

function validateForm() {
    const studentId = document.getElementById('student_id').value;
    const firstName = document.getElementById('first_name').value;
    const middleName = document.getElementById('middle_name').value;
    const lastName = document.getElementById('last_name').value;
    const college = document.getElementById('college').value;
    const program = document.getElementById('program').value;
    const year = document.getElementById('year').value;
    const addField = /^[a-zA-Z\s]+$/;

    if (!studentId || !firstName || !middleName || !lastName || !college || !program || !year) {
        Swal.fire('Error!', 'All fields must be filled!', 'error');
        return false;
    }

    if (!addField.test(firstName) || !addField.test(middleName) || !addField.test(lastName)) {
        Swal.fire('Error!', 'Names cannot contain numbers or special characters!', 'error');
        return false;
    }

    return true;
}

function submitForm(event) {
    event.preventDefault();

    if (!validateForm()) {
        return;
    }

    const studentId = document.getElementById('student_id').value;
    const firstName = document.getElementById('first_name').value;
    const middleName = document.getElementById('middle_name').value;
    const lastName = document.getElementById('last_name').value;
    const college = document.getElementById('college').value;
    const program = document.getElementById('program').value;
    const year = document.getElementById('year').value;

    const formData = {
        student_id: studentId,
        first_name: firstName,
        middle_name: middleName,
        last_name: lastName,
        college: college,
        program: program,
        year: year
    };

    axios.post('student_add.php', formData)
        .then(response => {
            const data = response.data;
            if (data.success) {
                Swal.fire('Success!', 'Student information added successfully!', 'success')
                    .then(() => {
                        window.location.href = 'student_dashboard.php';
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
