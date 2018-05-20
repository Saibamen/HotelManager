<?php

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
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
            ->see('Wszystkie rezerwacje')
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
        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation')
            ->dontSee('Zaloguj')
            ->see('Wszystkie rezerwacje')
            ->see('Gość')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see('Ilość osób')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->dontSee('Brak rezerwacji w bazie danych')
            ->see('Dodaj')
            ->see($reservation->room->number)
            ->see($reservation->guest->rooms->first()->number)
            ->see($reservation->guest->full_name)
            ->see($reservation->date_start)
            ->see($reservation->date_end);
    }

    public function testEmptyCurrent()
    {
        $this->visit('reservation/current')
            ->dontSee('Zaloguj')
            ->see('Aktualne rezerwacje')
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

    public function testFilledCurrent()
    {
        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation/current')
            ->dontSee('Zaloguj')
            ->see('Aktualne rezerwacje')
            ->see('Gość')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see('Ilość osób')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->dontSee('Brak rezerwacji w bazie danych')
            ->see('Dodaj')
            ->see($reservation->room->number)
            ->see($reservation->guest->full_name)
            ->see($reservation->date_start)
            ->see($reservation->date_end);
    }

    public function testEmptyFuture()
    {
        $this->visit('reservation/future')
            ->dontSee('Zaloguj')
            ->see('Przyszłe rezerwacje')
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

    public function testFilledFuture()
    {
        $reservation = factory(Reservation::class)->create([
            'date_start' => Carbon::tomorrow(),
        ]);

        $this->visit('reservation/future')
            ->dontSee('Zaloguj')
            ->see('Przyszłe rezerwacje')
            ->see('Gość')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see('Ilość osób')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->dontSee('Brak rezerwacji w bazie danych')
            ->see('Dodaj')
            ->see($reservation->room->number)
            ->see($reservation->guest->full_name)
            ->see($reservation->date_start)
            ->see($reservation->date_end);
    }

    public function testChooseGuestEmpty()
    {
        $this->visit('reservation/add')
            ->seePageIs('reservation/choose_guest')
            ->dontSee('Zaloguj')
            ->see('Wybierz gościa')
            ->dontSee('Imię')
            ->dontSee('Nazwisko')
            ->dontSee('Adres')
            ->dontSee('PESEL')
            ->dontSee('Kontakt')
            ->dontSee('Akcje')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
            ->see('Brak gości w bazie danych')
            ->see('Dodaj');
    }

    public function testChooseGuestFilled()
    {
        factory(Guest::class)->create();

        $this->visit('reservation/add')
            ->seePageIs('reservation/choose_guest')
            ->dontSee('Zaloguj')
            ->see('Wybierz gościa')
            ->see('Imię')
            ->see('Nazwisko')
            ->see('Adres')
            ->see('PESEL')
            ->see('Kontakt')
            ->see('Akcje')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
            ->dontSee('Brak gości w bazie danych')
            ->see('Dodaj');
    }

    public function testSearchFreeRoomsInvalidId()
    {
        $this->visit('reservation')
            ->visit('reservation/search_free_rooms/1000')
            ->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');
    }

    public function testPostSearchFreeRoomsInvalidId()
    {
        $this->visit('reservation');

        $response = $this->call('POST', 'reservation/search_free_rooms/1000', [
            '_token'     => csrf_token(),
            'guest'      => 'd',
            'date_start' => Carbon::today(),
            'date_end'   => Carbon::tomorrow(),
            'people'     => 1,
        ]);

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('reservation.index')
            ->seeInSession('message', 'Nie znaleziono obiektu');
    }

    public function testSearchFreeRoomsDefaultPost()
    {
        $guest = factory(Guest::class)->create();

        $this->visit('reservation/add')
            ->seePageIs('reservation/choose_guest')
            ->dontSee('Zaloguj')
            ->see('Wybierz gościa')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
            ->dontSee('Brak gości w bazie danych')
            ->see('Dodaj')
            ->click('Wybierz')
            ->seePageIs('reservation/search_free_rooms/'.$guest->id);

        $todayDate = Carbon::today()->format('d.m.Y');

        $this->see($guest->first_name.' '.$guest->last_name)
            ->see($todayDate)
            ->press('Wyślij');

        $this->see($guest->first_name.' '.$guest->last_name)
            ->see($todayDate)
            ->see('Data rozpoczęcia musi być datą wcześniejszą od data zakończenia.');
    }

    public function testSearchFreeRoomsCorrectPostNoRooms()
    {
        $guest = factory(Guest::class)->create();

        $this->visit('reservation/search_free_rooms/'.$guest->id);

        $todayDate = Carbon::today()->format('d.m.Y');
        $tomorrowDate = Carbon::tomorrow()->format('d.m.Y');

        $this->see($guest->first_name.' '.$guest->last_name)
            ->see($todayDate)
            ->type($tomorrowDate, 'date_end')
            ->press('Wyślij');

        $this->seePageIs('reservation/choose_room/'.$guest->id)
            ->see('Wybierz pokój dla '.$guest->first_name.' '.$guest->last_name)
            ->see('Brak pokoi w bazie danych')
            ->dontSee('Numer')
            ->dontSee('Piętro');
    }

    public function testShowFreeRoomForReservationsOutsideDates()
    {
        $sessionEndAddDays = 3;

        $this->session([
            'date_start' => Carbon::today(),
            'date_end'   => Carbon::today()->addDays($sessionEndAddDays),
            'people'     => 1,
        ]);

        $room = factory(Room::class)->create();
        $reservation = factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->subDays(10),
            'date_end'   => Carbon::today()->subDays(2),
        ]);
        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->subDays(10),
            'date_end'   => Carbon::today(),
        ]);
        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->addDays($sessionEndAddDays),
            'date_end'   => Carbon::today()->addDays($sessionEndAddDays + 10),
        ]);
        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->addDays($sessionEndAddDays + 2),
            'date_end'   => Carbon::today()->addDays($sessionEndAddDays + 10),
        ]);

        $this->visit('reservation/choose_room/'.$reservation->guest->id)
            ->see('Wybierz pokój dla '.$reservation->guest->first_name.' '.$reservation->guest->last_name)
            ->dontSee('Brak pokoi w bazie danych')
            ->dontSee('Liczba osób przekracza pojemność pokoju')
            ->dontSee('Podane daty kolidują z inną rezerwacją na ten pokój')
            ->dontSee('Błąd sesji. Spróbuj ponownie')
            ->see('Numer')
            ->see('Piętro');
    }

    public function testSearchFreeRoomsCorrectPostWithRoomsAndAddReservation()
    {
        $guest = factory(Guest::class)->create();
        $room = factory(Room::class)->create();

        $this->assertFalse($room->reservations()->exists());
        $this->assertFalse($room->guests()->exists());

        $this->assertFalse($guest->reservations()->exists());
        $this->assertFalse($guest->rooms()->exists());

        $this->visit('reservation/search_free_rooms/'.$guest->id);

        $todayDate = Carbon::today();
        $tomorrowDate = Carbon::tomorrow();

        $this->see($guest->first_name.' '.$guest->last_name)
            ->see($todayDate->format('d.m.Y'))
            ->type($tomorrowDate->format('d.m.Y'), 'date_end')
            ->press('Wyślij');

        $this->seePageIs('reservation/choose_room/'.$guest->id)
            ->see('Wybierz pokój dla '.$guest->first_name.' '.$guest->last_name)
            ->dontSee('Brak pokoi w bazie danych')
            ->see('Numer')
            ->see('Piętro')
            ->see('Akcje')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
            ->see($room->number);

        $this->click('Wybierz')
            ->dontSee('Liczba osób przekracza pojemność pokoju')
            ->seePageIs('reservation')
            ->see('Zapisano pomyślnie')
            ->dontSee('Brak rezerwacji w bazie danych');

        $this->seeInDatabase('reservations', [
            'room_id'    => $room->id,
            'guest_id'   => $guest->id,
            'people'     => 1,
        ]);

        $this->see($todayDate->format('d.m.Y'))
            ->see($tomorrowDate->format('d.m.Y'));

        $this->assertTrue($room->reservations()->exists());
        $this->assertTrue($room->guests()->exists());

        $this->assertTrue($guest->reservations()->exists());
        $this->assertTrue($guest->rooms()->exists());
    }

    public function testAddReservationOutsideOtherDates()
    {
        $sessionEndAddDays = 3;

        $this->session([
            'date_start' => Carbon::today(),
            'date_end'   => Carbon::today()->addDays($sessionEndAddDays),
            'people'     => 1,
        ]);

        $room = factory(Room::class)->create();
        $reservation = factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->subDays(10),
            'date_end'   => Carbon::today()->subDays(2),
        ]);
        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->subDays(10),
            'date_end'   => Carbon::today(),
        ]);
        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->addDays($sessionEndAddDays),
            'date_end'   => Carbon::today()->addDays($sessionEndAddDays + 10),
        ]);
        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->addDays($sessionEndAddDays + 2),
            'date_end'   => Carbon::today()->addDays($sessionEndAddDays + 10),
        ]);

        $this->visit('reservation/add/'.$reservation->guest->id.'/'.$reservation->room->id)
            ->dontSee('Nie znaleziono obiektu')
            ->dontSee('Brak pokoi w bazie danych')
            ->dontSee('Liczba osób przekracza pojemność pokoju')
            ->dontSee('Podane daty kolidują z inną rezerwacją na ten pokój')
            ->dontSee('Błąd sesji. Spróbuj ponownie')
            ->see('Zapisano pomyślnie')
            ->see(Carbon::today()->format('d.m.Y'))
            ->see(Carbon::today()->addDays($sessionEndAddDays)->format('d.m.Y'))
            ->seePageIs('reservation');

        $this->seeInDatabase('reservations', [
            'room_id'    => $reservation->room->id,
            'guest_id'   => $reservation->guest->id,
            'people'     => 1,
            'date_start' => Carbon::today(),
        ]);
    }

    public function testGetChooseFreeRoomsWithIncorrectSession()
    {
        $response = $this->call('GET', 'reservation/choose_room/1000');

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('home')
            ->seeInSession('message', 'Błąd sesji. Spróbuj ponownie');

        $this->followRedirects()
            ->seePageIs('room');
    }

    public function testPostChooseFreeRoomsWithoutGuest()
    {
        $this->session([
            'date_start' => 1, 'date_end' => 2, 'people' => 2,
        ]);

        $response = $this->call('GET', 'reservation/choose_room/1000');

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('home')
            ->seeInSession('message', 'Nie znaleziono obiektu');

        $this->followRedirects()
            ->seePageIs('room');

        $this->seeInSession(['date_start', 'date_end', 'people']);
    }

    public function testTryAddWithIncorrectSession()
    {
        $response = $this->call('GET', 'reservation/add/1/2');

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('home')
            ->seeInSession('message', 'Błąd sesji. Spróbuj ponownie');

        $this->followRedirects()
            ->seePageIs('room');
    }

    public function testTryAddWithoutGuestAndRoom()
    {
        $this->session([
            'date_start' => Carbon::today(),
            'date_end'   => Carbon::tomorrow(),
            'people'     => 2,
        ]);

        $response = $this->call('GET', 'reservation/add/1/2');

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('home')
            ->seeInSession('message', 'Nie znaleziono obiektu');

        $this->followRedirects()
            ->seePageIs('room');

        $this->seeInSession(['date_start', 'date_end', 'people']);

        $guest = factory(Guest::class)->create();

        $response = $this->call('GET', 'reservation/add/'.$guest->id.'/2');

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('room.index')
            ->seeInSession('message', 'Nie znaleziono obiektu');

        $this->followRedirects()
            ->seePageIs('room');

        $this->seeInSession(['date_start', 'date_end', 'people']);
    }

    public function testTryAddWithTooSmallRoom()
    {
        $guest = factory(Guest::class)->create();
        $room = factory(Room::class)->create([
            'capacity' => rand(1, 98),
        ]);

        $this->session([
            'date_start' => Carbon::today(),
            'date_end'   => Carbon::tomorrow(),
            'people'     => rand($room->capacity + 1, 99),
        ]);

        $this->visit('reservation')
            ->visit('reservation/add/'.$guest->id.'/'.$room->id)
            ->dontSee('Nie znaleziono obiektu')
            ->dontSee('Podane daty kolidują z inną rezerwacją na ten pokój')
            ->dontSee('Błąd sesji. Spróbuj ponownie')
            ->dontSee('Zapisano pomyślnie')
            ->see('Liczba osób przekracza pojemność pokoju')
            ->seePageIs('reservation');
    }

    public function testTryAddWithNonFreeRoom()
    {
        $guest = factory(Guest::class)->create();
        $room = factory(Room::class)->create();

        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today(),
        ]);

        $this->session([
            'date_start' => Carbon::today(),
            'date_end'   => Carbon::tomorrow(),
            'people'     => rand(1, $room->capacity),
        ]);

        $this->visit('reservation')
            ->visit('reservation/add/'.$guest->id.'/'.$room->id)
            ->dontSee('Nie znaleziono obiektu')
            ->dontSee('Błąd sesji. Spróbuj ponownie')
            ->dontSee('Zapisano pomyślnie')
            ->dontSee('Liczba osób przekracza pojemność pokoju')
            ->see('Podane daty kolidują z inną rezerwacją na ten pokój')
            ->seePageIs('reservation');
    }

    public function testTryEditInvalidId()
    {
        $this->visit('reservation')
            ->see('Rezerwacje')
            ->visit('reservation/edit/10000');

        $this->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');
    }

    public function testTryPostEditInvalidId()
    {
        $response = $this->call('POST', 'reservation/edit/1000', [
            '_token'     => csrf_token(),
            'guest'      => 1,
            'date_start' => Carbon::today(),
            'date_end'   => Carbon::tomorrow(),
            'people'     => 1,
        ]);

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('home')
            ->seeInSession('message', 'Nie znaleziono obiektu');

        $this->followRedirects()
            ->seePageIs('room');
    }

    public function testShowEditForm()
    {
        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation/edit/'.$reservation->id)
            ->see('Edytuj rezerwację')
            ->see('Gość')
            ->see('Zmień gościa')
            ->see('Zmień pokój')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see($reservation->guest->full_name)
            ->see($reservation->room->number)
            ->see('Wyślij');
    }

    public function testSendDefaultEditForm()
    {
        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation/edit/'.$reservation->id)
            ->see('Edytuj rezerwację')
            ->see('Gość')
            ->see('Zmień gościa')
            ->see('Zmień pokój')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see($reservation->guest->full_name)
            ->see($reservation->room->number)
            ->press('Wyślij')
            ->dontSee('Liczba osób przekracza pojemność pokoju')
            ->dontSee('Podane daty kolidują z inną rezerwacją na ten pokój')
            ->seePageIs('reservation')
            ->see('Zapisano pomyślnie');
    }

    public function testSendEditFormWithMorePeopleThanRoomCapacity()
    {
        $room = factory(Room::class)->create([
            'capacity' => rand(1, 98),
        ]);

        $reservation = factory(Reservation::class)->create([
            'room_id' => $room->id,
            'people'  => rand(1, $room->capacity),
        ]);

        $this->visit('reservation/edit/'.$reservation->id)
            ->see('Edytuj rezerwację')
            ->see('Gość')
            ->see('Zmień gościa')
            ->see('Zmień pokój')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see($reservation->guest->full_name)
            ->see($reservation->room->number)
            ->type(rand($reservation->room->capacity + 1, 99), 'people')
            ->press('Wyślij')
            ->see('Liczba osób przekracza pojemność pokoju')
            ->dontSee('Podane daty kolidują z inną rezerwacją na ten pokój')
            ->seePageIs('reservation/edit/'.$reservation->id)
            ->dontSee('Zapisano pomyślnie');
    }

    public function testTryChangeCollidingDatesForReservation()
    {
        $room = factory(Room::class)->create();
        $reservation = factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'people'     => rand(1, $room->capacity),
            'date_start' => Carbon::today(),
        ]);
        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today()->subDays(15),
            'date_end'   => Carbon::today()->subDays(5),
        ]);

        $this->visit('reservation');

        $response = $this->call('POST', 'reservation/edit/'.$reservation->id, [
            '_token'     => csrf_token(),
            'guest'      => 'd',
            'date_start' => Carbon::today()->subDays(10),
            'date_end'   => Carbon::tomorrow(),
            'people'     => $reservation->people,
        ]);

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('reservation.index')
            ->seeInSession('message', 'Podane daty kolidują z inną rezerwacją na ten pokój');

        $this->seeInDatabase('reservations', [
            'id'         => $reservation->id,
            'guest_id'   => $reservation->guest->id,
            'room_id'    => $room->id,
            'date_start' => Carbon::parse($reservation->date_start),
            'date_end'   => Carbon::parse($reservation->date_end),
        ]);
    }

    public function testShowChooseGuestForEdit()
    {
        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation/edit/'.$reservation->id)
            ->see('Edytuj rezerwację')
            ->see('Gość')
            ->see('Zmień gościa')
            ->see('Zmień pokój')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see($reservation->guest->full_name)
            ->see($reservation->room->number)
            ->see('Wyślij')
            ->click('Zmień gościa');

        $this->seePageIs('reservation/edit_choose_guest/'.$reservation->id)
            ->see('Zmień gościa dla rezerwacji')
            ->see('Brak gości w bazie danych')
            ->dontSee('Wybierz');

        factory(Guest::class)->create();

        $this->visit('reservation/edit_choose_guest/'.$reservation->id)
            ->see('Zmień gościa dla rezerwacji')
            ->dontSee('Brak gości w bazie danych')
            ->see('Wybierz');
    }

    public function testTryShowChooseGuestForEditWithIncorrectReservationId()
    {
        $this->visit('reservation')
            ->visit('reservation/edit_choose_guest/1000')
            ->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');
    }

    public function testTryChangeGuestForReservationWithoutReservationAndGuest()
    {
        $this->visit('reservation')
            ->visit('reservation/edit_change_guest/1000/2000')
            ->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');

        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation')
            ->visit('reservation/edit_change_guest/'.$reservation->id.'/2000')
            ->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');
    }

    public function testChangeGuestForReservation()
    {
        $reservation = factory(Reservation::class)->create();
        $guest = factory(Guest::class)->create();

        $this->visit('reservation/edit_choose_guest/'.$reservation->id)
            ->seePageIs('reservation/edit_choose_guest/'.$reservation->id)
            ->see('Zmień gościa dla rezerwacji')
            ->dontSee('Brak gości w bazie danych')
            ->see('Wybierz')
            ->click('Wybierz')
            ->seePageIs('reservation/edit/'.$reservation->id)
            ->see('Zapisano pomyślnie');

        $this->seeInDatabase('reservations', [
            'id'       => $reservation->id,
            'guest_id' => $guest->id,
        ]);
    }

    public function testShowChooseRoomForEdit()
    {
        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation/edit/'.$reservation->id)
            ->see('Edytuj rezerwację')
            ->see('Gość')
            ->see('Zmień gościa')
            ->see('Zmień pokój')
            ->see('Data rozpoczęcia')
            ->see('Data zakończenia')
            ->see($reservation->guest->full_name)
            ->see($reservation->room->number)
            ->see('Wyślij')
            ->click('Zmień pokój');

        $this->seePageIs('reservation/edit_choose_room/'.$reservation->id)
            ->see('Zmień pokój dla rezerwacji')
            ->see('Brak pokoi w bazie danych')
            ->dontSee('Wybierz');

        factory(Room::class)->create([
            'capacity' => rand($reservation->people, 99),
        ]);

        $this->visit('reservation/edit_choose_room/'.$reservation->id)
            ->see('Zmień pokój dla rezerwacji')
            ->dontSee('Brak pokoi w bazie danych')
            ->see('Wybierz');
    }

    public function testTryShowChooseRoomForEditWithIncorrectReservationId()
    {
        $this->visit('reservation')
            ->visit('reservation/edit_choose_room/1000')
            ->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');
    }

    public function testTryChangeRoomForReservationWithoutReservationAndRoom()
    {
        $this->visit('reservation')
            ->visit('reservation/edit_change_room/1000/2000')
            ->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');

        $reservation = factory(Reservation::class)->create();

        $this->visit('reservation')
            ->visit('reservation/edit_change_room/'.$reservation->id.'/2000')
            ->see('Nie znaleziono obiektu')
            ->seePageIs('reservation');
    }

    public function testTryChangeRoomForReservationWithTooSmallCapacity()
    {
        $reservation = factory(Reservation::class)->create([
            'people' => rand(2, 99),
        ]);
        $room = factory(Room::class)->create([
            'capacity' => rand(1, $reservation->people - 1),
        ]);

        $this->visit('reservation')
            ->visit('reservation/edit_change_room/'.$reservation->id.'/'.$room->id)
            ->dontSee('Nie znaleziono obiektu')
            ->dontSee('Podane daty kolidują z inną rezerwacją na ten pokój')
            ->dontSee('Błąd sesji. Spróbuj ponownie')
            ->dontSee('Zapisano pomyślnie')
            ->see('Liczba osób przekracza pojemność pokoju')
            ->seePageIs('reservation');
    }

    public function testTryChangeRoomForReservationToNonFreeRoom()
    {
        $reservation = factory(Reservation::class)->create([
            'date_start' => Carbon::today(),
        ]);
        $room = factory(Room::class)->create([
            'capacity' => rand($reservation->people, 99),
        ]);
        factory(Reservation::class)->create([
            'room_id'    => $room->id,
            'date_start' => Carbon::today(),
        ]);

        $this->visit('reservation')
            ->visit('reservation/edit_change_room/'.$reservation->id.'/'.$room->id)
            ->dontSee('Nie znaleziono obiektu')
            ->dontSee('Błąd sesji. Spróbuj ponownie')
            ->dontSee('Liczba osób przekracza pojemność pokoju')
            ->dontSee('Zapisano pomyślnie')
            ->see('Podane daty kolidują z inną rezerwacją na ten pokój')
            ->seePageIs('reservation');

        $this->seeInDatabase('reservations', [
            'id'       => $reservation->id,
            'guest_id' => $reservation->guest->id,
            'room_id'  => $reservation->room->id,
        ]);
    }

    public function testChangeRoomForReservation()
    {
        $reservation = factory(Reservation::class)->create();
        $room = factory(Room::class)->create([
            'capacity' => rand($reservation->people, 99),
        ]);

        $this->visit('reservation/edit_choose_room/'.$reservation->id)
            ->seePageIs('reservation/edit_choose_room/'.$reservation->id)
            ->see('Zmień pokój dla rezerwacji')
            ->dontSee('Brak pokoi w bazie danych')
            ->see('Wybierz')
            ->click('Wybierz')
            ->seePageIs('reservation/edit/'.$reservation->id)
            ->see('Zapisano pomyślnie')
            ->dontSee('Liczba osób przekracza pojemność pokoju');

        $this->seeInDatabase('reservations', [
            'id'      => $reservation->id,
            'room_id' => $room->id,
        ]);
    }

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

    public function testTryDeleteInvalidId()
    {
        $response = $this->call('DELETE', 'reservation/delete/1000', [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Nie znaleziono obiektu', $this->decodeResponseJson()['message']);
    }

    public function testEmptyFreeRooms()
    {
        $this->visit('room/free')
            ->dontSee('Zaloguj')
            ->see('Aktualnie wolne pokoje')
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

    public function testFilledFreeRooms()
    {
        factory(Room::class, 3)->create();

        $this->visit('room/free')
            ->dontSee('Zaloguj')
            ->see('Aktualnie wolne pokoje')
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
            ->see('Pokoje')
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

    public function testFilledOccupiedRooms()
    {
        factory(Reservation::class)->create();

        $this->visit('room/occupied')
            ->dontSee('Zaloguj')
            ->see('Aktualnie zajęte pokoje')
            ->dontSee('Użytkownicy')
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
}
