 const app = {
            loginUrl: "http://localhost/Exercise-Yflix/public/api/login_check",
            jwtToken: "",

            init: function() {
                // Initialize your app, if needed
            },

            handleConnectionBtnClick: function(evt) {
                // Get the username and password from input fields
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;

                fetch(app.loginUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(json) {
                    // console.log(json.token);
                    // Save the token in localStorage
                    localStorage.setItem('jwt-token', json.token);

                    // Set the token in the app object for future use
                    app.jwtToken = json.token;
                })
                .catch(function(error) {
                    console.error('Error during login:', error);
                });
            }
        };

        // Initialize the app when the DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            app.init();
            document.querySelector('#username').addEventListener('input', function() {
                app.handleConnectionBtnClick();
            });
            document.querySelector('#password').addEventListener('input', function() {
                app.handleConnectionBtnClick();
            });
        });