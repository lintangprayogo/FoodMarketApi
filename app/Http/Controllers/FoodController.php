<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    
    public function index()
    {
        $foods = Food::paginate(10);

        return view('food.index', [
            'foods' => $foods
        ]);
    }

    
    public function create()
    {
        return view('food.create');
    }

   
    public function store(Request $request)
    {
        $data = $request->all();
        if($request->file('picturePath')){
            $data['picturePath']= $request->file('picturePath')->store('assets/food','public');
        }
        Food::create($data);
        return redirect()->route('food.index');
    }

  
    public function show($id)
    {
        
    }

   
    public function edit($id)
    {
        $food = Food::find($id);
        return view('food.edit',["food"=>$food]);
    }

    
    public function update(Request $request, $id)
    {
        $data = $request->all();
        if($request->file('picturePath')){
            $data['picturePath']= $request->file('picturePath')->store('assets/food','public');
        }
        $food= Food::find($id);
        $food->update($data);
        return redirect()->route('food.index');
    }

   
    public function destroy($id)
    {
        $food=Food::find($id);
        $food->delete();
        return redirect()->route('food.index');
    }
}
