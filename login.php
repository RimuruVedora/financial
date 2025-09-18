<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Soliera Hotel - Department Login</title>
    
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
</head>
<body>
   <section class="relative w-full h-screen">
  
  <div class="absolute inset-0 bg-cover bg-center z-0" style="background-image: url('layout/resources/images/login_background.jpg');"></div>
  <div class="absolute inset-0 bg-black/40 z-10"></div>
  <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/70 z-10"></div>
  
  <div class="relative z-10 w-full h-full flex justify-center items-center p-4">
    <div class="w-1/2 flex justify-center items-center max-md:hidden">
      <div class="max-w-lg p-8">
        <div class="text-center mb-8">
          <a href="/">
          <img data-aos = "zoom-in" data-aos-delay = "100"  class="w-full max-h-52 hover:scale-105 transition-all" src="logofinal.png" alt="">
          </a>
          <h1 data-aos = "zoom-in-up" data-aos-delay="200" class="text-3xl font-bold text-white mb-2">Welcome to <span class="text-[#F7B32B]">Soliera<span> Hotel & Restaurant</h1>
          <p data-aos = "zoom-in-up" data-aos-delay="300" class="text-white/80">  Savor The Stay, Dine With Elegance</p>
        </div>
        <div data-aos="zoom-in-up" data-aos-delay="400" class="space-y-4">
          <div  class="flex items-start">
            <div class="flex-shrink-0 mt-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-concierge-bell text-amber-400">
                <path d="M2 18a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v2H2v-2Z"/>
                <path d="M20 16a8 8 0 1 0-16 0"/>
                <path d="M12 4v4"/>
                <path d="M10 4h4"/>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-white font-medium">24/7 Concierge Service</p>
              <p class="text-white/70 text-sm">Personalized assistance whenever you need</p>
            </div>
          </div>
          <div  class="flex items-start">
            <div class="flex-shrink-0 mt-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check text-amber-400">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                <line x1="16" x2="16" y1="2" y2="6"/>
                <line x1="8" x2="8" y1="2" y2="6"/>
                <line x1="3" x2="21" y1="10" y2="10"/>
                <path d="m9 16 2 2 4-4"/>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-white font-medium">Easy Reservations</p>
              <p class="text-white/70 text-sm">Book rooms, tables, and services seamlessly</p>
            </div>
          </div>
          <div  class="flex items-start">
            <div class="flex-shrink-0 mt-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star text-amber-400">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-white font-medium">Exclusive Rewards</p>
              <p class="text-white/70 text-sm">Earn points with every stay and dining experience</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="w-1/2 flex justify-center items-center max-md:w-full">
      <div class="max-w-md w-full bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-2xl border border-white/20">
        <div class="mb-6 text-center flex justify-center items-center flex-col">
          <h2 class="text-2xl font-bold text-white">Sign in to your account</h2>
          <p class="text-white/80 mt-1">Enter your credentials to continue</p>
        </div>
        
        <div>
          <?php
          if (isset($_SESSION['login_error'])) {
              echo '<div class="alert alert-error mb-4">'.$_SESSION['login_error'].'</div>';
              unset($_SESSION['login_error']);
          }
          ?>
          
          <form action="php/login_backend.php" method="POST">
            <div class="mb-4">
              <label class="block text-white/90 text-sm font-medium mb-2" for="user_id">User - ID</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                  </svg>
                </div>
                <input id="user_id" type="text" class="w-full pl-10 pr-3 py-3 bg-white/5 border border-white/20 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent placeholder-white/50" placeholder="Your ID" required name="user_id">
              </div>
            </div>
            
            <div class="mb-6">
              <label class="block text-white/90 text-sm font-medium mb-2" for="password">Password</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <input id="password" type="password" class="w-full pl-10 pr-10 py-3 bg-white/5 border border-white/20 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent placeholder-white/50" placeholder="••••••••" required name="password">
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-white/50 hover:text-white focus:outline-none" onclick="togglePasswordVisibility()"><svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg><svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/></svg></button>
              </div>
            </div>
          
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center">
                <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-white/30 rounded bg-white/10">
                <label for="remember-me" class="ml-2 block text-sm text-white/80">Remember me</label>
              </div>
              <div class="text-sm">
                <a href="#" class="font-medium text-blue-400 hover:text-blue-300">Forgot password?</a>
              </div>
            </div>
            
            <button type="submit" class="w-full btn-primary btn">Sign in</button>
          </form>
          
          <div class="mt-6">
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-white/20"></div>
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-transparent text-white/70">
                  Don't have an account?
                </span>
              </div>
            </div>
            
            <div class="mt-4 text-center">
              <a href="#" class="font-medium text-blue-400 hover:text-blue-300">
                Create new account
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<dialog id="otp_modal" class="modal modal-middle sm:modal-middle">
  <div class="modal-box w-full max-w-md bg-white/10 backdrop-blur-lg p-6 sm:p-8 rounded-xl shadow-2xl border border-white/20">
    <div class="mb-6 sm:mb-8 text-center flex justify-center items-center flex-col">
      <div class="bg-white/10 p-3 rounded-full mb-3 sm:mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 sm:h-8 sm:w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h2 class="text-xl sm:text-2xl font-bold text-white">OTP Verification</h2>
      <p class="text-sm sm:text-base text-white/80 mt-1 sm:mt-2">Enter the 6-digit code sent to your device</p>
    </div>
    
    <div>
      <form id="otpForm" action="php/authenticate_password.php" method="POST">
        <div class="flex justify-between mb-6 sm:mb-8 gap-2 sm:gap-3">
          <input type="text" name="otp1" maxlength="1" class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" oninput="moveToNext(this, 'otp2')" autocomplete="off" required inputmode="numeric" pattern="[0-9]*">
          <input type="text" name="otp2" maxlength="1" class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" oninput="moveToNext(this, 'otp3')" autocomplete="off" required inputmode="numeric" pattern="[0-9]*">
          <input type="text" name="otp3" maxlength="1" class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" oninput="moveToNext(this, 'otp4')" autocomplete="off" required inputmode="numeric" pattern="[0-9]*">
          <input type="text" name="otp4" maxlength="1" class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" oninput="moveToNext(this, 'otp5')" autocomplete="off" required inputmode="numeric" pattern="[0-9]*">
          <input type="text" name="otp5" maxlength="1" class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" oninput="moveToNext(this, 'otp6')" autocomplete="off" required inputmode="numeric" pattern="[0-9]*">
          <input type="text" name="otp6" maxlength="1" class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" oninput="moveToNext(this, '')" autocomplete="off" required inputmode="numeric" pattern="[0-9]*">
        </div>
        
        <div class="flex items-center justify-center mb-6 sm:mb-8">
          <p id="countdown" class="text-sm sm:text-base text-white/80">Resend OTP in 02:00</p>
          <button id="resendBtn" type="button" class="ml-2 text-sm sm:text-base font-medium text-blue-400 hover:text-blue-300 hidden" onclick="resendOTP()">
            Resend
          </button>
        </div>
        
        <button type="submit" class="w-full btn-primary btn">Verify</button>
      </form>
      
      <div class="mt-4 sm:mt-6 text-center">
        <button onclick="otp_modal.close()" class="text-sm sm:text-base font-medium text-blue-400 hover:text-blue-300 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Login
        </button>
      </div>
    </div>
  </div>
  <form method="dialog" class="modal-backdrop">
    <button>close</button>
  </form>
