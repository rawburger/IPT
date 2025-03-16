function loginUser(event) {
    event.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!username || !password) {
        Swal.fire('Error!', 'Both fields are required.', 'error');
        return;
    }

    axios.post('login.php', {
        username: username,
        password: password
    }, {
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log(response.data);

        if (response.data.success) {
            Swal.fire('Success!', 'Logging in', 'success').then(() => {
                window.location.href = 'landing_page.php';
            });
        } else {
            Swal.fire('Error!', response.data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'An error occurred while logging in.', 'error');
        console.error('Error:', error);
    });
}
