<div>
    <div
        x-data="wheelSpinComponent()"
        x-init="init()"
        class="flex flex-col items-center justify-center space-y-8 py-12">

        <h2 class="text-3xl font-bold text-gray-800">🎡 عجلة الحظ: {{ $wheel->name }}</h2>

        <!-- رسائل -->
        @if(session()->has('error'))
            <div class="bg-red-100 text-red-600 p-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- عرض الجائزة إن وُجدت -->
        @if($wonPrize)
            <div class="bg-green-100 text-green-800 p-4 rounded shadow text-center">
                <h3 class="text-xl font-semibold">🎉 مبروك! ربحت:</h3>
                <p class="mt-2 text-lg font-bold">{{ $wonPrize->name }}</p>
                <p class="text-sm text-gray-700">النوع: {{ $wonPrize->type }}</p>
            </div>
        @endif

        <!-- العجلة -->
        <div class="relative">
            <div
                id="wheel"
                class="w-64 h-64 rounded-full border-8 border-blue-500 relative"
                :class="{ 'animate-spin-custom': spinning }"
                @animationend="onSpinEnd">
                <div class="absolute inset-0 flex items-center justify-center text-3xl text-blue-800 font-bold">
                    🎯
                </div>
            </div>
            <div class="absolute top-[50%] left-1/2 -translate-x-1/2 -translate-y-[60%] text-red-600 text-4xl font-bold">
                ▲
            </div>
        </div>

        <!-- زر اللف -->
        <div>
            @if($canSpin && !$wonPrize)
                <button
                    @click="spin"
                    class="mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    لف العجلة 🎯
                </button>
            @elseif(!$canSpin && !$wonPrize)
                <p class="text-gray-500 mt-4">⏳ لا يمكنك اللف الآن. حاول لاحقاً.</p>
            @endif
        </div>
    </div>

    <!-- Alpine Script -->
    <script>
        function wheelSpinComponent() {
            return {
                spinning: false,
                init() {},

                spin() {
                    if (this.spinning) return;
                    this.spinning = true;

                    // تشغيل الدوران ثم إعلام Livewire بعد 3 ثواني
                    setTimeout(() => {
                    @this.call('spin');
                    }, 3000);
                },

                onSpinEnd() {
                    this.spinning = false;
                }
            };
        }
    </script>

    <!-- Custom animation class -->
    <style>
        .animate-spin-custom {
            animation: spinWheel 3s ease-out;
        }

        @keyframes spinWheel {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(1440deg); } /* 4 لفات */
        }
    </style>

</div>
