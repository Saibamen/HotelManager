<?php

use App\Models\Room;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoomTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        Session::start();

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
            ->see('Pojemność')
            ->see('Cena')
            ->see('Komentarz')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->see('test comment')
            ->see('Dodaj');
    }

    public function testAddEmptyForm()
    {
        $this->visit('room/add')
            ->dontSee('Zaloguj')
            ->see('Dodaj pokój')
            ->see('Numer')
            ->see('Piętro')
            ->see('Pojemność')
            ->see('Cena')
            ->see('Komentarz')
            ->see('Wyślij')
            ->press('Wyślij');

        //$this->seePageIs('room/add')
        //->see('jest wymagane');
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
            'ID' => $room->id,
        ]);

        $response = $this->call('DELETE', 'room/delete/'.$room->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('rooms', [
            'ID' => $room->id,
        ]);
    }

    public function testTryStoreInvalidId()
    {
        $this->makeRequest('POST', 'room/edit/1000', [
            '_token' => csrf_token(),
        ]);

        $this->notSeeInDatabase('rooms', []);
    }
}
