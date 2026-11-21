<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;

// Public landing page
Route::get('/', [LandingController::class, 'index'])
    ->name('landing');

// Student homepage
// Biarkan Route::get('/home', ...) yang sudah ada

//Route::redirect('/', '/home');

Route::get('/home', function (Request $request) {
    // Demo content only for Task 6. Another teammate can connect actual
    // PostgreSQL/Eloquent records in the later implementation phase.
    $listings = collect([
        ['title' => 'Macbook Air M1',      'category' => 'Electronics', 'price' => 'Rp 50.000/day', 'owner' => 'Budi S.',  'initials' => 'BS', 'status' => 'Available'],
        ['title' => 'Textbook Calculus',  'category' => 'Books',       'price' => 'Rp 15.000/day', 'owner' => 'Dewi L.',  'initials' => 'DL', 'status' => 'Available'],
        ['title' => 'Canon Camera',       'category' => 'Electronics', 'price' => 'Rp 75.000/day', 'owner' => 'Rizky A.', 'initials' => 'RA', 'status' => 'Booked'],
        ['title' => 'Camping Tent',       'category' => 'Equipment',   'price' => 'Rp 40.000/day', 'owner' => 'Sari M.',  'initials' => 'SM', 'status' => 'Available'],
        ['title' => 'Jaket Hoodie',       'category' => 'Fashion',     'price' => 'Rp 20.000/day', 'owner' => 'Andi P.',  'initials' => 'AP', 'status' => 'Available'],
        ['title' => 'Road Bike',          'category' => 'Sports',      'price' => 'Rp 60.000/day', 'owner' => 'Tono W.',  'initials' => 'TW', 'status' => 'Booked'],
    ]);

    $categories = ['All', 'Books', 'Electronics', 'Fashion', 'Sports', 'Equipment'];
    $selectedCategory = $request->query('category', 'All');
    $search = trim((string) $request->query('q', ''));

    if (! in_array($selectedCategory, $categories, true)) {
        $selectedCategory = 'All';
    }

    if ($selectedCategory !== 'All') {
        $listings = $listings->where('category', $selectedCategory);
    }

    if ($search !== '') {
        $listings = $listings->filter(function (array $listing) use ($search) {
            return stripos($listing['title'], $search) !== false
                || stripos($listing['category'], $search) !== false
                || stripos($listing['owner'], $search) !== false;
        });
    }

    return view('home', [
        'studentName' => 'Dyandra',   // Replace with auth()->user()->name later
        'studentInitials' => 'DY',
        'credits' => 178,            // Illustrative only
        'totalAvailable' => 356,     // Illustrative only
        'categories' => $categories,
        'selectedCategory' => $selectedCategory,
        'search' => $search,
        'listings' => $listings->values(),
    ]);
})->name('home');

// Circlo sharing module: UI-11 to UI-13.
require __DIR__.'/circlo-sharing.php';
