<x-landing-layout>
    <div class="min-h-[80vh] flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100 max-w-lg w-full text-center">
            
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Berhasil!</h1>
            <p class="text-gray-500 mb-6">
                Kode Pesanan: <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded">{{ $order->invoice_code }}</span>
            </p>

            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-sm mb-8 text-left">
                <p class="font-bold mb-1">Langkah Terakhir:</p>
                <p>Pesanan Anda sedang menunggu verifikasi Admin. Untuk mempercepat proses, silakan kirim konfirmasi ke WhatsApp kami.</p>
            </div>

            <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20sudah%20pesan%20katering%20dengan%20kode%20*{{ $order->invoice_code }}*.%20Mohon%20diproses%20ya%21" 
               target="_blank"
               class="flex items-center justify-center w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition mb-4 gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                Konfirmasi via WhatsApp
            </a>

            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-orange-500 underline">Kembali ke Beranda</a>
        </div>
    </div>
</x-landing-layout>