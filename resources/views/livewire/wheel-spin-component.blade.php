<div class="text-center space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">عجلة الحظ: {{ $wheel->name }}</h2>

    @if(session()->has('error'))
        <div class="bg-red-100 text-red-600 p-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if($wonPrize)
        <div class="bg-green-100 text-green-800 p-4 rounded shadow">
            <h3 class="text-xl font-semibold">🎉 مبروك! ربحت:</h3>
            <p class="mt-2 text-lg">{{ $wonPrize->name }} (نوع: {{ $wonPrize->type }})</p>
        </div>
    @elseif($canSpin)
        <button wire:click="spin"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            🎯 اضغط للّف
        </button>
    @else
        <div class="text-gray-500">
            ⏳ لا يمكنك اللف الآن. يرجى المحاولة لاحقًا.
        </div>
    @endif
</div>
