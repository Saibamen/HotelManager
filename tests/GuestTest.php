<?php

use App\Models\Guest;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GuestTest extends BrowserKitTestCase
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
        $this->visit('guest')
            ->dontSee('Zaloguj')
            ->see('Goście')
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

    public function testFilledIndex()
    {
        factory(Guest::class, 3)->create();

        $this->visit('guest')
            ->dontSee('Zaloguj')
            ->see('Goście')
            ->see('Imię')
            ->see('Nazwisko')
            ->see('Adres')
            ->see('PESEL')
            ->see('Kontakt')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->see('test contact')
            ->dontSee('Brak gości w bazie danych')
            ->see('Dodaj');
    }

    public function testAddEmptyForm()
    {
        $this->visit('guest/add')
            ->dontSee('Zaloguj')
            ->see('Dodaj gościa')
            ->see('Imię')
            ->see('Nazwisko')
            ->see('Adres')
            ->see('Kod pocztowy')
            ->see('Miejscowość')
            ->see('PESEL')
            ->see('Kontakt')
            ->see('Wyślij')
            ->press('Wyślij');

        $this->see('jest wymagane')
            ->seePageIs('guest/add');
    }

    public function testAddNewObject()
    {
        $object = factory(Guest::class)->make();

        $this->visit('guest/add')
            ->dontSee('Zaloguj')
            ->see('Dodaj gościa')
            ->type($object->first_name, 'first_name')
            ->type($object->last_name, 'last_name')
            ->type($object->address, 'address')
            ->type($object->zip_code, 'zip_code')
            ->type($object->place, 'place')
            ->type($object->PESEL, 'PESEL')
            ->type('test contact', 'contact')
            ->press('Wyślij');

        $this->see($object->first_name)
            ->see($object->last_name)
            ->see($object->address)
            ->see($object->zip_code)
            ->see($object->place)
            ->see($object->PESEL)
            ->see('test contact')
            ->dontSee('Brak gości w bazie danych')
            ->see('Zapisano pomyślnie')
            ->seePageIs('guest');

        $this->seeInDatabase('guests', [
            'first_name' => $object->first_name,
            'last_name'  => $object->last_name,
        ]);
    }

    public function testTryEditInvalidId()
    {
        $this->visit('guest')
            ->see('Goście')
            ->visit('guest/edit/10000');

        $this->see('Nie znaleziono obiektu')
            ->seePageIs('guest');
    }

    public function multilangualGuestProvider()
    {
        $this->createApplication();

        return [
            [factory(Guest::class)->make()],
            [factory(Guest::class)->states('polish')->make()],
            [factory(Guest::class)->states('german')->make()],
            [factory(Guest::class)->states('french')->make()],
            [factory(Guest::class)->states('belarus')->make()],
            [factory(Guest::class)->states('czech')->make()],
        ];
    }

    /**
     * @dataProvider multilangualGuestProvider
     *
     * @param Guest $guest
     */
    public function testEditValidId($guest)
    {
        $guest->save();

        $this->visit('guest')
            ->see('Goście')
            ->visit('guest/edit/'.$guest->id);

        $this->see('Edytuj gościa')
            ->see('Imię')
            ->see('Nazwisko')
            ->see('Adres')
            ->see('Kod pocztowy')
            ->see('Miejscowość')
            ->see('PESEL')
            ->see('Kontakt')
            ->see('test contact')
            ->see('Wyślij');

        $this->type('Edycja kontaktu', 'contact')
            ->press('Wyślij');

        $this->see('Zapisano pomyślnie')
            ->seePageIs('guest')
            ->see('Edycja kontaktu');
    }

    public function testTryStoreInvalidId()
    {
        $response = $this->call('POST', 'guest/edit/1000', [
            '_token'     => csrf_token(),
            'first_name' => 'aa',
            'last_name'  => 'aa',
            'address'    => 'aa',
            'zip_code'   => '11-111',
            'place'      => 'aa',
            'PESEL'      => 12345678912,
        ]);

        $this->assertEquals(302, $response->status());

        $this->assertRedirectedToRoute('home')
            ->seeInSession('message', 'Nie znaleziono obiektu');
    }

    public function testDelete()
    {
        $object = factory(Guest::class)->create();

        $this->seeInDatabase('guests', [
            'id' => $object->id,
        ]);

        $response = $this->call('DELETE', 'guest/delete/'.$object->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('guests', [
            'id' => $object->id,
        ]);
    }

    public function testTryDeleteInvalidId()
    {
        $response = $this->call('DELETE', 'guest/delete/1000', [
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

        $response = $this->call('DELETE', 'guest/delete/'.$reservation->guest->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('guests', [
            'id' => $reservation->guest->id,
        ]);

        $this->notSeeInDatabase('reservations', [
            'id' => $reservation->id,
        ]);

        $this->seeInDatabase('rooms', [
            'id' => $reservation->room->id,
        ]);
    }
}
