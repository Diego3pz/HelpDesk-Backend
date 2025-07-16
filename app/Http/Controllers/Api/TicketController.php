<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    // Crear un nuevo ticket
    public function store(StoreTicketRequest $request)
    {

        $attachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $attachments[] = $path;
            }
        }

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'priority' => $request->input('priority'),
            'category' => $request->input('category'),
            'attachments' => $attachments,
            'status' => $request->input('status', 'open'),
        ]);

        return response()->json(['ticket' => $ticket], 201);
    }

    // Listar tickets del usuario autenticado
    public function index(Request $request)
    {
        $tickets = Ticket::where('user_id', $request->user()->id)->get();

        // Convertir los attachments a URLs públicas
        $tickets->transform(function ($ticket) {
            if (is_array($ticket->attachments)) {
                $ticket->attachments = collect($ticket->attachments)
                    ->map(fn($path) => Storage::url($path))
                    ->toArray();
            }
            return $ticket;
        });

        return response()->json(['tickets' => $tickets]);
    }

    // Mostrar un ticket específico
    public function show(Request $request, $id)
    {
        $ticket = Ticket::with(['comments.user'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        // Convertir los attachments a URLs públicas
        if (is_array($ticket->attachments)) {
            $ticket->attachments = collect($ticket->attachments)
                ->map(fn($path) => Storage::url($path))
                ->toArray();
        }

        return response()->json(['ticket' => $ticket]);
    }

    // Actualizar un ticket
    public function update(UpdateTicketRequest $request, $id)
    {
        $ticket = Ticket::where('user_id', $request->user()->id)->findOrFail($id);

        $ticket->update($request->validated());

        return response()->json(['ticket' => $ticket]);
    }

    // Cerrar un ticket
    public function close(Request $request, $id)
    {
        $ticket = Ticket::where('user_id', $request->user()->id)->findOrFail($id);
        $ticket->status = 'closed';
        $ticket->save();

        return response()->json(['ticket' => $ticket]);
    }

    // Eliminar un ticket
    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::where('user_id', $request->user()->id)->findOrFail($id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket eliminado con éxito']);
    }
}
