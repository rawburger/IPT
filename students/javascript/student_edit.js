document.addEventListener('DOMContentLoaded', fetchStudentDetails);

        function fetchStudentDetails() {
            const urlParams = new URLSearchParams(window.location.search);
            const studentId = urlParams.get('student_id');

            axios.get(`student_edit.php?action=fetch&student_id=${studentId}`)
                .then(response => {
                    const data = response.data;
                    const student = data.student;
                    const colleges = data.colleges;
                    const programs = data.programs;

                    document.getElementById('studid').value = student.studid;
                    document.getElementById('studfirstname').value = student.studfirstname;
                    document.getElementById('studlastname').value = student.studlastname;
                    document.getElementById('studmidname').value = student.studmidname;
                    document.getElementById('studyear').value = student.studyear;
                    document.getElementById('studcollid').value = student.studcollid;

                    const collegeSelect = document.getElementById('studcollid');
                    const programSelect = document.getElementById('studprogid');

                    collegeSelect.innerHTML = '<option value="">----- Select College -----</option>';
                    colleges.forEach(college => {
                        const selected = college.collid == student.studcollid ? 'selected' : '';
                        collegeSelect.innerHTML += `<option value="${college.collid}" ${selected}>${college.collfullname}</option>`;
                    });

                    programSelect.innerHTML = '<option value="">----- Select Program -----</option>';
                    programs.forEach(program => {
                        if (program.progcollid == student.studcollid) {
                            const selected = program.progid == student.studprogid ? 'selected' : '';
                            programSelect.innerHTML += `<option value="${program.progid}" ${selected}>${program.progfullname}</option>`;
                        }
                    });
                })
                .catch(error => {
                    console.error("There was an error fetching the student details:", error);
                });
        }

        function cancelEdit() {
            window.location.href = 'student_dashboard.php';
        }

        function validateInputs() {
            const studfirstname = document.getElementById('studfirstname').value;
            const studlastname = document.getElementById('studlastname').value;
            const studmidname = document.getElementById('studmidname').value;
            const studcollid = document.getElementById('studcollid').value;
            const studprogid = document.getElementById('studprogid').value;
            const studyear = document.getElementById('studyear').value;
            const initialYear = parseInt(document.getElementById('studyear').dataset.initialYear);
            const editField = /^[a-zA-Z\s]+$/;

            if (!studfirstname || !studlastname || !studmidname || !studcollid || !studprogid || !studyear) {
                Swal.fire('Error!', 'All fields must be filled!', 'error');
                return false;
            }

            if (!editField.test(studfirstname) || !editField.test(studlastname) || !editField.test(studmidname)) {
                Swal.fire('Error!', 'Names cannot contain numbers or special characters!', 'error');
                return false;
            }

            const newYear = parseInt(studyear);
            if (newYear < initialYear) {
                Swal.fire('Error!', 'Year cannot be changed to a lower value!', 'error');
                return false;
            }

            if (initialYear <= 5) {
                Swal.fire('Error!', 'Year cannot exceed 5!', 'error');
                return false;
            }

            return true;
        }

        function updateStudent() {
            if (!validateInputs()) {
                return;
            }

            const form = document.getElementById('form_edit');
            const formData = new FormData(form);

            axios.post('student_edit.php', formData)
                .then(function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: 'Student information has been updated successfully.',
                    }).then(() => {
                        window.location.href = 'student_dashboard.php';
                    });
                })
                .catch(function (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error updating the student information.',
                    });
                    console.error("There was an error updating the student:", error);
                });
        }