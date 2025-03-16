function backtoLanding() {
    window.location.href = '../landing_page.php';
}

function addNewStudent() {
    window.location.href = 'student_add.php';
}

function editStudent(studentId) {
    window.location.href = 'student_edit.php?student_id=' + studentId;
}

function deleteStudent(studentId) {
    window.location.href = 'student_delete.php?student_id=' + studentId;
}

function fetchStudents() {
    axios.get('student_dashboard.php?action=fetch')
        .then(response => {
            const students = response.data;

            const tbody = document.querySelector('tbody');
            tbody.innerHTML = '';

            if (students.error) {
                tbody.innerHTML = `<tr><td colspan="8">${students.error}</td></tr>`;
                return;
            }

            students.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.ID}</td>
                    <td>${student.LastName}</td>
                    <td>${student.FirstName}</td>
                    <td>${student.MiddleName}</td>
                    <td>${student.College}</td>
                    <td>${student.Program}</td>
                    <td>${student.Year}</td>
                    <td class='actions'>
                        <button onclick="deleteStudent(${student.ID})"><i class="bi bi-trash3-fill" style="color: red;"></i></button>
                        <button onclick="editStudent(${student.ID})"><i class="bi bi-pencil"></i></button>
                    </td>`;
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error("There was an error getting the students data:", error);
        });
}

document.addEventListener('DOMContentLoaded', fetchStudents);
