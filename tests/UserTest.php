<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Input;

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
            ->seePageIs('room');
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
            ->dontSee('Edytuj')
            ->see('Usuń')
            ->dontSee('Brak użytkowników w bazie danych')
            ->see('Dodaj');
    }

    public function testAddEmptyForm()
    {
        $this->visit('user/add')
            ->seePageIs('user/add')
            ->see('Dodaj użytkownika')
            ->type('', 'name')
            ->type('', 'email')
            ->type('', 'password')
            ->type('', 'password_confirmation')
            ->press('Wyślij')
            ->seePageIs('user/add')
            ->see('Pole adres e-mail jest wymagane.')
            ->see('Pole hasło jest wymagane.');

        $this->actingAs(factory(User::class)->make());

        $this->visit('user/add')
            ->seePageIs('room');
    }

    public function testSimpleFailAddForm()
    {
        $this->visit('user/add')
            ->seePageIs('user/add')
            ->see('Dodaj użytkownika')
            ->type('name', 'name')
            ->type('badEmail', 'email')
            ->type('bad', 'password')
            ->type('badPassConfirm', 'password_confirmation')
            ->press('Wyślij')
            ->seePageIs('user/add')
            ->see('Pole adres e-mail nie jest poprawnym adresem e-mail.')
            ->see('Hasło musi mieć przynajmniej 6 znaków.');
    }

    public function testSimpleFailAddFormBadPassConfirm()
    {
        $this->visit('user/add')
            ->seePageIs('user/add')
            ->see('Dodaj użytkownika')
            ->type('name', 'name')
            ->type('badEmail', 'email')
            ->type('badPass4', 'password')
            ->type('badPassConfirm', 'password_confirmation')
            ->press('Wyślij')
            ->seePageIs('user/add')
            ->see('Pole adres e-mail nie jest poprawnym adresem e-mail.')
            ->see('Potwierdzenie pola hasło nie zgadza się.')
            ->assertNull(Input::get('password'));
    }

    public function testSimpleFailAddFormDuplicatedEmail()
    {
        $user = factory(User::class)->create([
            'email' => 'duplicate@example.com',
        ]);

        $this->seeInDatabase('users', [
            'name'     => $user->name,
            'email'    => 'duplicate@example.com',
            'is_admin' => false,
        ]);

        $this->visit('user/add')
            ->seePageIs('user/add')
            ->see('Dodaj użytkownika')
            ->type('name', 'name')
            ->type($user->email, 'email')
            ->type('correctpassword', 'password')
            ->type('correctpassword', 'password_confirmation')
            ->press('Wyślij')
            ->seePageIs('user/add')
            ->see('Taki adres e-mail już występuje.')
            ->dontSee('Potwierdzenie pola hasło nie zgadza się.')
            ->assertNull(Input::get('password'));
    }

    public function testSimpleCorrectAddForm()
    {
        $this->visit('user/add')
            ->seePageIs('user/add')
            ->see('Dodaj użytkownika')
            ->type('Valid Name', 'name')
            ->type('valid-email@test.com', 'email')
            ->type('correctpassword', 'password')
            ->type('correctpassword', 'password_confirmation')
            ->press('Wyślij')
            ->seePageIs('user');

        $this->seeInDatabase('users', [
            'name'     => 'Valid Name',
            'email'    => 'valid-email@test.com',
            'is_admin' => false,
        ]);
    }

    public function testAdminCorrectAddForm()
    {
        $this->visit('user/add')
            ->seePageIs('user/add')
            ->see('Dodaj użytkownika')
            ->type('Valid Name', 'name')
            ->type('valid-email@test.com', 'email')
            ->type('correctpassword', 'password')
            ->type('correctpassword', 'password_confirmation')
            ->check('is_admin')
            ->press('Wyślij')
            ->seePageIs('user');

        $this->seeInDatabase('users', [
            'name'     => 'Valid Name',
            'email'    => 'valid-email@test.com',
            'is_admin' => true,
        ]);
    }

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

    public function testTryDeleteInvalidId()
    {
        $response = $this->call('DELETE', 'user/delete/1000', [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Nie można usunąć tego obiektu', $this->decodeResponseJson()['message']);
    }

    public function testCannotDeleteYourself()
    {
        $object = factory(User::class)->create(['is_admin' => true]);
        $this->actingAs($object);

        $this->seeInDatabase('users', [
            'id' => $object->id,
        ]);

        $response = $this->call('DELETE', 'user/delete/'.$object->id, [
            '_token' => csrf_token(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Nie można usunąć tego obiektu', $responseData['message']);

        $this->seeInDatabase('users', [
            'id' => $object->id,
        ]);
    }

    public function testChangePassword()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('old_correct_pass'),
        ]);

        $this->actingAs($user);

        $this->visit('user/change_password')
            ->seePageIs('user/change_password')
            ->type('old_pass', 'current_password')
            ->type('new_test_password', 'new_password')
            ->type('new_test_password2', 'new_password_confirmation')
            ->press('Zmień hasło')
            ->see('Aktualne hasło jest nieprawidłowe.')
            ->see('Potwierdzenie pola nowe hasło nie zgadza się.');

        $this->type('old_correct_pass', 'current_password')
            ->type('new_test_password', 'new_password')
            ->type('new_test_password', 'new_password_confirmation')
            ->press('Zmień hasło')
            ->dontSee('Aktualne hasło jest nieprawidłowe.')
            ->dontSee('Potwierdzenie pola nowe hasło nie zgadza się.');
    }
}
