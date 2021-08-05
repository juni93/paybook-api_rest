<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountingController extends Controller
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
    public function index($id)
    {
        $client = Auth::user()->client()->where('id', $id);
        if($client->exists()){
            $notes = Auth::user()->client()->find($id)->accounting()->get();
            $response = ['notes' => $notes];
        }else{
            $response = "Client not found";
        }
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
    public function store(Request $request, $id)
    {
        $rules = [
            'description.required' => 'description can not be empty.',
            'amount.required' => 'amount can not be empty',
            'debit.required' => 'debit code can not be empty',
            'credit.required' => 'credit can not be empty.',
            'paid.required' => 'paid can not be empty.',
        ];

        $validator = Validator::make($request->all(), [
            'description' => 'bail|required',
            'amount' => 'required',
            'debit' => 'required',
            'credit' => 'required',
            'paid' => 'required',
        ], $rules);

        if($validator->fails()){
            $errors = $validator->errors();
            $this->responser(false, "Bad Request", null, $errors, 400);
        }

        $clientId = Auth::user()->client()->where('id', $id);
        if($clientId->exists()){
            $note = new Accounting();
            $note->description = $request->description;
            $note->amount = $request->amount;
            $note->debit = $request->debit;
            $note->credit = $request->credit;
            $note->paid = $request->paid;

            if($success = Auth::user()->client()->find($id)->accounting()->save($note)){
                return $this->responser(true, "Created", $note, null, 201);
            }else{
                $details = "Note insertion Failed!";
                return $this->responser(false, "Internal Error", $details, null, 500);
            }
        }else{
            $response = "Client not Found.";
            return $this->responser(false, "Internal Error", $response, null, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Accounting  $accounting
     * @return \Illuminate\Http\Response
     */
    public function show($id, $noteId)
    {
        $client = Auth::user()->client()->where('id', $id);
        if($client->exists()){
            if(Auth::user()->client()->find($id)->accounting()->where('id', $noteId)->exists()){
                $note = Auth::user()->client()->find($id)->accounting()->find($noteId);
                $response = ['notes' => $note];
            }else{
                $response = "Note not found.";
            }
        }else{
            $response = "Client not found";
        }
        return $this->responser(true, "Accepted", $response, null, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Accounting  $accounting
     * @return \Illuminate\Http\Response
     */
    public function edit(Accounting $accounting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Accounting  $accounting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $noteId)
    {
        $rules = [
            'description.required' => 'description can not be empty.',
            'amount.required' => 'amount can not be empty',
            'debit.required' => 'debit code can not be empty',
            'credit.required' => 'credit can not be empty.',
            'paid.required' => 'paid can not be empty.',
        ];

        $validator = Validator::make($request->all(), [
            'description' => 'bail|required',
            'amount' => 'required',
            'debit' => 'required',
            'credit' => 'required',
            'paid' => 'required',
        ], $rules);

        if($validator->fails()){
            $errors = $validator->errors();
            $this->responser(false, "Bad Request", null, $errors, 400);
        }
        $client = Auth::user()->client()->where('id', $id);
        if($client->exists()){
            $noteToUpdate = Auth::user()->client()->find($id)->accounting()->where('id', $noteId);
            if($noteToUpdate->exists()){
                $note = $noteToUpdate->update([
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'debit' => $request->debit,
                    'credit' => $request->credit,
                    'paid' => $request->paid,
                ]);
                if($note){
                    $response = [
                        'description' => $request->description,
                        'amount' => $request->amount,
                        'debit' => $request->debit,
                        'credit' => $request->credit,
                        'paid' => $request->paid,
                    ];
                }
            }else{
                $response = "Note not found.";
            }
        }else{
            $response = "Client not found";
        }
        return $this->responser(true, "Accepted", $response, null, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Accounting  $accounting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $noteId)
    {
        $client = Auth::user()->client()->where('id', $id);
        if($client->exists()){
            $note = Auth::user()->client()->find($id)->accounting()->where('id', $noteId);
            if($note->exists()){
                $note->delete();
                $response = ['note' => $noteId];
            }else{
                $response = "Note not found.";
            }
        }else{
            $response = "Client not found";
        }
        return $this->responser(true, "Accepted", $response, null, 200);
    }
}
