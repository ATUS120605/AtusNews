<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="{{ asset('output.css')}}" rel="stylesheet" />
        <link href="{{ asset('main.css')}}" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap"
            rel="stylesheet" />

        {{-- menambahkan cdn.tailwindcss karena uppercase atau huruf besar semuanya tidak ada belum disediakan --}}
        <script src="https://cdn.tailwindcss.com"></script>
	</head>
	<@extends('front.master')
@section('content')
	<body class="font-[Poppins]">
		<x-navbar/>
		 <nav id="Category" class="max-w-[1130px] mx-auto flex justify-center items-center gap-4 mt-4 overflow-x-auto pb-2 sm:overflow-visible px-4 sm:px-0">
            
            {{-- 1. KATEGORI UTAMA (Hanya 5 item) --}}
            @foreach ($categories->take(5) as $category) 
                <a href="{{ route('front.category', $category->slug) }}" 
                   class="rounded-full p-[10px_16px] flex gap-[10px] font-semibold transition-all duration-300 border border-[#007BFF] hover:ring-2 hover:ring-[#007BFF] flex-shrink-0">
                    
                    <div class="flex w-6 h-6 shrink-0">
                        <img src="{{ route('private.file', $category->icon) }}" alt="icon" />
                    </div>
                    <span>{{ $category->name }}</span>
                </a>
            @endforeach
            
            {{-- 2. TOMBOL DROPDOWN UNTUK KATEGORI SISA --}}
            @php
                // Ambil semua kategori setelah 5 item pertama
                $remainingCategories = $categories->skip(5);
            @endphp

            @if ($remainingCategories->isNotEmpty())
            {{-- Menggunakan Alpine.js untuk mengontrol dropdown --}}
            <div x-data="{ open: false }" @click.away="open = false" class="relative flex-shrink-0">
                
                {{-- TOMBOL PEMICU DROPDOWN (Menggunakan styling pill yang sama) --}}
                <button @click="open = ! open" class="rounded-full p-[10px_16px] flex gap-[10px] font-semibold transition-all duration-300 border border-[#007BFF] hover:ring-2 hover:ring-[#007BFF] flex-shrink-0">
                    <div class="flex w-6 h-6 shrink-0">
                        {{-- Ikon 'Semua Kategori' (Grid) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-[#1A1A1A]"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </div>
                    <span>Semua Kategori</span>
                </button>

                {{-- KOTAK DROPDOWN --}}
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 origin-top-right rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 p-2"
                     role="menu" aria-orientation="vertical" tabindex="-1">
                    
                    <div class="py-1">
                        @foreach ($remainingCategories as $rc)
                        {{-- Link setiap kategori sisa --}}
                        <a href="{{ route('front.category', $rc->slug) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <img src="{{ route('private.file', $rc->icon) }}" alt="icon" class="w-4 h-4 mr-2" />
                            {{ $rc->name }}
                        </a>
                        @endforeach
                    </div>
                    
                </div>
            </div>
            @endif

        </nav> 
		<section id="heading" class="max-w-[1130px] mx-auto flex items-center flex-col gap-[30px] mt-[70px]">
			<h1 class="text-4xl leading-[45px] font-bold text-center">
				Explore Hot Trending <br />
				Good News Today
			</h1>
			<form action="{{ route('front.search')  }}" method="GET" >
				<label for="search-bar" class="w-[500px] flex p-[12px_20px] transition-all duration-300 gap-[10px] ring-1 ring-[#007BFF] focus-within:ring-2 focus-within:ring-#007BFF] rounded-[50px] group">
					<div class="flex w-5 h-5 shrink-0">
						<img src="assets/images/icons/search-normal.svg" alt="icon" />
					</div>
					<input
						autocomplete="off"
						type="text"
						id="search-bar"
						name="keyword"
						placeholder="Search hot trendy news today..."
						class="appearance-none font-semibold placeholder:font-normal placeholder:text-[#A3A6AE] outline-none focus:ring-0 w-full"
					/>
				</label>
			</form>
		</section>
		<section id="search-result" class="max-w-[1130px] mx-auto flex items-start flex-col gap-[30px] mt-[70px] mb-[100px]">
			<h2 class="text-[26px] leading-[39px] font-bold">Search Result: <span>{{ ucfirst($keyword) }}</span></h2>
			<div id="search-cards" class="grid grid-cols-3 gap-[30px]">
                @forelse ($articles as $article)
                <a href="{{ route('front.details', $article->slug) }}" class="card">
                    <div
                        class="flex flex-col gap-4 p-[26px_20px] transition-all duration-300 ring-1 ring-[#007BFF] hover:ring-2 hover:ring-[#007BFF] rounded-[20px] overflow-hidden bg-white">
                        <div class="thumbnail-container h-[200px] relative rounded-[20px] overflow-hidden">
                            <div
                                class="badge absolute left-5 top-5 bottom-auto right-auto flex p-[8px_18px] bg-white rounded-[50px]">
                                {{-- uppercase agar huruf besar semua --}}
                                <p class="text-xs leading-[18px] font-bold uppercase">{{ $article->category->name }}</p>
                            </div>
                            <img src="{{ route('private.file', $article->thumbnail) }}" alt="thumbnail photo"
                                class="object-cover w-full h-full" />
                        </div>
                        <div class="flex flex-col gap-[6px]">
                            <h3 class="text-lg leading-[27px] font-bold">
                                {{ substr($article->name, 0, 55) }}{{ strlen($article->name) > 55 ? '...':''}}
                            </h3>
                            <p class="text-sm leading-[21px] text-[#A3A6AE]">
                                {{ $article->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </a>
                @empty
                    <p>belum ada artikel dengan keyword tersebut</p>
                @endforelse
		</section>
	</body>
@endsection
