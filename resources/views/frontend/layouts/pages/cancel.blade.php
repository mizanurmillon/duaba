<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment Cancelled</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body class="bg-red-100 flex items-center justify-center h-screen">
  <div class="bg-white p-8 rounded-2xl shadow-lg text-center max-w-md">
    <!-- Cancel Animation -->
    <lottie-player
      src="https://assets1.lottiefiles.com/packages/lf20_qp1q7mct.json"
      background="transparent"
      speed="1"
      style="width: 200px; height: 200px; margin: 0 auto;"
      loop
      autoplay>
    </lottie-player>

    <!-- Title -->
    <h2 class="text-2xl font-bold text-red-600 mt-4">Payment Cancelled</h2>

    <!-- Description -->
    <p class="text-gray-600 mt-2">Your payment has been cancelled. If this was a mistake, please try again.</p>

  </div>
</body>
</html>
