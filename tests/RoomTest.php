<?php

use App\Models\Room;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class RoomTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();

        $user = factory(App\Models\User::class)->create();

        $this->actingAs($user);
    }

    public function testEmptyIndex()
    {
        $this->visit('room')
            ->dontSee('Zaloguj')
            ->see('Pokoje')
            ->dontSee('Numer')
            ->dontSee('Piętro')
            ->dontSee('Cena')
            ->dontSee('Komentarz')
            ->dontSee('Akcje')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
            ->see('Dodaj');
    }

    public function testFilledIndex()
    {
        factory(Room::class, 3)->create();

        $this->visit('room')
            ->dontSee('Zaloguj')
            ->see('Pokoje')
            ->see('Numer')
            ->see('Piętro')
            ->see('Cena')
            ->see('Komentarz')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->see('test comment')
            ->see('Dodaj');
    }
}
