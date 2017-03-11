<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Input;

class RegisterTest extends TestCase
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
            ->type('', 'name')
            ->type('', 'email')
            ->type('', 'password')
            ->type('', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Wyloguj')
            ->see('Pole adres e-mail jest wymagane.')
            ->see('Pole hasło jest wymagane.');
    }

    public function testSimpleFailRegister()
    {
        $this->visit('register')
            ->see('Zarejestruj')
            ->type('name', 'name')
            ->type('badEmail', 'email')
            ->type('bad', 'password')
            ->type('badPassConfirm', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Wyloguj')
            ->see('Format adres e-mail jest nieprawidłowy.')
            ->see('Hasło musi mieć przynajmniej 6 znaków.');
    }

    public function testSimpleFailRegisterBadPassConfirm()
    {
        $this->visit('register')
            ->see('Zarejestruj')
            ->type('name', 'name')
            ->type('badEmail', 'email')
            ->type('badPass4', 'password')
            ->type('badPassConfirm', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Wyloguj')
            ->see('Format adres e-mail jest nieprawidłowy.')
            ->see('Potwierdzenie hasło nie zgadza się.')
            ->assertNull(Input::get('password'));
    }

    public function testSimpleCorrectRegister()
    {
        $this->visit('register')
            ->see('Zarejestruj')
            ->type('name', 'name')
            ->type('valid-email@test.com', 'email')
            ->type('correctpassword', 'password')
            ->type('correctpassword', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('home')
            ->see('Wyloguj');
    }
}
