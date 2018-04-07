<?php

use App\Models\Guest;
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

    public function testTryEditInvalidId()
    {
        $this->visit('guest')
            ->see('Goście')
            ->visit('guest/edit/10000');

        $this->see('Nie znaleziono obiektu')
            ->seePageIs('guest');
    }

    public function testEditValidId()
    {
        $guest = factory(Guest::class)->create();

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

    public function testDelete()
    {
        $guest = factory(Guest::class)->create();

        $this->seeInDatabase('guests', [
            'id' => $guest->id,
        ]);

        $response = $this->call('DELETE', 'guest/delete/'.$guest->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('guests', [
            'id' => $guest->id,
        ]);
    }

    public function testTryStoreInvalidId()
    {
        $this->makeRequest('POST', 'guest/edit/1000', [
            '_token' => csrf_token(),
        ]);

        $this->notSeeInDatabase('guests', []);
    }
}
