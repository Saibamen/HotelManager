<?php

use App\Models\Reservation;
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
            ->dontSee('Użytkownicy')
            ->dontSee('Numer')
            ->dontSee('Piętro')
            ->dontSee('Pojemność')
            ->dontSee('Cena')
            ->dontSee('Komentarz')
            ->dontSee('Akcje')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
            ->see('Brak pokoi w bazie danych')
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
            ->dontSee('Brak pokoi w bazie danych')
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

        $this->see('jest wymagane')
            ->seePageIs('room/add');
    }

    public function testAddNewObject()
    {
        $object = factory(Room::class)->make();

        $this->visit('room/add')
            ->dontSee('Zaloguj')
            ->see('Dodaj pokój')
            ->type($object->number, 'number')
            ->type($object->floor, 'floor')
            ->type($object->capacity, 'capacity')
            ->type($object->price, 'price')
            ->type('test comment', 'comment')
            ->press('Wyślij');

        $this->see($object->number)
            ->see($object->floor)
            ->see($object->capacity)
            ->see($object->price)
            ->see('test comment')
            ->dontSee('Brak pokoi w bazie danych')
            ->see('Zapisano pomyślnie')
            ->seePageIs('room');

        $this->seeInDatabase('rooms', [
            'number' => $object->number,
        ]);
    }

    public function testTryEditInvalidId()
    {
        $this->visit('room')
            ->see('Pokoje')
            ->visit('room/edit/10000');

        $this->see('Nie znaleziono obiektu')
            ->seePageIs('room');
    }

    public function testTryRefreshEditFormAfterDelete()
    {
        $room = factory(Room::class)->create();

        $this->visit('room/edit/'.$room->id)
            ->seePageIs('room/edit/'.$room->id)
            ->dontSee('Nie znaleziono obiektu')
            ->see('Edytuj pokój')
            ->see('Wyślij');

        $room->delete();

        $this->visit('room/edit/'.$room->id)
            ->dontSee('Edytuj pokój')
            ->dontSee('Wyślij')
            ->see('Nie znaleziono obiektu')
            ->seePageIs('room');
    }

    public function testTryStoreInvalidId()
    {
        $response = $this->call('POST', 'room/edit/1000', [
            '_token'   => csrf_token(),
            'number'   => 1,
            'floor'    => 1,
            'capacity' => 1,
            'price'    => 1,
        ]);

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('home')
            ->seeInSession('message', 'Nie znaleziono obiektu');
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

        $this->see('Zapisano pomyślnie')
            ->seePageIs('room')
            ->see('Edycja komentarza');
    }

    public function testDelete()
    {
        $object = factory(Room::class)->create();

        $this->seeInDatabase('rooms', [
            'id' => $object->id,
        ]);

        $response = $this->call('DELETE', 'room/delete/'.$object->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('rooms', [
            'id' => $object->id,
        ]);
    }

    public function testTryDeleteInvalidId()
    {
        $response = $this->call('DELETE', 'room/delete/1000', [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Nie znaleziono obiektu', $this->decodeResponseJson()['message']);
    }

    public function testDeleteWithReservation()
    {
        $reservation = factory(Reservation::class)->create();

        $this->seeInDatabase('reservations', [
            'id' => $reservation->id,
        ]);

        $this->seeInDatabase('rooms', [
            'id' => $reservation->room->id,
        ]);

        $this->seeInDatabase('guests', [
            'id' => $reservation->guest->id,
        ]);

        $response = $this->call('DELETE', 'room/delete/'.$reservation->room->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('rooms', [
            'id' => $reservation->room->id,
        ]);

        $this->notSeeInDatabase('reservations', [
            'id' => $reservation->id,
        ]);

        $this->seeInDatabase('guests', [
            'id' => $reservation->guest->id,
        ]);
    }
}
