<?php

namespace App\Http\Controllers;

use App\Models\ArticleNews;
use App\Models\Author;
use App\Models\BannerAdvertisment;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{

    //
    public function index(){
        $categories = Category::all();

        // Mengambil 3 artikel non-featured terbaru
        $articles = ArticleNews::with(['category'])
        ->where('is_featured','not_featured')
        ->latest()
        ->take(3)
        ->get();

        // Mengambil 3 artikel featured secara acak
        $featured_articles = ArticleNews::with(['category'])
        ->where('is_featured','featured')
        ->inRandomOrder()
        ->take(3)
        ->get();

        $authors = Author::all();

        // Mengambil satu iklan banner aktif secara acak
        $bannerads = BannerAdvertisment::where('is_active','active')
        ->where('type','banner')
        ->inRandomOrder()
        ->first();

        // ===== LIFESTYLE ARTICLES =====
        $lifestyle_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Lifestyle');
        })
        ->where('is_featured','not_featured')
        ->latest()
        ->take(6)
        ->get();

        $lifestyle_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Lifestyle');
        })
        ->where('is_featured','featured')
        ->inRandomOrder()
        ->first();

        // ===== BUSINESS ARTICLES =====
        $business_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Business');
        })
        ->where('is_featured','not_featured')
        ->latest()
        ->take(6)
        ->get();

        $business_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Business');
        })
        ->where('is_featured','featured')
        ->inRandomOrder()
        ->first();
        
        // ===== BEAUTY ARTICLES =====

        $beauty_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Beauty');
        })
        ->where('is_featured','not_featured')
        ->latest()
        ->take(6)
        ->get();
        
        $beauty_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Beauty');
        })
        ->where('is_featured','featured')
        ->inRandomOrder()
        ->first();
        

        // ===== AUTOMOTIVE ARTICLES =====
        $automotive_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
        ->where('is_featured','featured')
        ->inRandomOrder()
        ->first();

        $automotive_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
        ->where('is_featured','not_featured')
        ->latest()
        ->take(6)
        ->get();


        return view('front.index', compact(
            'lifestyle_featured_articles',
            'lifestyle_articles',
            'business_featured_articles',
            'business_articles',
            'beauty_featured_articles',
            'beauty_articles',
            'automotive_featured_articles',
            'automotive_articles',
            'categories',
            'articles',
            'authors',
            'featured_articles',
            'bannerads'
        ));
    }

    /**
     * FUNGSI UNTUK MENAMPILKAN ARTIKEL BERDASARKAN KATEGORI.
     * Menggunakan Route Model Binding ($category) dan query yang difilter.
     */
    public function category(Category $category){
        
        // Query untuk mengambil artikel yang category_id-nya sesuai dengan kategori yang diakses.
        $category_articles = ArticleNews::where('category_id', $category->id)
                                    ->with(['author', 'category'])
                                    ->latest()
                                    ->paginate(12);

        $categories = Category::all();
        $bannerads = BannerAdvertisment::where('is_active','active')
        ->where('type','banner')
        ->inRandomOrder()
        ->first();

        // Variabel $category_articles sekarang dikirim ke view.
        return view('front.category', compact('category', 'category_articles', 'categories', 'bannerads'));
    }

    /**
     * FUNGSI UNTUK MENAMPILKAN ARTIKEL BERDASARKAN PENULIS.
     */
    public function author(Author $author){
        
        // Query untuk mengambil artikel berdasarkan author_id
        $author_articles = ArticleNews::where('author_id', $author->id)
                                     ->with(['category'])
                                     ->latest()
                                     ->paginate(12);

        $categories = Category::all();
        $bannerads = BannerAdvertisment::where('is_active','active')
        ->where('type','banner')
        ->inRandomOrder()
        ->first();

        // Variabel $author_articles sekarang dikirim ke view.
        return view('front.author', compact('author', 'author_articles', 'categories', 'bannerads'));
    }

    public function search(Request $request){

        $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
        ]);

        $categories = Category::all();

        $keyword = $request->keyword;

        $articles = ArticleNews::with(['category', 'author'])
        ->where('name', 'like', '%' . $keyword . '%')->paginate(6);

        return view('front.search', compact('articles', 'keyword', 'categories'));

    }

    public function details(ArticleNews $articleNews){
        $categories = Category::all();

        $articles = ArticleNews::with(['category'])
        ->where('is_featured','not_featured')
        ->where('id', '!=', $articleNews->id)
        ->latest()
        ->take(3)
        ->get();

        $bannerads = BannerAdvertisment::where('is_active','active')
        ->where('type','banner')
        ->inRandomOrder()
        ->first();

        $square_ads = BannerAdvertisment::where('type','square')
        ->where('is_active', 'active')
        ->inRandomOrder()
        ->take(2)
        ->get();

        if($square_ads->count() < 2) {
            $square_ads_1 = $square_ads->first();
            $square_ads_2 = $square_ads->first();
        } else {
            $square_ads_1 = $square_ads->get(0);
            $square_ads_2 = $square_ads->get(1);
        }

        $author_news = ArticleNews::where('author_id', $articleNews->author_id)
        ->where('id', '!=', $articleNews->id)
        ->inRandomOrder()
        ->get();

        return view('front.details', compact(
            'articleNews',
            'categories',
            'articles',
            'bannerads',
            'square_ads_1',
            'square_ads_2',
            'author_news'
        ));
    }
}