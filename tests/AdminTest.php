<?php

use App\Models\Reservation;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        Session::start();

        $user = factory(App\Models\User::class)->make(['is_admin' => true]);
        $this->actingAs($user);
    }

    public function testIndex()
    {
        $this->visit('admin')
            ->seePageIs('admin')
            ->dontSee('Zaloguj')
            ->see('Opcje')
            ->see('Generuj stan początkowy')
            ->see('Wszystkie rezerwacje')
            ->see('Usuń')
            ->see('Pokoje')
            ->see('Goście')
            ->see('Rezerwacje')
            ->see('Pamiętaj o utworzeniu kopii zapasowej bazy danych!');

        $user = factory(App\Models\User::class)->make();
        $this->actingAs($user);

        $this->visit('admin')
            ->seePageIs('room')
            ->dontSee('Pamiętaj o utworzeniu kopii zapasowej bazy danych!');
    }

    public function testDeleteRooms()
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

        $response = $this->call('DELETE', 'admin/delete_rooms', [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('reservations', [
            'id' => $reservation->id,
        ]);

        $this->notSeeInDatabase('rooms', [
            'id' => $reservation->room->id,
        ]);

        $this->seeInDatabase('guests', [
            'id' => $reservation->guest->id,
        ]);
    }

    public function testDeleteGuests()
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

        $response = $this->call('DELETE', 'admin/delete_guests', [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('reservations', [
            'id' => $reservation->id,
        ]);

        $this->seeInDatabase('rooms', [
            'id' => $reservation->room->id,
        ]);

        $this->notSeeInDatabase('guests', [
            'id' => $reservation->guest->id,
        ]);
    }

    public function testDeleteReservations()
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

        $response = $this->call('DELETE', 'admin/delete_reservations', [
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

    public function testGenerateInitialState()
    {
        $this->visit('admin/generate_initial_state')
            ->seePageIs('admin/generate_initial_state')
            ->dontSee('Zaloguj')
            ->press('Wyślij');

        $this->seePageIs('admin')
            ->see('Zapisano pomyślnie');

        $this->visit('room')
            ->seePageIs('room')
            ->dontSee('Brak pokoi w bazie danych');

        $this->visit('guest')
            ->seePageIs('guest')
            ->dontSee('Brak gości w bazie danych');
    }

    public function testGenerateInitialStateEnglish()
    {
        App::setLocale('en');

        $this->visit('admin/generate_initial_state')
            ->seePageIs('admin/generate_initial_state')
            ->dontSee('Login')
            ->press('Send');

        $this->seePageIs('admin')
            ->see('Saved successfully');

        $this->visit('room')
            ->seePageIs('room')
            ->dontSee('No rooms in database');

        $this->visit('guest')
            ->seePageIs('guest')
            ->dontSee('No guests in database');
    }
}
