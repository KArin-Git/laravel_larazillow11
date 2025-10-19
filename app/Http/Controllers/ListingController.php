<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia(
            'Listing/Index',
            [
                'listings' => Listing::all()
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Listing/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Listing::create(
            $request->validate([
                'beds' => 'required|integer|min:0|max:20',
                'baths'=> 'required|integer|min:0|max:20',
                'area' => 'required|integer|min:150|max:15000',
                'city' => 'required|max:255',
                'code' => 'required|max:255',
                'street' => 'required|max:255',
                'street_number' => 'required|max:255',
                'price' => 'required|integer|min:1|max:10000000',
            ])
        );
        return redirect()->route('listing.index')
            ->with('message', 'Listing was created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        return inertia(
            'Listing/Show',
            [
                'listing' => $listing
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        return inertia(
            'Listing/Edit',
            [
                'listing' => $listing
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        $listing->update(
            $request->validate([
                'beds' => 'required|integer|min:0|max:20',
                'baths'=> 'required|integer|min:0|max:20',
                'area' => 'required|integer|min:150|max:15000',
                'city' => 'required|max:255',
                'code' => 'required|max:255',
                'street' => 'required|max:255',
                'street_number' => 'required|max:255',
                'price' => 'required|integer|min:1|max:10000000',
            ])
        );
        return redirect()->route('listing.index')
            ->with('message', 'Listing was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        $listing->delete();
        return redirect()->back()
            ->with('message', 'Listing was deleted successfully.');
    }
}
