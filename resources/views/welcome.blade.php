<x-landing-layout>
    <section class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polygon points="50,0 100,0 50,100 0,100" />
                </svg>

                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Makan Siang Enak</span>
                            <span class="block text-orange-600 xl:inline">Tanpa Antri</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Pesan makananmu dari kelas, ambil di kantin saat istirahat. Solusi katering mahasiswa yang hemat waktu dan anti food-waste.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ url('/menus') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 md:py-4 md:text-lg transition-all">
                                    Pesan Sekarang
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="#how-it-works" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 md:py-4 md:text-lg transition-all">
                                    Cara Kerja
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1543353071-873f17a7a088?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1350&q=80" alt="Delicious Food Catering">
        </div>
    </section>

    <section class="py-12 bg-gray-50" id="features">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-orange-600 font-semibold tracking-wide uppercase">Kenapa ReadyEat?</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Lebih Cerdas dari Kantin Biasa
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Kami mendigitalkan pengalaman makan siangmu agar kamu bisa fokus belajar (atau nongkrong) tanpa pusing mikirin antrian.
                </p>
            </div>

            <div class="mt-10">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-orange-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Pre-Order Cepat</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Pesan H-1 atau pagi hari sebelum kuliah dimulai. Makananmu dijamin tersedia.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-orange-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 11H4v4h2v-4zM6 11V7a1 1 0 011-1h14a1 1 0 011 1v4H6z"></path></svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Ambil & Pergi</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Tunjukkan bukti pesanan/nama di counter "ReadyEat", ambil makananmu dalam hitungan detik.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-orange-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Zero Waste</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Kami memasak sesuai jumlah pesanan (kuota harian). Tidak ada makanan sisa yang terbuang.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white" id="how-it-works">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-12">Cara Pesan di ReadyEat</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div class="p-6">
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">1</div>
                    <h3 class="text-xl font-bold mb-2">Pilih Menu</h3>
                    <p class="text-gray-500">Buka website, pilih menu favoritmu untuk hari ini atau besok.</p>
                </div>
                <div class="p-6">
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">2</div>
                    <h3 class="text-xl font-bold mb-2">Bayar & Upload</h3>
                    <p class="text-gray-500">Bayar via QRIS/Transfer, lalu upload bukti bayar di website.</p>
                </div>
                <div class="p-6">
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">3</div>
                    <h3 class="text-xl font-bold mb-2">Verifikasi</h3>
                    <p class="text-gray-500">Admin memverifikasi pesananmu. Status berubah jadi "Siap Diambil".</p>
                </div>
                <div class="p-6">
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">4</div>
                    <h3 class="text-xl font-bold mb-2">Ambil</h3>
                    <p class="text-gray-500">Datang ke kantin, sebut nama/tunjukkan invoice, dan nikmati!</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-orange-600">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Lapar tapi malas antri?</span>
                <span class="block text-orange-200">Amankan kuota makan siangmu sekarang.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ url('/menus') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-orange-600 bg-white hover:bg-orange-50">
                        Lihat Menu
                    </a>
                </div>
                <div class="ml-3 inline-flex rounded-md shadow">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-700 hover:bg-orange-800">
                        Buat Akun
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-landing-layout>