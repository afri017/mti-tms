<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{

    public function index()
    {
        // ambil semua data customer dari database
        $customers = Customer::all();
        $pageTitle = 'Customer Master';
        $breadchumb = 'Table Maintenance';

        // arahkan ke view customer.index (bukan 'index' aja)
        return view('customer.index', compact('customers','pageTitle','breadchumb'));
    }

    public function create()
    {
        $pageTitle = 'Customer Master';
        $breadchumb = 'Table Maintenance';
        return view('customer.create', compact('pageTitle','breadchumb'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idcustomer' => 'required|unique:customers,idcustomer',
            'customer_name' => 'required|string|max:255',
            'address' => 'required|string',
            'notelp' => 'nullable|string|max:50',
        ]);

        Customer::create([
            'idcustomer'   => $request->idcustomer,
            'customer_name'=> $request->customer_name,
            'address'      => $request->address,
            'notelp'       => $request->notelp,
            'is_active'    => 'Y',
            'created_by'   => 'admin', // bisa diganti auth()->user()->name
            'last_update'  => now(),
        ]);

        return redirect()->route('customer.create')->with('success', 'Customer berhasil disimpan!');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $pageTitle = 'Customer Master';
        $breadchumb = 'Table Maintenance';
        return view('customer.edit', compact('customer','pageTitle','breadchumb'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'idcustomer' => 'required',
            'customer_name' => 'required',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus.');
    }
}
