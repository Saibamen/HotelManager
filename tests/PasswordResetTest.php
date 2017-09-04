<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class PasswordResetTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        Session::start();
    }

    public function testEmptyPasswordReset()
    {
        $this->visit('password/reset')
            ->see('Zresetuj hasło')
            ->dontSee('Pokoje')
            ->type('', 'email')
            ->press('Wyślij link na email')
            ->seePageIs('password/reset')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje')
            ->see('Pole adres e-mail jest wymagane.');
    }

    public function testSimpleFailPasswordReset()
    {
        $this->visit('password/reset')
            ->see('Zresetuj hasło')
            ->dontSee('Pokoje')
            ->type('badEmail', 'email')
            ->press('Wyślij link na email')
            ->seePageIs('password/reset')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje')
            ->see('Format adres e-mail jest nieprawidłowy.');
    }

    public function testUserNotInDatabasePasswordReset()
    {
        $this->visit('password/reset')
            ->see('Zresetuj hasło')
            ->dontSee('Pokoje')
            ->type('valid-email@test.com', 'email')
            ->press('Wyślij link na email')
            ->seePageIs('password/reset')
            ->see('Nie znaleziono użytkownika z takim adresem e-mail')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje');

        $this->dontSeeInDatabase('users', [
            'email' => 'valid-email@test.com',
        ]);
    }

    public function testFactoryLoggedUserCannotResetPassword()
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
            ->visit('password/reset')
            ->seePageIs('home')
            ->see($this->fakeUser->name)
            ->see('You are logged in!')
            ->see('Pokoje');
    }
}
