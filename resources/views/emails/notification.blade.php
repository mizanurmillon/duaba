 {{--  resources/views/emails/custom_notification.blade.php   --}}
 <!DOCTYPE html>
 <html>

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <style>
         body {
             font-family: Arial, sans-serif;
             background-color: #f4f4f4;
             margin: 0;
             padding: 0;
         }

         .container {
             width: 100%;
             padding: 20px;
             background-color: #ffffff;
             box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
             margin: 30px auto;
             max-width: 600px;
             border-radius: 8px;
         }

         .header {
             background-color: #4CAF50;
             padding: 10px;
             border-radius: 8px 8px 0 0;
             color: #ffffff;
             text-align: center;
         }

         .content {
             padding: 20px;
         }

         .button {
             display: inline-block;
             padding: 10px 20px;
             margin: 20px 0;
             background-color: #4CAF50;
             color: #ffffff;
             text-decoration: none;
             border-radius: 5px;
         }

         .footer {
             text-align: center;
             padding: 10px;
             font-size: 12px;
             color: #777777;
         }

         .footer a {
             color: #007bff;
             text-decoration: none;
         }
     </style>
 </head>

 <body>
     <div class="container">
         <div class="header">
             <h1>{{ config('app.name') }}</h1>
         </div>
         <div class="content">
             <h2>Hello {{ $notifiable->name ?? '' }}</h2>
             <p>{{ $messageContent }}</p>
             <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
         </div>
         <div class="footer">
             <p>Thanks,<br>{{ config('app.name') }}</p>
             <p>If you did not expect this email, you can safely ignore it.</p>
         </div>
     </div>
 </body>

 </html>
