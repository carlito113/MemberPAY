<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Member Pay</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">



    </head>
    <body>
        <div class="parent d-flex flex-column">
            <div [innerHtml]="target" class="m-0"></div>
            @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
            @endif
            <div class="container">
                <div class="form-box login">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('login.submit') }}" method="POST">
                        @csrf
                        <h5>Welcome to</h5>
                        <h1>MemberPay</h1>
                        {{-- <div class="input-box">
                            <i class='bx bxs-buildings'></i>
                            <select name="organization" placeholder = "Organization" required>
                                <option value="" disabled selected hidden>Organization</option>
                                <option value="digits">Digits</option>
                                <option value="jco">JCO</option>
                            </select>
                        </div> --}}

                        <div class="input-box">
                            <i class='bx bxs-user' style="position: absolute; top: 50%; right: 10px;"></i>
                            <input type="text" name="username" placeholder="Username" autocomplete="username" required >

                        </div>
                        <div class="input-box" style="position: relative;">
                            <input id="password" type="password" name="password" placeholder="Password" autocomplete="current-password" required >
                            <i class='bx bx-show' id="togglePassword" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"></i>

                        </div>
                        <button type="submit" class="btn">Login</button>
                        <a href="" style="color: rgb(65, 62, 62);" routerLink="/register">
                            <p>Terms and Conditions</p>
                        </a>
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>
                <div class="toggle-box">
                    <div class="toggle-panel toggle-left">
                    <img src="{{ asset('3d-computer(1).png') }}" class="computer">
                        {{-- <p class="text-center pt-0 pb-0 p-4 mb-0" style="font-size: 13px;">
                            MemberPay is a payment platform that allows you to be a member of the organization.
                        </p> --}}
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const toggle = document.getElementById('togglePassword');
            const password = document.getElementById('password');

            toggle.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('bx-show');
                this.classList.toggle('bx-hide');
            });
        </script>
        <script>
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    new bootstrap.Alert(alert).close();
                });
            }, 5000); // Close after 5 seconds
        </script>
    </body>
</html>
