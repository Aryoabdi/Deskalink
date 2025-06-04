<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Deskalink') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    transitionProperty: {
                        'height': 'height',
                        'spacing': 'margin, padding',
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased bg-white text-gray-900">
    <!-- Header -->
    <header class="fixed w-full bg-white border-b z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <span class="text-2xl font-bold tracking-tight">Deskalink</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 transition-colors">Log in</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-md bg-black text-white hover:bg-gray-800 transition-all duration-200 transform hover:scale-105">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 tracking-tight">
                    Connect with Top Design<br>Professionals
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    Find and collaborate with skilled designers for your interior design, architectural, and visualization projects.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-black text-white rounded-md hover:bg-gray-800 transition-all duration-200 transform hover:scale-105">
                        Start Your Project
                    </a>
                    <a href="#services" class="px-8 py-4 border border-gray-300 rounded-md hover:border-gray-400 hover:bg-gray-50 transition-all duration-200">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 max-w-6xl">
            <h2 class="text-3xl font-bold text-center mb-12">Our Services</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Interior Design -->
                <div class="bg-white p-8 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-black text-white rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-couch text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Interior Design</h3>
                    <p class="text-gray-600">Transform your space with professional interior design services tailored to your style and needs.</p>
                </div>

                <!-- Architecture -->
                <div class="bg-white p-8 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-black text-white rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Architecture</h3>
                    <p class="text-gray-600">Expert architectural services for residential and commercial projects of any scale.</p>
                </div>

                <!-- 3D Visualization -->
                <div class="bg-white p-8 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-black text-white rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-cube text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">3D Visualization</h3>
                    <p class="text-gray-600">Bring your designs to life with high-quality 3D renderings and visualizations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20">
        <div class="container mx-auto px-6 max-w-6xl">
            <h2 class="text-3xl font-bold text-center mb-16">How It Works</h2>
            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center group">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-black group-hover:text-white transition-colors duration-300">
                        <span class="text-3xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Post Your Project</h3>
                    <p class="text-gray-600">Describe your design needs and requirements in detail.</p>
                </div>
                <div class="text-center group">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-black group-hover:text-white transition-colors duration-300">
                        <span class="text-3xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Connect with Designers</h3>
                    <p class="text-gray-600">Review profiles and portfolios to find the perfect match.</p>
                </div>
                <div class="text-center group">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-black group-hover:text-white transition-colors duration-300">
                        <span class="text-3xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Get Results</h3>
                    <p class="text-gray-600">Work together to bring your design vision to life.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-white py-16">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-12">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Deskalink</h3>
                    <p class="text-gray-400">Connecting design professionals with clients worldwide.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Services</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Interior Design</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Architecture</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">3D Visualization</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">How It Works</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Deskalink. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
