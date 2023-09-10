<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Middleware\CheckRole;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offers = Offer::all();
        return response()->json(['offers' => $offers], Response::HTTP_OK);
    }

    public function index_by_user(){
        // Get the ID of the authenticated user
        $userId = Auth::id();

        // Retrieve offers where the "created_by" column matches the authenticated user's ID
        $offers = Offer::where('created_by', $userId)->get();
        $orders=array();
        
        foreach($offers as $offer){
            array_push($orders,Order::where('id_offer', $offer['id'])->get());
        }
        
        return response()->json(['offers' => $offers,'orders' => $orders], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => [
                'required',
                Rule::in(['active', 'inactive']), // You can customize the allowed values
            ],
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        // dd(Auth::user()->id);
        $validatedData['created_by'] = Auth::user()->id;
        $offer = Offer::create($validatedData);
        return response()->json(['offer' => $offer], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function show(Offer $offer)
    {
        return response()->json(['offer' => $offer], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Offer $offer)
    {
        $validatedData = $request->validate([
            'name' => 'string',
            'description' => 'string',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'status' => Rule::in(['active', 'inactive']), // You can customize the allowed values
            'price' => 'numeric',
            'stock' => 'integer',
        ]);

        $offer->update($validatedData);

        return response()->json(['offer' => $offer], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
