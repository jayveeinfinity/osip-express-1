<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>OSIP Express</title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/sweetalert2.js"></script>
    </head>
    <body>
        <div class="container-fluid vh-100 bg-1 p-0">
            <div class="vh-100 bg-overlay">
                <section class="main-content d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-center" style="height: 250px;">
                        <img class="object-fit-cover" src="assets/images/logo.png">
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="card" style="width: 36rem;">
                            <div class="card-body">
                                <h5 class="card-title text-center">VALIDATION</h5>
                                <form class="m-3">
                                    <label for="exampleFormControlInput1" class="form-label mb-0">Card number</label>
                                    <input type="text" class="form-control" placeholder="Type card number..." autofocus maxlength="9" oninput="validateNumberInput(this)" data-input="cardnumber">
                                    <span id="error-message" style="color: green;"></span>
                                </form>
                                <div class="px-3 text-end">
                                    <button role="button" class="btn btn-primary" data-click="findPatron" disabled>Find patron</button>
                                </div>
                                <div class="student_info p-3 d-none" data-info="studentInfo">
                                    <div class="alert bg-danger text-white" role="alert" data-alert="expired">
                                        <strong>Registration is expired!</strong>
                                    </div>
                                    <div class="alert bg-success text-white" role="alert" data-alert="registered">
                                        <strong>Account is registered!</strong>
                                    </div>
                                    <table class="table-light table table-striped">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Card number</th>
                                                <td data-text="cardnumber"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Name</th>
                                                <td data-text="name"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email</th>
                                                <td data-text="email"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Phone</th>
                                                <td data-text="phone"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">College</th>
                                                <td data-text="college"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Course</th>
                                                <td data-text="course"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Date Expiry</th>
                                                <td data-text="dateexpiry"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-end d-none" data-show="validateBtn">
                                        <button role="button" class="btn btn-primary" data-click="validatePatron">Validate patron</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <script>
            const cardNumberInput = document.querySelector('[data-input="cardnumber"]');
            const findPatronBtn = document.querySelector('[data-click="findPatron"]');
            let isButtonActive = false;

            const studentInfo = document.querySelector('[data-info="studentInfo"]');

            const cardNumberText = document.querySelector('[data-text="cardnumber"]');
            const nameText = document.querySelector('[data-text="name"]');
            const emailText = document.querySelector('[data-text="email"]');
            const phoneText = document.querySelector('[data-text="phone"]');
            const collegeText = document.querySelector('[data-text="college"]');
            const courseText = document.querySelector('[data-text="course"]');
            const dateExpiryText = document.querySelector('[data-text="dateexpiry"]');

            const alertExpired = document.querySelector('[data-alert="expired"]');
            const alertRegistered = document.querySelector('[data-alert="registered"]');

            const showPatronBtn = document.querySelector('[data-show="validateBtn"]');
            const validatePatronBtn = document.querySelector('[data-click="validatePatron"]');
            
            cardNumberInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    if(!isButtonActive) return;
                    findPatronBtn.click();
                }
            });

            findPatronBtn.addEventListener('click', (e) => {
                event.preventDefault();
                makeApiCall();
            });

            validatePatronBtn.addEventListener('click', (e) => {
                event.preventDefault();
                makeApiCall2();
            });

            function validateNumberInput(input) {
                // Allow only digits
                input.value = input.value.replace(/[^0-9]/g, '');

                // Optional: Display error message if any non-numeric value is entered
                const errorMessage = document.getElementById('error-message');
                if (input.value.length == 9) {
                    errorMessage.textContent = "Card number is valid!";
                    isButtonActive = true;
                } else {
                    errorMessage.textContent = "";
                    isButtonActive = false;
                }

                if(findPatronBtn.hasAttribute('disabled')) {
                    if(isButtonActive) {
                        findPatronBtn.removeAttribute('disabled');
                    }
                } else {
                    findPatronBtn.setAttribute('disabled', '');
                }
            }
            
            // GET PATRON DATA
            function makeApiCall() {
                const endpoint = 'http://library.cvsu.edu.ph/sandbox/laravel/api/patrons/';
                const bearerToken = '5331|K8zARyqmUtfj8plNwTyLGGVaRRgJjMfkFBiSaay1';

                const requestData = {
                    cardNumber: cardNumberInput.value
                };

                $.ajax({
                    url: endpoint + cardNumberInput.value,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + bearerToken,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(requestData),
                    success: function(response) {
                        if(studentInfo.classList.contains('d-none')) {
                            studentInfo.classList.remove('d-none');
                        }

                        const data = response.data;
                        cardNumberText.innerText = data.cardnumber;
                        nameText.innerText = data.firstname +  ' ' + data.middle_name + ' ' + data.surname;
                        emailText.innerText = data.email;
                        phoneText.innerText = data.phone;
                        collegeText.innerText = data.sort2;
                        courseText.innerText = data.sort1;
                        dateExpiryText.innerText = data.dateexpiry;

                        if(!data.isExpired) {
                            alertExpired.classList.add('d-none');
                            alertRegistered.classList.remove('d-none');
                            showPatronBtn.classList.add('d-none');
                        } else {
                            alertRegistered.classList.add('d-none');
                            alertExpired.classList.remove('d-none');
                            showPatronBtn.classList.remove('d-none');
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            title: 'For Registration',
                            text: 'Patron not found',
                            icon: "error",
                            allowOutsideClick: false
                        });

                        if(!studentInfo.classList.contains('d-none')) {
                            studentInfo.classList.add('d-none');
                        }

                        cardNumberInput.reset();
                        cardNumberInput.focus();
                    }
                });
            }

            // VALIDATION
            function makeApiCall2() {
                const endpoint = 'http://library.cvsu.edu.ph/sandbox/laravel/api/patrons/updateExpiry/';
                const bearerToken = '5331|K8zARyqmUtfj8plNwTyLGGVaRRgJjMfkFBiSaay1';

                const requestData = {
                    cardNumber: cardNumberInput.value
                };

                $.ajax({
                    url: endpoint,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + bearerToken,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(requestData),
                    beforeSend: function() {
                        validatePatronBtn.setAttribute('disabled', '');
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Account validated',
                            icon: "success",
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                makeApiCall();
                            }
                        });
                    },
                    error: function(error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Validation error',
                            icon: "error",
                            allowOutsideClick: false
                        });
                    }
                });

                validatePatronBtn.removeAttribute('disabled');
            }
        </script>
        <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    </body>
</html>