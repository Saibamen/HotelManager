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
            ->dontSee('Kod pocztowy')
            ->dontSee('Miejscowość')
            ->dontSee('PESEL')
            ->dontSee('Kontakt')
            ->dontSee('Akcje')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
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
            ->see('Kod pocztowy')
            ->see('Miejscowość')
            ->see('PESEL')
            ->see('Kontakt')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->see('test contact')
            ->see('Dodaj');
    }

    public function testAddEmptyForm()
    {
        $this->visit('guest/add')
            ->dontSee('Zaloguj')
            //->see('Dodaj pokój')
            ->see('Imię')
            ->see('Nazwisko')
            ->see('Adres')
            ->see('Kod pocztowy')
            ->see('Miejscowość')
            ->see('PESEL')
            ->see('Kontakt')
            ->see('Wyślij')
            ->press('Wyślij');

        //$this->seePageIs('guest/add')
        //->see('jest wymagane');
    }

    public function testTryEditInvalidId()
    {
        $this->visit('guest')
            ->see('Goście')
            ->visit('guest/edit/10000');

        $this->followRedirects();

        $this->see('Nie znaleziono obiektu');
    }

    public function testEditValidId()
    {
        $guest = factory(Guest::class)->create();

        $this->visit('guest')
            ->see('Goście')
            ->visit('guest/edit/'.$guest->id);

        //$this->see('Edytuj pokój')
        $this->see('Imię')
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

        $this->seePageIs('guest')
            ->see('Zapisano pomyślnie')
            ->see('Edycja kontaktu');
    }

    public function testDelete()
    {
        $guest = factory(Guest::class)->create();

        $this->seeInDatabase('guests', [
            'ID' => $guest->id,
        ]);

        $response = $this->call('DELETE', 'guest/delete/'.$guest->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('guests', [
            'ID' => $guest->id,
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
