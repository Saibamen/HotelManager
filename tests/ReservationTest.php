<?php

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends BrowserKitTestCase
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
        $this->visit('reservation')
            ->dontSee('Zaloguj')
            ->see('Rezerwacje')
            ->dontSee('Numer')
            ->dontSee('Gość')
            ->dontSee('Data rozpoczęcia')
            ->dontSee('Data zakończenia')
            ->dontSee('Ilość osób')
            ->dontSee('Akcje')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
            ->see('Brak rezerwacji w bazie danych')
            ->see('Dodaj');
    }

    public function testFilledIndex()
    {
        factory(Reservation::class, 3)->create();

        $this->visit('reservation')
            ->dontSee('Zaloguj')
            ->see('Rezerwacje')
            ->see('Pokój')
            ->see('Gość')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see('Ilość osób')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->dontSee('Brak rezerwacji w bazie danych')
            ->see('Dodaj');
    }

    /*public function testAddEmptyForm()
    {
        $this->visit('reservation/add')
            ->dontSee('Zaloguj')
            ->see('Dodaj rezerwację')
            ->see('Numer')
            ->see('Piętro')
            ->see('Pojemność')
            ->see('Cena')
            ->see('Komentarz')
            ->see('Wyślij')
            ->press('Wyślij');

        $this->see('jest wymagane')
            ->seePageIs('reservation/add');
    }*/

    public function testTryEditInvalidId()
    {
        $this->visit('reservation')
            ->see('Rezerwacje')
            ->visit('reservation/edit/10000');

        $this->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');
    }

    /*public function testEditValidId()
    {
        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation')
            ->see('Rezerwacje')
            ->visit('reservation/edit/'.$reservation->id);

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
            ->seePageIs('reservation')
            ->see('Edycja komentarza');
    }*/

    public function testDelete()
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

        $response = $this->call('DELETE', 'reservation/delete/'.$reservation->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('reservations', [
            'id' => $reservation->id,
        ]);

        $this->seeInDatabase('rooms', [
            'id' => $reservation->room->id,
        ]);

        $this->seeInDatabase('guests', [
            'id' => $reservation->guest->id,
        ]);
    }

    /*public function testTryStoreInvalidId()
    {
        $this->makeRequest('POST', 'reservation/edit/1000', [
            '_token' => csrf_token(),
        ]);

        $this->notSeeInDatabase('reservations', []);
    }*/

    public function testEmptyFreeRooms()
    {
        $this->visit('room/free')
            ->dontSee('Zaloguj')
            ->see('Aktualnie wolne pokoje')
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

    public function testFilledFreeRooms()
    {
        factory(Room::class, 3)->create();

        $this->visit('room/free')
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

    public function testEmptyOccupiedRooms()
    {
        $this->visit('room/occupied')
            ->dontSee('Zaloguj')
            ->see('Aktualnie zajęte pokoje')
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
}
