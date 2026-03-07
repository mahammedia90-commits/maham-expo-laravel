<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Reset Your Password</h2>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Hidden inputs to capture token and email -->
            <input type="hidden" name="token" value="{{ request('token') }}">
            <input type="hidden" name="email" value="{{ request('email') }}">

            <div class="mb-4">
                <label class="block mb-2 font-semibold">New Password</label>
                <input type="password" name="password" required
                       class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300">
            </div> 

            <div class="mb-6">
                <label class="block mb-2 font-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition">
                Reset Password
            </button>
        </form>
    </div>

</body>
</html>
