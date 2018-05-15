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

        $user = factory(User::class)->make(['is_admin' => true]);
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

        $this->actingAs(factory(User::class)->make());

        $this->visit('user')
            ->seePageIs('home');
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
        $objects = factory(User::class, 2)->create();
        $object = $objects[1];

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

    public function testChangePassword()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('old_correct_pass')
        ]);

        $this->actingAs($user);

        $this->visit('user/change_password')
            ->seePageIs('user/change_password')
            ->type('old_pass', 'current_password')
            ->type('new_test_password', 'new_password')
            ->type('new_test_password2', 'new_password_confirmation')
            ->press('Zmień hasło')
            ->see('Aktualne hasło jest nieprawidłowe.')
            ->see('Potwierdzenie nowe hasło nie zgadza się.');

        $this->type('old_correct_pass', 'current_password')
            ->type('new_test_password', 'new_password')
            ->type('new_test_password', 'new_password_confirmation')
            ->press('Zmień hasło')
            ->dontSee('Aktualne hasło jest nieprawidłowe.')
            ->dontSee('Potwierdzenie nowe hasło nie zgadza się.');
    }
}