</dialog>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>

<script>
// Password Visibility Toggle
function togglePasswordVisibility() {
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eye-icon');
  const eyeSlashIcon = document.getElementById('eye-slash-icon');

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.classList.add('hidden');
    eyeSlashIcon.classList.remove('hidden');
  } else {
    passwordInput.type = 'password';
    eyeIcon.classList.remove('hidden');
    eyeSlashIcon.classList.add('hidden');
  }
}

// OTP Modal, Inputs, and Countdown Logic
document.addEventListener('DOMContentLoaded', function() {
  const otpModal = document.getElementById('otp_modal');
  const otpInputs = document.querySelectorAll('.otp-input');
  const countdownEl = document.getElementById('countdown');
  const resendBtn = document.getElementById('resendBtn');
  let timeLeft = 120;
  let timer;

  // Function to show the modal
  function showOTPModal() {
      otpModal.showModal();
      startCountdown();
  }

  // Function to start the countdown
  function startCountdown() {
    timeLeft = 120;
    clearInterval(timer); // Clear any existing timer
    updateCountdown();
    timer = setInterval(updateCountdown, 1000);
  }

  // Check if OTP modal needs to be shown based on PHP session variable
  <?php if (isset($_SESSION['auth_needed']) && isset($_SESSION['email_for_verification'])): ?>
    showOTPModal();
  <?php endif; ?>

  // Function to update the countdown timer display
  function updateCountdown() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    countdownEl.textContent = `Resend OTP in ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeLeft <= 0) {
      clearInterval(timer);
      countdownEl.classList.add('hidden');
      resendBtn.classList.remove('hidden');
    } else {
      timeLeft--;
    }
  }

  // Function to handle resend OTP
  function resendOTP() {
    // This part requires an AJAX call to a PHP script that resends the OTP
    // For now, we'll just simulate it and restart the countdown.
    alert('New OTP has been sent to your device');
    
    countdownEl.classList.remove('hidden');
    resendBtn.classList.add('hidden');
    startCountdown();
    
    // Clear OTP fields
    otpInputs.forEach(input => {
      input.value = '';
    });
    otpInputs[0].focus();
  }
  
  // Expose resendOTP to the global scope so the onclick handler works
  window.resendOTP = resendOTP;

  // OTP Input Navigation & Paste Handling
  window.moveToNext = function(current, nextFieldId) {
    if (current.value.length >= current.maxLength) {
      if (nextFieldId) {
        document.getElementsByName(nextFieldId)[0].focus();
      }
    }
    
    // Auto-submit if last field is filled
    if (current.name === 'otp6' && current.value.length === 1) {
      document.getElementById('otpForm').submit();
    }
  };

  // Handle paste event for OTP
  document.getElementById('otpForm').addEventListener('paste', function(e) {
    e.preventDefault();
    const pasteData = e.clipboardData.getData('text/plain').trim();
    if (/^\d{6}$/.test(pasteData)) {
      for (let i = 0; i < 6; i++) {
        otpInputs[i].value = pasteData[i];
      }
      otpInputs[5].focus();
      // Optional: auto-submit on paste
      document.getElementById('otpForm').submit();
    }
  });
  
  // Handle backspace/delete
  otpInputs.forEach((input, index) => {
    input.addEventListener('keydown', function(e) {
      if (e.key === 'Backspace' && !this.value && index > 0) {
        otpInputs[index - 1].focus();
      }
    });
  });
});
</script>
</body>
</html>