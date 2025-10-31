<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Sport;
use App\Models\Location;
use App\Models\Team;
use PHPUnit\Framework\Attributes\Test;

class EventControllerTest extends TestCase
{
    #[Test]
    public function it_can_load_the_events_page()
    {
        $response = $this->get(route('events.index'));
        $response->assertStatus(200);
        $response->assertSee('Upcoming Sports Events');
    }

    #[Test]
    public function it_can_list_events_via_ajax()
    {
        // ✅ Simulate AJAX request
        $response = $this->get('/events/list', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    #[Test]
    public function it_can_create_a_new_event()
    {
        $sport = Sport::first();
        $location = Location::first();
        $teams = Team::take(2)->pluck('id')->toArray();

        if (!$sport || count($teams) < 2) {
            $this->markTestSkipped('Required records not found in DB');
        }

        $response = $this->post(route('events.store'), [
            'title' => 'Unit Test Match',
            '_sport_id' => $sport->id,
            '_location_id' => $location?->id,
            'team1_id' => $teams[0],
            'team2_id' => $teams[1],
            'start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'description' => 'Testing event creation from automated test.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('events', ['title' => 'Unit Test Match']);
    }

    #[Test]
    public function it_can_update_an_event()
    {
        $event = Event::latest()->first();

        if (!$event) {
            $this->markTestSkipped('No event found to update.');
        }

        $response = $this->post("/events/{$event->id}", [
            '_method' => 'PUT',
            'title' => 'Updated Event Title',
            '_sport_id' => $event->_sport_id,
            '_location_id' => $event->_location_id,
            'team1_id' => 1,
            'team2_id' => 2,
            'start_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'description' => 'Updated via test.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('events', ['title' => 'Updated Event Title']);
    }

    #[Test]
    public function it_can_delete_an_event()
    {
        $event = Event::latest()->first();

        if (!$event) {
            $this->markTestSkipped('No event found to delete.');
        }

        $response = $this->post("/events/{$event->id}", [
            '_method' => 'DELETE',
        ]);

        $response->assertStatus(200);
    }
}
