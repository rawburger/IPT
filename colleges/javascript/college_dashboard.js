function backtoLanding() {
    window.location.href = '../landing_page.php';
}

function addNewCollege() {
    window.location.href = 'college_add.php';
}

function editCollege(collegeId) {
    window.location.href = 'college_edit.php?college_id=' + collegeId;
}

function deleteCollege(collegeId) {
    window.location.href = 'college_delete.php?college_id=' + collegeId;
}

function fetchColleges() {
    axios.get('college_dashboard.php?action=fetch')
        .then(response => {
            if (response.data.success) {
                const colleges = response.data.data;
                const tableBody = document.getElementById('college-data');

                tableBody.innerHTML = '';

                colleges.forEach(college => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${college.collid}</td>
                        <td>${college.collfullname}</td>
                        <td>${college.collshortname}</td>
                        <td class='actions'>
                            <button onclick="deleteCollege(${college.collid})"><i class="bi bi-trash3-fill" style="color: red;"></i></button>
                            <button onclick="editCollege(${college.collid})"><i class="bi bi-pencil"></i></button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                console.error('Failed to get the data:', response.data.message);
            }
        })
        .catch(error => {
            console.error('Error getting colleges:', error);
        });
}

document.addEventListener('DOMContentLoaded', fetchColleges);
