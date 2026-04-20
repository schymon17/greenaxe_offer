<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        
        $query = Client::query();
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
        }
        
        $clients = $query->paginate(10);
        return view('clients.index', compact('clients', 'search'));
    }

    public function create()
    {
        $client = null;
        return view('clients.form', compact('client'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Klient został dodany');
    }

    public function edit(Client $client)
    {
        if (request()->wantsJson()) {
            return response()->json($client);
        }
        return view('clients.form', compact('client'));
    }

    public function crm(Client $client)
    {
        return view('clients.crm', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id . '|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
        ]);

        $client->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Klient zaktualizowany']);
        }

        return redirect()->route('clients.index')->with('success', 'Klient zaktualizowany');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Klient został usunięty');
    }
}
