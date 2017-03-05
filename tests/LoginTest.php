<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() {
        parent::setUp();
        Session::start();

        $this->fakeUser = factory(App\Models\User::class)->create([
            'password' => bcrypt('testpass123')
        ]);
    }

    public function testEmptyLogin() {
        $this->visit('login')
            ->see('Zaloguj')
            ->type('', 'email')
            ->type('', 'password')
            ->press('Zaloguj')
            ->seePageIs('login')
            ->dontSee('Cześć')
            ->see('Pole adres e-mail jest wymagane.')
            ->see('Pole hasło jest wymagane.');
    }

    public function testSimpleFailLogin() {
        $this->visit('login')
            ->see('Zaloguj')
            ->type('badEmail', 'email')
            ->type('badPass', 'password')
            ->press('Zaloguj')
            ->seePageIs('login')
            ->dontSee('Cześć')
            ->see('Błędny login lub hasło.');
    }

    public function testFactoryLogin() {
        $this->visit('login')
            ->see('Zaloguj')
            ->type($this->fakeUser->email, 'email')
            ->type('testpass123', 'password')
            ->press('Zaloguj')
            ->seePageIs('home')
            ->see($this->fakeUser->name)
            ->see('You are logged in!')
            ->dontSee('Błędny login lub hasło.');
    }

    public function testFactoryLoginLogout() {
        $email = $this->fakeUser->email;
        $password = 'testpass123';

        $response = $this->call('POST', 'login', [
            '_token' => csrf_token(),
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertEquals(302, $response->status());
        $this->assertRedirectedToRoute('home');

        $this->visit('/')->dontSee('Zaloguj')->see($this->fakeUser->name);

        $response = $this->call('POST', 'logout', ['_token' => csrf_token()]);

        $this->assertEquals(302, $response->status());
        $this->assertRedirectedTo('/');

        $this->visit('login')->seePageIs('login')->dontSee($this->fakeUser->name);
    }
}
