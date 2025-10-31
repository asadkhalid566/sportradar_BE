<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sport;
use App\Models\Location;
use App\Models\Team;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    public function index()
    {
        $sports = Sport::all();
        $locations = Location::all();
        $teams = Team::all();
        $events = Event::with(['sport', 'location', 'teams'])->get();

        return view('events.index', compact('sports', 'locations', 'teams', 'events'));
    }




    public function list(Request $request)
    {
        if ($request->ajax()) {
            $query = Event::with(['sport', 'location', 'teams'])->select('events.*');

             if ($request->sport) {
                $query->whereHas('sport', fn($q) => $q->where('name', $request->sport));
            }

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('start_time', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            } elseif ($request->start_date) {
                $query->where('start_time', '>=', $request->start_date . ' 00:00:00');
            } elseif ($request->end_date) {
                $query->where('start_time', '<=', $request->end_date . ' 23:59:59');
            }

            return DataTables::of($query)
                ->addColumn('date', fn($e) => $e->start_time->format('D, d M Y, H:i'))
                ->addColumn('sport', fn($e) => $e->sport->name)
                ->addColumn('location', fn($e) => $e->location->name ?? 'N/A')
                ->addColumn('teams', fn($e) => $e->teams->pluck('name')->join(' vs '))
                ->addColumn(
                    'actions',
                    fn($e) => '
                <button class="btn btn-sm btn-info text-white btn-show" data-id="' . $e->id . '">Show</button>
                <button class="btn btn-sm btn-primary btn-edit" data-id="' . $e->id . '">Edit</button>
                <form action="' . route('events.destroy', $e->id) . '" method="POST" class="d-inline deleteForm">
                    ' . csrf_field() . '
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>'
                )
                ->rawColumns(['actions'])
                ->make(true);
        }

        abort(404);
    }




    public function store(Request $request)
    {
        $v = $request->validate([
            'title' => 'required|string|max:255',
            '_sport_id' => 'required|exists:sports,id',
            '_location_id' => 'nullable|exists:locations,id',
            'start_time' => 'required|date',
            'description' => 'nullable|string',
            'team1_id' => 'required|different:team2_id|exists:teams,id',
            'team2_id' => 'required|different:team1_id|exists:teams,id',
        ]);

         $event = Event::create([
            'title' => $v['title'],
            '_sport_id' => $v['_sport_id'],
            '_location_id' => $v['_location_id'] ?? null,
            'start_time' => $v['start_time'],
            'description' => $v['description'] ?? null,
        ]);

         $event->teams()->sync([$v['team1_id'], $v['team2_id']]);

        return response()->json(['success' => true]);
    }



    public function show(Event $event)
    {
        $event->load(['sport', 'location', 'teams']);

        return response()->json([
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'sport' => $event->sport->name,
                'location' => $event->location->name ?? 'N/A',
                'teams' => $event->teams->pluck('name')->join(' vs '),
                'start_time' => $event->start_time->format('D, d M Y, H:i'),
                'description' => $event->description ?? '—',
            ]
        ]);
    }




    public function edit(Event $event)
    {
        $event->load(['teams']);
        return response()->json([
            'event' => $event,
            'teams' => $event->teams->pluck('id'),
        ]);
    }


    public function update(Request $request, Event $event)
    {
        $v = $request->validate([
            'title' => 'required|string|max:255',
            '_sport_id' => 'required|exists:sports,id',
            '_location_id' => 'nullable|exists:locations,id',
            'start_time' => 'required|date',
            'description' => 'nullable|string',
            'team1_id' => 'required|different:team2_id|exists:teams,id',
            'team2_id' => 'required|different:team1_id|exists:teams,id',
        ]);

         $event->update([
            'title' => $v['title'],
            '_sport_id' => $v['_sport_id'],
            '_location_id' => $v['_location_id'] ?? null,
            'start_time' => $v['start_time'],
            'description' => $v['description'] ?? null,
        ]);

         $event->teams()->sync([$v['team1_id'], $v['team2_id']]);

        return response()->json(['success' => true]);
    }


    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['success' => true]);
    }
}
