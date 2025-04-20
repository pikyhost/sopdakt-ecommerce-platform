<div class="d-flex flex-column align-items-center justify-content-center py-5">

    <h2 class="mb-4 fw-bold text-primary h4">🎡 عجلة الحظ: {{ $wheel->name }}</h2>

    @if(session()->has('error'))
        <div class="alert alert-danger w-75 text-center">
            {{ session('error') }}
        </div>
    @endif

    @if($wonPrize)
        <div class="alert alert-success w-75 text-center">
            <h4 class="fw-bold">🎉 مبروك! ربحت:</h4>
            <p class="mb-0">{{ $wonPrize->name }} (نوع: {{ $wonPrize->type }})</p>
        </div>
    @endif

    @if($canSpin)
        <div class="position-relative" style="width: 300px; height: 300px;">
            <canvas id="wheelCanvas" width="300" height="300"
                    class="border border-3 border-primary rounded-circle"></canvas>
            <div class="position-absolute top-0 start-50 translate-middle-x text-danger fs-3" style="z-index: 10;">
                ▲
            </div>
        </div>

        <button class="btn btn-primary mt-4" id="spinBtn">
            🎯 اضغط للّف
        </button>
    @else
        <div class="text-muted mt-3">
            ⏳ لا يمكنك اللف الآن. يرجى المحاولة لاحقًا.
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById("wheelCanvas");
            if (!canvas) return;

            const ctx = canvas.getContext("2d");
            const prizes = @json($wheel->prizes()->where('is_available', true)->pluck('name'));
            const colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#FF9F40"];
            const arcSize = 2 * Math.PI / prizes.length;

            function drawWheel() {
                for (let i = 0; i < prizes.length; i++) {
                    const angle = i * arcSize;
                    ctx.beginPath();
                    ctx.fillStyle = colors[i % colors.length];
                    ctx.moveTo(150, 150);
                    ctx.arc(150, 150, 150, angle, angle + arcSize);
                    ctx.lineTo(150, 150);
                    ctx.fill();

                    ctx.save();
                    ctx.translate(150, 150);
                    ctx.rotate(angle + arcSize / 2);
                    ctx.textAlign = "right";
                    ctx.fillStyle = "#fff";
                    ctx.font = "bold 14px sans-serif";
                    ctx.fillText(prizes[i], 140, 10);
                    ctx.restore();
                }
            }

            drawWheel();

            let spinning = false;
            document.getElementById("spinBtn")?.addEventListener("click", function () {
                if (spinning) return;
                spinning = true;
                document.getElementById("spinBtn").disabled = true;

                const spins = Math.floor(Math.random() * 5) + 5;
                const prizeIndex = Math.floor(Math.random() * prizes.length);
                const finalDeg = (360 * spins) + (360 / prizes.length * prizeIndex) + (360 / (2 * prizes.length));

                canvas.style.transition = 'transform 5s cubic-bezier(0.33, 1, 0.68, 1)';
                canvas.style.transform = `rotate(${finalDeg}deg)`;

                setTimeout(() => {
                @this.call('spin')
                }, 5100);
            });
        });
    </script>
</div>
