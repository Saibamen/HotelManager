<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Input;

class RegisterTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        Session::start();
    }

    public function testEmptyRegister()
    {
        $this->visit('register')
            ->see('Zarejestruj')
            ->dontSee('Pokoje')
            ->type('', 'name')
            ->type('', 'email')
            ->type('', 'password')
            ->type('', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje')
            ->see('Pole adres e-mail jest wymagane.')
            ->see('Pole hasło jest wymagane.');
    }

    public function testSimpleFailRegister()
    {
        $this->visit('register')
            ->see('Zarejestruj')
            ->dontSee('Pokoje')
            ->type('name', 'name')
            ->type('badEmail', 'email')
            ->type('bad', 'password')
            ->type('badPassConfirm', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje')
            ->see('Format adres e-mail jest nieprawidłowy.')
            ->see('Hasło musi mieć przynajmniej 6 znaków.');
    }

    public function testSimpleFailRegisterBadPassConfirm()
    {
        $this->visit('register')
            ->see('Zarejestruj')
            ->dontSee('Pokoje')
            ->type('name', 'name')
            ->type('badEmail', 'email')
            ->type('badPass4', 'password')
            ->type('badPassConfirm', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje')
            ->see('Format adres e-mail jest nieprawidłowy.')
            ->see('Potwierdzenie hasło nie zgadza się.')
            ->assertNull(Input::get('password'));
    }

    public function testSimpleCorrectRegister()
    {
        $this->visit('register')
            ->see('Zarejestruj')
            ->dontSee('Pokoje')
            ->type('Valid Name', 'name')
            ->type('valid-email@test.com', 'email')
            ->type('correctpassword', 'password')
            ->type('correctpassword', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('home')
            ->see('Wyloguj')
            ->see('Pokoje');

        $this->seeInDatabase('users', [
            'name'  => 'Valid Name',
            'email' => 'valid-email@test.com',
        ]);
    }

    public function testFactoryLoggedUserCannotRegister()
    {
        $this->fakeUser = factory(App\Models\User::class)->create([
            'password' => bcrypt('testpass123'),
        ]);

        if ($this->actingAs($this->fakeUser) == null) {
            $this->markTestSkipped('FakeUser not working...');
        }

        $this->actingAs($this->fakeUser)
            ->visit('home')
            ->see($this->fakeUser->name)
            ->see('You are logged in!')
            ->see('Pokoje')
            ->visit('register')
            ->seePageIs('home')
            ->see($this->fakeUser->name)
            ->see('You are logged in!')
            ->see('Pokoje');
    }
}
