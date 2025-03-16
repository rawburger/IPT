function addUser(event) {
    event.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const verifyPassword = document.getElementById('verify').value.trim();

    if (!username || !password || !verifyPassword) {
        Swal.fire('Error!', 'All fields are required.', 'error');
        return;
    }

    if (password !== verifyPassword) {
        Swal.fire('Error!', 'The password does not match.', 'error');
        return;
    }

    axios.post('register.php', {
        username: username,
        password: password,
        verify: verifyPassword
    }, {
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.data.success) {
            Swal.fire('Success!', 'New user added successfully!', 'success').then(() => {
                window.location.href = 'login.php';
            });
        } else {
            Swal.fire('Error!', response.data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'An error happened when adding the user.', 'error');
        console.error('Error:', error);
    });
}
