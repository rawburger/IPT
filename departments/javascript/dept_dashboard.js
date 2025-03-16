function backtoLanding() {
    window.location.href = '../landing_page.php';
}

function addNewDept() {
    window.location.href = 'dept_add.php';
}

function editDept(deptId) {
    window.location.href = 'dept_edit.php?deptid=' + deptId;
}

function deleteDept(deptId) {
    window.location.href = 'dept_delete.php?deptid=' + deptId;
}

function fetchDepartments() {
    axios.get('dept_dashboard.php?action=fetch')
        .then(response => {
            if (response.data.success) {
                const departments = response.data.departments;
                const tableBody = document.getElementById('departments-tbody');

                tableBody.innerHTML = '';

                departments.forEach(department => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${department.deptid}</td>
                        <td>${department.deptfullname}</td>
                        <td>${department.deptshortname || ''}</td>
                        <td>${department.collfullname}</td>
                        <td class='actions'>
                            <button onclick="deleteDept(${department.deptid})"><i class="bi bi-trash3-fill" style="color: red;"></i></button>
                            <button onclick="editDept(${department.deptid})"><i class="bi bi-pencil"></i></button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                console.error('Failed to get the data:', response.data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching departments:', error);
        });
}

document.addEventListener('DOMContentLoaded', fetchDepartments);