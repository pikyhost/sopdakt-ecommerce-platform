<div>
    <div>
        <!-- Button to trigger modal -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#wheelModal">
            🎁 جرب حظك
        </button>

        <!-- Bootstrap Modal -->
        <div wire:ignore.self class="modal fade" id="wheelModal" tabindex="-1" aria-labelledby="wheelModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-header border-0">
                        <h5 class="modal-title w-100" id="wheelModalLabel">عجلة الحظ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="wheel" style="width: 300px; height: 300px; margin: 0 auto; border: 10px solid #ccc; border-radius: 50%;">
                            <!-- Placeholder canvas for drawing wheel (via JS) -->
                            <canvas id="wheelCanvas" width="300" height="300"></canvas>
                        </div>

                        <button wire:click="spin" class="btn btn-success mt-4" id="spinBtn" @disabled($spinning)>
                            اضغط للدوران
                        </button>

                        @if ($showResult && $winnerPrize)
                            <div class="mt-3 alert alert-success">
                                مبروك! ربحت: <strong>{{ $winnerPrize->name }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:load', () => {
                const canvas = document.getElementById('wheelCanvas');
                const ctx = canvas.getContext('2d');
                let rotation = 0;

                function drawWheel(prizes) {
                    const num = prizes.length;
                    const angle = 2 * Math.PI / num;
                    prizes.forEach((prize, i) => {
                        ctx.beginPath();
                        ctx.fillStyle = i % 2 === 0 ? '#f8b400' : '#4caf50';
                        ctx.moveTo(150, 150);
                        ctx.arc(150, 150, 150, i * angle, (i + 1) * angle);
                        ctx.fill();

                        // Text
                        ctx.save();
                        ctx.translate(150, 150);
                        ctx.rotate(i * angle + angle / 2);
                        ctx.fillStyle = '#fff';
                        ctx.font = 'bold 14px sans-serif';
                        ctx.fillText(prize.name, 60, 0);
                        ctx.restore();
                    });
                }

                Livewire.on('spin-start', ({ prizeId }) => {
                    const prizes = @json(\App\Models\WheelPrize::where('is_available', true)->get(['id', 'name'])->toArray());

                    const index = prizes.findIndex(p => p.id == prizeId);
                    const sliceAngle = 360 / prizes.length;
                    const finalAngle = 360 * 5 + (360 - (index * sliceAngle + sliceAngle / 2)); // spin 5 full rotations then land on prize

                    let current = 0;
                    const interval = setInterval(() => {
                        rotation += 10;
                        canvas.style.transform = `rotate(${rotation}deg)`;
                        current += 10;
                        if (current >= finalAngle) {
                            clearInterval(interval);
                            Livewire.dispatch('show-result');
                        }
                    }, 10);
                });

                Livewire.on('spin-finished', ({ prizeId }) => {
                    setTimeout(() => {
                        Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).showResult = true;
                    }, 1000);
                });

                drawWheel(@json(\App\Models\WheelPrize::where('is_available', true)->get(['id', 'name'])->toArray()));
            });
        </script>
    @endpush
    
</div>
