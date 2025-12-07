
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded shadow w-full max-w-md">
    <!-- Page Heading -->
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <!-- Display Error Message (e.g., invalid credentials) -->
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Display Success Message (e.g., after registration) -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Normal Email/Password Login Form -->
    <form action="{{ route('login') }}" method="POST" class="space-y-5">
        @csrf <!-- CSRF token for security -->

        <!-- Email Input -->
        <div>
            <label class="block text-gray-700 mb-1" for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email"
                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Password Input -->
        <div>
            <label class="block text-gray-700 mb-1" for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password"
                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
            Login
        </button>
    </form>

    <!-- OR Divider -->
    <div class="flex items-center my-4">
        <hr class="flex-grow border-gray-300">
        <span class="mx-2 text-gray-500">OR</span>
        <hr class="flex-grow border-gray-300">
    </div>

    <!-- Google Login Button -->
    <a href="{{ route('google.login') }}" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded flex items-center justify-center gap-2">
         Login with Google
    </a>

    <!-- Link to Registration Page -->
    <div class="text-center mt-5">
        <p class="text-gray-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register here</a>
        </p>
    </div>
</div>

</body>
</html>
