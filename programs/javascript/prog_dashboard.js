
function backtoLanding() {
    window.location.href = '../landing_page.php';
}

function addNewProgram() {
    window.location.href = 'prog_add.php';
}

function editProgram(progId) {
    window.location.href = 'prog_edit.php?prog_id=' + progId;
}

function deleteProgram(progId) {
    window.location.href = 'prog_delete.php?prog_id=' + progId;
}

function fetchPrograms() {
    axios.get('prog_dashboard.php?action=fetch')
        .then(response => {
            if (response.data.success) {
                const programs = response.data.data;
                const tableBody = document.getElementById('program-data');

                tableBody.innerHTML = '';

                programs.forEach(program => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${program.progid}</td>
                        <td>${program.progfullname}</td>
                        <td>${program.progshortname}</td>
                        <td>${program.college_name}</td>
                        <td>${program.dept_name}</td>
                        <td class='actions'>
                            <button onclick="deleteProgram(${program.progid})"><i class="bi bi-trash-fill" style="color: red;"></i></button>
                            <button onclick="editProgram(${program.progid})"><i class="bi bi-pencil"></i></button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                console.error('Failed to get the data:', response.data.message);
            }
        })
        .catch(error => {
            console.error('Error getting programs:', error);
        });
}

document.addEventListener('DOMContentLoaded', fetchPrograms);