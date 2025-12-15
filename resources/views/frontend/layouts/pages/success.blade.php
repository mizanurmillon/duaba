<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment Successful</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  <style>
    body {
      background: radial-gradient(ellipse at center, #d1fae5, #6ee7b7);
    }

    .glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      border-radius: 1.5rem;
      border: 1px solid rgba(255, 255, 255, 0.25);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease;
    }

    .glass:hover {
      transform: scale(1.015);
    }

    .glow-button {
      background: linear-gradient(to right, #10b981, #34d399);
      box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
    }

    .glow-button:hover {
      background: linear-gradient(to right, #059669, #10b981);
      transform: scale(1.03);
    }
  </style>
</head>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment Successful</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  <style>
    body {
      background: radial-gradient(ellipse at center, #d1fae5, #6ee7b7);
    }

    .glass {
      background: white;
      border-radius: 1.5rem;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease;
    }

    .glass:hover {
      transform: scale(1.015);
    }

    .glow-button {
      background: linear-gradient(to right, #10b981, #34d399);
      box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
    }

    .glow-button:hover {
      background: linear-gradient(to right, #059669, #10b981);
      transform: scale(1.03);
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen text-gray-900 font-sans">
  <div class="glass p-8 max-w-lg w-full text-center animate-fadeIn">
    <!-- Lottie Animation -->
    <lottie-player
      src="https://assets9.lottiefiles.com/packages/lf20_jcikwtux.json"
      background="transparent"
      speed="1"
      style="width: 200px; height: 200px; margin: auto"
      autoplay
    ></lottie-player>

    <!-- Title -->
    <h1 class="text-3xl font-extrabold text-green-700 mt-4">Payment Successful</h1>

    <!-- Message -->
    <p class="text-sm text-gray-700 mt-2">Your transaction has been completed successfully.</p>

    
  </div>
</body>
</html>

