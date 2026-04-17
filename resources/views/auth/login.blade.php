<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border-t-4 border-blue-600">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login Now</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-1" for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="name@example.com"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-1" for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="••••••••"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-200">
            Login
        </button>
    </form>

    <div class="flex items-center my-6">
        <hr class="flex-grow border-gray-200">
        <span class="mx-3 text-gray-400 text-sm font-medium">OR LOGIN WITH</span>
        <hr class="flex-grow border-gray-200">
    </div>

    <div class="flex flex-col gap-3">
        
        <a href="{{ route('google.login') }}" 
           class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 py-2 rounded-lg flex items-center justify-center gap-2 font-medium shadow-sm transition">
            <i class="bi bi-google text-red-500"></i> Continue with Google
        </a>

        <a href="{{ route('twitter.login') }}" 
           class="w-full bg-sky-500 hover:bg-sky-600 text-white py-2 rounded-lg flex items-center justify-center gap-2 font-medium shadow-sm transition">
            <i class="bi bi-twitter"></i> Continue with Twitter
        </a>

    </div>

    <div class="text-center mt-8 pt-4 border-t border-gray-100">
        <p class="text-gray-600 text-sm">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Register here</a>
        </p>
    </div>
</div>

</body>
</html>