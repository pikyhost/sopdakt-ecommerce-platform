<div class="wheel-container d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 90vh; background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);">

    <div class="text-center mb-5">
        <div class="wheel-badge mb-3">جولة الحظ</div>
        <h1 class="fw-bold text-gradient mb-2">عجلة الجوائز</h1>
        <p class="text-muted fs-5">
            @if($remainingSpins > 0)
                لديك {{ $remainingSpins }} محاولة متبقية
            @endif
        </p>
    </div>

    <div class="message-container mb-4" style="width: 100%; max-width: 550px;">
        @if(session()->has('error'))
            <div class="alert alert-danger alert-elegant text-center py-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($wonPrize)
            <div class="prize-card animate__animated animate__rubberBand">
                <div class="prize-ribbon">جائزة</div>
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <i class="fas fa-trophy prize-icon"></i>
                    <h3 class="mb-0 mx-2">مبروك!</h3>
                    <i class="fas fa-gift prize-icon"></i>
                </div>
                <h4 class="prize-name">{{ $wonPrize->name }}</h4>
                <div class="prize-type">{{ $wonPrize->type }}</div>
                <div class="confetti"></div>
            </div>
        @endif
    </div>

    @if($canSpin && !$hasReachedSpinLimit)
        <div class="wheel-wrapper position-relative mb-5">
            <div class="wheel-outer-circle"></div>
            <div class="wheel-inner-circle"></div>
            <canvas id="wheelCanvas" width="350" height="350" class="wheel-main"></canvas>
            <div class="wheel-pointer">
                <div class="pointer-top"></div>
                <div class="pointer-base"></div>
            </div>
        </div>

        <button class="spin-button btn-glow" id="spinBtn">
            <span class="spin-text">إبدأ التدوير</span>
            <span class="spin-icon"><i class="fas fa-redo-alt"></i></span>
        </button>
    @elseif($hasReachedSpinLimit)
        <div class="cooldown-message">
            <div class="trophy-icon mb-3">
                <i class="fas fa-check-circle" style="font-size: 3rem; color: #10B981;"></i>
            </div>
            <h4 class="mb-3">لقد استنفذت جميع محاولاتك</h4>
            <p class="text-muted">شكراً لمشاركتك في هذه العجلة</p>
        </div>
    @else
        <div class="cooldown-message">
            <div class="clock-loader"></div>
            <p>سيتم تجديد المحاولات خلال: <span class="fw-bold">{{ now()->addHours($wheel->spins_duration)->diffForHumans() }}</span></p>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById("wheelCanvas");
            const spinBtn = document.getElementById("spinBtn");

            if (!canvas || !spinBtn) return;

            const ctx = canvas.getContext("2d");

            const prizes = @json($wheel->prizes()->where('is_available', true)->pluck('name'));
            const prizeCount = prizes.length;

            const colors = ["#3B82F6", "#8B5CF6", "#EC4899", "#F59E0B", "#10B981", "#EF4444", "#6366F1", "#F97316"];

            const arcSize = 2 * Math.PI / prizeCount;
            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;
            const radius = canvas.width / 2 - 10;

            function drawWheel(rotation = 0) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (let i = 0; i < prizeCount; i++) {
                    const startAngle = i * arcSize + rotation;
                    const endAngle = (i + 1) * arcSize + rotation;

                    ctx.beginPath();
                    ctx.moveTo(centerX, centerY);
                    ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                    ctx.closePath();
                    ctx.fillStyle = colors[i % colors.length];
                    ctx.fill();

                    ctx.save();
                    ctx.translate(centerX, centerY);
                    ctx.rotate(startAngle + arcSize / 2);
                    ctx.textAlign = "right";
                    ctx.fillStyle = "#fff";
                    ctx.font = "14px Arial";
                    ctx.fillText(prizes[i], radius - 10, 5);
                    ctx.restore();
                }
            }

            drawWheel();

            function spinWheel() {
                const spinAngle = 360 * 5;
                const winningIndex = Math.floor(Math.random() * prizeCount);
                const stopAngle = 360 / prizeCount * winningIndex + 360 / prizeCount / 2;
                const totalRotation = spinAngle + stopAngle;
                const duration = 4000;
                const frameRate = 1000 / 60;
                const totalFrames = duration / frameRate;

                const start = performance.now();

                function animate(timestamp) {
                    const progress = Math.min((timestamp - start) / duration, 1);
                    const easing = easeOutCubic(progress);
                    const angle = (totalRotation * easing * Math.PI / 180) % (2 * Math.PI);

                    drawWheel(angle);

                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        setTimeout(() => {
                            Livewire.dispatch('spin');
                        }, 300);
                    }
                }

                requestAnimationFrame(animate);
            }

            function easeOutCubic(t) {
                return (--t) * t * t + 1;
            }

            spinBtn.addEventListener('click', function () {
                spinBtn.disabled = true;
                spinWheel();
            });
        });
    </script>

    <style>
        /* Base Styles */
        .wheel-container {
            font-family: 'Tajawal', sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Text Gradient */
        .text-gradient {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 2.5rem;
        }

        /* Wheel Badge */
        .wheel-badge {
            display: inline-block;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        /* Alert Styling */
        .alert-elegant {
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #fff, #f8f9fa);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #dc3545;
        }

        /* Prize Card */
        .prize-card {
            position: relative;
            background: white;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(245, 245, 245, 0.9));
            backdrop-filter: blur(10px);
        }

        .prize-ribbon {
            position: absolute;
            top: 10px;
            right: -30px;
            background: #3b82f6;
            color: white;
            padding: 0.25rem 2rem;
            transform: rotate(45deg);
            font-size: 0.8rem;
            font-weight: bold;
            width: 120px;
            text-align: center;
        }

        .prize-icon {
            font-size: 1.5rem;
            color: #f59e0b;
        }

        .prize-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1e293b;
            margin: 0.5rem 0;
        }

        .prize-type {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
        }

        /* Wheel Styling */
        .wheel-wrapper {
            width: 350px;
            height: 350px;
            position: relative;
        }

        .wheel-outer-circle {
            position: absolute;
            width: 110%;
            height: 110%;
            top: -5%;
            left: -5%;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
            z-index: 1;
        }

        .wheel-inner-circle {
            position: absolute;
            width: 30px;
            height: 30px;
            background: white;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 4;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .wheel-main {
            position: relative;
            z-index: 2;
            border-radius: 50%;
            border: 8px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15),
            inset 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .wheel-pointer {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            width: 40px;
            height: 60px;
        }

        .pointer-top {
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-bottom: 30px solid #ef4444;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .pointer-base {
            width: 20px;
            height: 20px;
            background: #dc2626;
            margin: 0 auto;
            border-radius: 50%;
        }

        /* Spin Button */
        .spin-button {
            position: relative;
            border: none;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            color: white;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .spin-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.6);
        }

        .spin-button:active {
            transform: translateY(1px);
        }

        .spin-text {
            position: relative;
            z-index: 2;
        }

        .spin-icon {
            margin-right: 10px;
            transition: transform 0.5s;
        }

        .btn-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.3), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .btn-glow:hover::before {
            transform: translateX(100%);
        }

        /* Cooldown Message */
        .cooldown-message {
            text-align: center;
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .clock-loader {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            border: 3px solid;
            border-color: #3b82f6 #3b82f6 transparent transparent;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
            margin-bottom: 1rem;
        }

        .clock-loader::after,
        .clock-loader::before {
            content: '';
            box-sizing: border-box;
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto;
            border: 3px solid;
            border-color: transparent transparent #8b5cf6 #8b5cf6;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-sizing: border-box;
            animation: rotationBack 0.5s linear infinite;
            transform-origin: center center;
        }

        .clock-loader::before {
            width: 32px;
            height: 32px;
            border-color: #3b82f6 #3b82f6 transparent transparent;
            animation: rotation 1.5s linear infinite;
        }

        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes rotationBack {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(-360deg); }
        }

        /* Confetti Effect */
        .confetti {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
            overflow: hidden;
        }

        .confetti:after, .confetti:before {
            content: "";
            position: absolute;
            width: 10px;
            height: 10px;
            background: #f59e0b;
            top: 10%;
            left: 50%;
            opacity: 0;
            animation: confetti 3s ease-in-out infinite;
        }

        .confetti:before {
            background: #3b82f6;
            left: 30%;
            animation-delay: 0.5s;
        }

        @keyframes confetti {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(500px) rotate(360deg); opacity: 0; }
        }
    </style>
</div>
