<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // Get the ID of the authenticated user
        $userId = Auth::id();

        // Retrieve offers where the "created_by" column matches the authenticated user's ID
        $orders = Order::where('created_by', $userId)->get();

        return response()->json(['offers' => $orders], Response::HTTP_OK);
    }

    public function show($id)
    {
        // Retrieve a specific order by ID
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json(['order' => $order]);
    }

    public function store(Request $request)
    {
        // dd($request);

        // Create a new order
        $validatedData = $request->validate([
            'total' => 'required|numeric',
            'qte' => 'required|integer',
            'status' => 'required|string',
            'ship_date' => 'date',
            'id_offer' => 'required|integer'
        ]);
        $validatedData['created_by'] = Auth::user()->id;
 
        $order = Order::create($validatedData);

        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }

    public function index_for_courier(){
        // Get the ID of the authenticated user
        $userId = Auth::id();
        $createdBy = Auth::user()->created_by;
        // dd($createdBy);

        // Retrieve offers where the "created_by" column matches the authenticated user's ID
        $offers = Offer::where('created_by', $createdBy)->get();
        $orders=array();
        // dd($orders);


        
        foreach($offers as $offer){
            array_push($orders,Order::where('id_offer', $offer['id'])->get());
        }
        
        return response()->json(['offers' => $offers,'orders' => $orders], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        // Update an existing order
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validatedData = $request->validate([
            'total' => 'numeric',
            'qte' => 'integer',
            'status' => 'string',
            'ship_date' => 'date',
        ]);

        $order->update($validatedData);

        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }

    public function destroy($id)
    {
        // Delete an order by ID
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
