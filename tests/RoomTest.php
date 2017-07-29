<?php

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

    public function testIndex()
    {
        $this->visit('room')
            ->dontSee('Zaloguj')
            ->see('Pokoje')
            ->see('Numer')
            ->see('Piętro')
            ->see('Cena')
            ->see('Akcje')
            ->see('Dodaj')
            ->see('Edytuj')
            ->see('Usuń');
    }
}
