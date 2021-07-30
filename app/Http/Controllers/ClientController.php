<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Auth::user()->client()->get();
        $response = ['clients' => $clients];
        return $this->responser(true, "Accepted", $response, null, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name.required' => 'Name can not be empty.',
            'address.required' => 'Address can not be empty',
            'cap.required' => 'Postal code can not be empty',
            'city.required' => 'City can not be empty.'
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'bail|required',
            'address' => 'required',
            'cap' => 'required',
            'city' => 'required',
        ], $rules);

        if($validator->fails()){
            $errors = $validator->errors();
            $this->responser(false, "Bad Request", null, $errors, 400);
        }

        $client = new Client();
        $client->name = $request->name;
        $client->address = $request->address;
        $client->cap = $request->cap;
        $client->city = $request->city;

        if($success = Auth::user()->client()->save($client)){
            return $this->responser(true, "Created", $client, null, 201);
        }else{
            $details = "Client insertion Failed!";
            return $this->responser(false, "Internal Error", $details, null, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::user()->client()->where('id', $id)->exists()){
            //$client = Client::firstWhere('id', $id);
            $client = Auth::user()->client()->find($id);
            $response = ['client' => $client];
        }else{
            $response = "Client not found";
        }
        return $this->responser(true, "Accepted", $response, null, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $response = null;
        $rules = [
            'name.required' => 'Name can not be empty.',
            'address.required' => 'Address can not be empty',
            'cap.required' => 'Postal code can not be empty',
            'city.required' => 'City can not be empty.'
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'bail|required',
            'address' => 'required',
            'cap' => 'required',
            'city' => 'required',
        ], $rules);

        if($validator->fails()){
            $errors = $validator->errors();
            $this->responser(false, "Bad Request", null, $errors, 400);
        }

        $name = $request->name;
        $address = $request->address;
        $cap = $request->cap;
        $city = $request->city;

        $clientId = Auth::user()->client()->where('id', $id);
        if($clientId->exists()){
            $client = $clientId->update([
                    'name' => $name,
                    'address' => $address,
                    'cap' => $cap,
                    'city' => $city,
            ]);
            if($client){
                $response = [
                    'name' => $name,
                    'address' => $address,
                    'cap' => $cap,
                    'city' => $city
                ];
            }
        }else{
            $response = "Client not Found.";
        }

        return $this->responser(true, "Accepted", $response, null, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $clientId = Auth::user()->client()->where('id', $id);
        if($clientId->exists()){
            $clientId->delete();
            $response = [
                'deleted' => $id
            ];
        }else{
            $response = "Client non found.";
        }

        return $this->responser(true, "Accepted", $response, null, 200);
    }
}
