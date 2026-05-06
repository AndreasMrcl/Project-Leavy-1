<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    @include('layout.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>

</head>

<body>

    <div class="h-screen w-full mx-auto bg-gradient-to-b from-red-800 to-gray-100 flex items-center p-4 xl:p-0 2xl:p-0">
        <div
            class="h-screen w-full mx-auto bg-gradient-to-b from-red-800 to-gray-100 flex items-center p-4 xl:p-0 2xl:p-0">
            <div class="mx-auto sm:max-w-sm w-full space-y-3 p-8 bg-white rounded-3xl">
                <div class="space-y-3">
                    <div>
                        <img class="w-28 h-16 md:w-32 md:h-20" src="{{ asset('/beil.svg') }}" alt="">
                    </div>
                    <div>
                        <h1 class="text-2xl xl:text-3xl 2xl:text-3xl font-extrabold text-black">Login</h1>
                        <p class="text-black text-lg xl:text-xl 2xl:text-xl font-extralight">Sign in to your account</p>
                    </div>
                </div>
                <form method="post" action="{{ route('signin') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label for="email" class="text-black">Email</label>
                        <input class="w-full p-2 bg-gray-100 rounded-xl" type="email" name="email" required />
                    </div>
                    <div class='space-y-2'>
                        <label for="password" class='text-black'>Password</label>
                        <div class="password-container">
                            <input id="password" class='w-full p-2 bg-gray-100 rounded-xl pr-12' type="password"
                                name="password" required />
                            <i id="toggle-password" class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>
                    <div
                        class='border-4 border-red-700 p-2 rounded-3xl w-3/5 mx-auto hover:scale-110 duration-200 transition-all'>
                        <button name="submit" type="submit"
                            class='flex mx-auto text-black text-xl xl:text-2xl 2xl:text-2xl font-semibold'>
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('sweetalert::alert')
    <script>
        // Password Visibility Toggle for Login
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>

</html>
