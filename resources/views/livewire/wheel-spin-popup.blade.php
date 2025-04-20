<div>
    @if ($wheel)
        <!-- Trigger button or automatic show (optional) -->
        {{-- You can auto-show or use a button to trigger this modal --}}

        <!-- Modal -->
        <div class="modal fade show d-block" id="wheelModal" tabindex="-1" style="background-color: rgba(0,0,0,0.4); animation: slideInUp 0.5s;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                <div class="modal-content rounded-4 p-4 text-center">
                    <h4 class="mb-3">🎁 عجلة الحظ 🎉</h4>

                    <!-- Wheel Image / Spinner -->
                    <div class="position-relative mb-4">
                        <img src="{{ asset('images/wheel.png') }}" alt="Wheel" id="wheel" style="width: 300px; transition: transform 3s cubic-bezier(0.33, 1, 0.68, 1);" class="mx-auto">
                        <img src="{{ asset('images/pointer.png') }}" alt="Pointer" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -140%); width: 40px;">
                    </div>

                    <!-- Spin Button -->
                    <button wire:click="spin" wire:loading.attr="disabled" class="btn btn-primary btn-lg">
                        🎯 جرّب حظك الآن
                    </button>

                    <!-- Result -->
                    @if ($winnerPrize)
                        <div class="alert alert-success mt-4 animate__animated animate__fadeIn">
                            فزت بـ: <strong>{{ $winnerPrize->name }}</strong>
                        </div>
                    @endif

                    @error('spin')
                    <div class="alert alert-warning mt-3">{{ $message }}</div>
                    @enderror

                    <!-- Close Button -->
                    <button class="btn btn-link mt-2" onclick="closePopup()">❌ إغلاق</button>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        #wheelModal {
            z-index: 1055;
        }

        .modal-content {
            animation: animate__fadeInUp 0.5s ease;
        }
    </style>
@endpush

@push('scripts')
    <script>
        Livewire.on('spin-start', () => {
            const wheel = document.getElementById('wheel');
            const angle = Math.floor(Math.random() * 360) + 720; // Random 2+ full rotations
            wheel.style.transform = `rotate(${angle}deg)`;
        });

        function closePopup() {
            const modal = document.getElementById('wheelModal');
            modal.classList.add('animate__fadeOutDown');
            setTimeout(() => {
                modal.remove();
            }, 500);
        }
    </script>
@endpush
