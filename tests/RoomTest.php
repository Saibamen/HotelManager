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

    public function testTryEditInvalidId()
    {
        $this->visit('room')
            ->see('Pokoje')
            ->visit('room/edit/10000');

        $this->followRedirects();

        $this->see('Nie znaleziono obiektu');
    }

    public function testEditValidId()
    {
        $room = factory(Room::class)->create();

        $this->visit('room')
            ->see('Pokoje')
            ->visit('room/edit/'.$room->id);

        $this->see('Edytuj pokój')
            ->see('Numer')
            ->see('Piętro')
            ->see('Pojemność')
            ->see('Cena')
            ->see('Komentarz')
            ->see('test comment')
            ->see('Wyślij');

        $this->type('Edycja komentarza', 'comment')
            ->press('Wyślij');

        $this->seePageIs('room')
            ->see('Zapisano pomyślnie')
            ->see('Edycja komentarza');
    }

    public function testDelete()
    {
        $room = factory(Room::class)->create();

        $this->seeInDatabase('rooms', [
            'ID'  => $room->id,
        ]);

        $response = $this->call('DELETE', 'room/delete/'.$room->id, [
            '_token'   => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('rooms', [
            'ID'  => $room->id,
        ]);
    }
}
