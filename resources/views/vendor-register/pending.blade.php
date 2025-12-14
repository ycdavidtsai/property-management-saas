<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Registration Pending - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            {{-- Success Card --}}
            <div class="bg-white rounded-2xl shadow-lg border p-8 text-center">
                {{-- Icon --}}
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                {{-- Title --}}
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Registration Submitted!</h1>
                <p class="text-gray-600 mb-6">
                    Thank you for registering. Your application is now under review.
                </p>

                {{-- What's Next --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left">
                    <h3 class="font-semibold text-gray-900 mb-3">What happens next?</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Our team will review your application within 1-2 business days</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>You'll receive an SMS and email once approved</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong>Your profile will be visible to all landlords</strong> on the platform marketplace</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Start receiving maintenance job opportunities from property managers</span>
                        </li>
                    </ul>
                </div>

                {{-- Contact Info --}}
                <p class="text-sm text-gray-500 mb-6">
                    Questions? Contact us at<br>
                    <a href="/cdn-cgi/l/email-protection#99eaece9e9f6ebedd9fce1f8f4e9f5fcb7faf6f4" class="text-blue-600 hover:underline"><span class="__cf_email__" data-cfemail="43303633332c313703263b222e332f266d202c2e">[email&#160;protected]</span></a>
                </p>

                {{-- Actions --}}
                <div class="space-y-3">
                    <a href="{{ route('login') }}"
                       class="block w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors text-center">
                        Go to Login
                    </a>
                    <a href="/"
                       class="block w-full py-3 px-4 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors text-center">
                        Back to Home
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <p class="text-center text-sm text-gray-400 mt-6">