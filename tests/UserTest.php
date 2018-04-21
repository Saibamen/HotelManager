<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        Session::start();

        $user = factory(User::class)->make();
        $this->actingAs($user);
    }

    public function testEmptyIndex()
    {
        $this->visit('user')
            ->dontSee('Zaloguj')
            ->see('Użytkownicy')
            ->dontSee('Nazwa')
            ->dontSee('Adres e-mail')
            ->dontSee('Utworzono')
            ->dontSee('Akcje')
            ->dontSee('Edytuj')
            ->dontSee('Usuń')
            ->see('Brak użytkowników w bazie danych')
            ->see('Dodaj');
    }

    public function testFilledIndex()
    {
        factory(User::class, 3)->create();

        $this->visit('user')
            ->dontSee('Zaloguj')
            ->see('Użytkownicy')
            ->see('Nazwa')
            ->see('Adres e-mail')
            ->see('Utworzono')
            ->see('Akcje')
            ->see('Edytuj')
            ->see('Usuń')
            ->dontSee('Brak użytkowników w bazie danych')
            ->see('Dodaj');
    }

    /*public function testAddEmptyForm()
    {
        $this->visit('user/add')
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
    }*/

    /*public function testAddNewObject()
    {
        $object = factory(User::class)->make();

        $this->visit('user/add')
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
    }*/

    /*public function testTryEditInvalidId()
    {
        $this->visit('user')
            ->see('Pokoje')
            ->visit('user/edit/10000');

        $this->see('Nie znaleziono obiektu')
            ->seePageIs('room');
    }*/

    /*public function testEditValidId()
    {
        $room = factory(Room::class)->create();

        $this->visit('user')
            ->see('Pokoje')
            ->visit('user/edit/'.$room->id);

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
            ->seePageIs('user')
            ->see('Edycja komentarza');
    }*/

    public function testDelete()
    {
        $object = factory(User::class)->create();

        $this->seeInDatabase('users', [
            'id' => $object->id,
        ]);

        $response = $this->call('DELETE', 'user/delete/'.$object->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('users', [
            'id' => $object->id,
        ]);
    }

    /*public function testTryStoreInvalidId()
    {
        $this->makeRequest('POST', 'user/edit/1000', [
            '_token' => csrf_token(),
        ]);

        $this->notSeeInDatabase('rooms', []);
    }*/
}
