<div>
    <div class="wheel-container min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-gray-100">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    {{ __('Wheel of Fortune') }}
                </h1>
                <p class="mt-3 text-xl text-gray-600">
                    {{ $wheel->name }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-6 sm:p-8">
                <!-- Wheel Visualization -->
                <div class="flex flex-col lg:flex-row items-center justify-between gap-10">
                    <div class="wheel-wrapper relative w-full max-w-md mx-auto">
                        <div
                            class="wheel relative w-full h-0 pb-[100%] rounded-full border-8 border-gray-200 overflow-hidden transition-transform duration-5000 ease-out"
                            style="transform: rotate({{ $rotation }}deg);"
                            x-data="{ rotation: @entangle('rotation') }"
                            x-init="$watch('rotation', (value) => { if(value === 0) Livewire.emit('spinReady') })"
                        >
                            @foreach($wheelSegments as $index => $segment)
                                <div
                                    class="wheel-segment absolute top-0 left-0 w-full h-full"
                                    style="
                                    transform: rotate({{ $index * (360 / count($wheelSegments)) }}deg);
                                    clip-path: polygon(50% 50%, 50% 0%, {{ 50 + 50 * sin(2 * pi() / count($wheelSegments)) }}% {{ 50 - 50 * cos(2 * pi() / count($wheelSegments)) }}%);
                                    background-color: {{ $segment['color'] }};
                                "
                                >
                                    <div
                                        class="prize-label absolute"
                                        style="
                                        transform: rotate({{ (360 / count($wheelSegments)) / 2 }}deg) translateY(-120%);
                                        transform-origin: 50% 50%;
                                        color: {{ $segment['textColor'] }};
                                    "
                                    >
                                        <span class="text-xs font-medium">{{ $segment['name'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="wheel-center absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-16 h-16 rounded-full bg-white border-8 border-gray-300 flex items-center justify-center z-10 shadow-inner">
                            <div class="w-6 h-6 rounded-full bg-gray-600"></div>
                        </div>

                        <div class="wheel-pointer absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 z-20">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 1L22 22H2L12 1Z" fill="#EF4444" stroke="#B91C1C" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Controls and Info -->
                    <div class="flex-1 space-y-6">
                        @if(Auth::check())
                            @if($canSpin && !$isSpinning)
                                <button
                                    wire:click="spin"
                                    class="spin-button w-full max-w-xs px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold text-xl rounded-full shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-3"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    {{ __('Spin the Wheel') }}
                                </button>
                            @else
                                <div class="bg-gray-100 p-4 rounded-lg border border-gray-200 text-center">
                                    <p class="text-gray-700 font-medium">
                                        @if($isSpinning)
                                            {{ __('Spinning...') }}
                                        @else
                                            {{ $spinMessage }}
                                        @endif
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="space-y-4">
                                <p class="text-gray-600">{{ __('Login to spin the wheel') }}</p>
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200"
                                >
                                    {{ __('Sign In') }}
                                </a>
                            </div>
                        @endif

                        <!-- Prize Display -->
                        @if($wonPrize)
                            <div class="mt-6 animate-pulse">
                                <div class="bg-gradient-to-r from-green-500 to-green-400 p-6 rounded-xl shadow-lg text-white text-center">
                                    <h3 class="text-2xl font-bold mb-2 flex items-center justify-center gap-2">
                                        🎉 {{ __('Congratulations!') }}
                                    </h3>
                                    <p class="text-xl">{{ __('You won:') }}</p>
                                    <p class="text-2xl font-bold mt-2">{{ $wonPrize->name }}</p>
                                    <p class="mt-3 text-green-100">
                                        {{ __('Prize type:') }}
                                        <span class="font-semibold">{{ ucfirst($wonPrize->type) }}</span>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .wheel {
                transition: transform 5s cubic-bezier(0.17, 0.67, 0.12, 0.99);
            }

            .wheel-segment {
                transform-origin: 50% 50%;
            }

            .prize-label {
                width: 100%;
                text-align: center;
                left: 0;
                top: 50%;
                padding: 0 20%;
                box-sizing: border-box;
            }

            .spin-button {
                box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            }

            .spin-button:hover {
                box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('spin-complete', (prizeId) => {
                    setTimeout(() => {
                        Livewire.dispatch('notify', {
                            message: @json(__('Congratulations on your prize!')),
                            type: 'success'
                        });
                    }, 5500);
                });
            });

            // Smooth rotation animation
            Livewire.on('spin', () => {
                const wheel = document.querySelector('.wheel');
                const finalRotation = @this.finalRotation;

                // Reset to initial position before spinning
                wheel.style.transition = 'none';
                wheel.style.transform = 'rotate(0deg)';

                // Force reflow to apply reset
                void wheel.offsetWidth;

                // Start spinning animation
                wheel.style.transition = 'transform 5s cubic-bezier(0.17, 0.67, 0.12, 0.99)';
                wheel.style.transform = `rotate(${-finalRotation}deg)`;
            });
        </script>
    @endpush
</div>
