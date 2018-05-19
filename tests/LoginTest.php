<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    private $fakeUser;

    public function setUp()
    {
        parent::setUp();
        Session::start();

        $this->fakeUser = factory(App\Models\User::class)->create([
            'password' => Hash::make('testpass123'),
        ]);
    }

    public function testEmptyLogin()
    {
        $this->visit('login')
            ->see('Zaloguj')
            ->type('', 'email')
            ->type('', 'password')
            ->press('Zaloguj')
            ->seePageIs('login')
            ->dontSee('Cześć')
            ->dontSee('Pokoje')
            ->see('Pole adres e-mail jest wymagane.')
            ->see('Pole hasło jest wymagane.');
    }

    public function testSimpleFailLogin()
    {
        $this->visit('login')
            ->see('Zaloguj')
            ->type('badEmail', 'email')
            ->type('badPass', 'password')
            ->press('Zaloguj')
            ->seePageIs('login')
            ->dontSee('Cześć')
            ->dontSee('Pokoje')
            ->see('Błędny login lub hasło.');
    }

    public function testFactoryLogin()
    {
        $this->visit('login')
            ->see('Zaloguj')
            ->type($this->fakeUser->email, 'email')
            ->type('testpass123', 'password')
            ->press('Zaloguj')
            ->seePageIs('room')
            ->see('Witaj, '.$this->fakeUser->name)
            ->see('Wyloguj')
            ->see('Pokoje')
            ->dontSee('Błędny login lub hasło.');
    }

    public function testFactoryLoginLogout()
    {
        $email = $this->fakeUser->email;
        $password = 'testpass123';

        $response = $this->call('POST', 'login', [
            '_token'   => csrf_token(),
            'email'    => $email,
            'password' => $password,
        ]);

        $this->assertEquals(302, $response->status());
        $this->assertRedirectedToRoute('room.index');

        $this->visit('/')
            ->dontSee('Zaloguj')
            ->see($this->fakeUser->name)
            ->see('Pokoje');

        $response = $this->call('POST', 'logout', ['_token' => csrf_token()]);

        $this->assertEquals(302, $response->status());
        $this->assertRedirectedToRoute('login');

        $this->visit('login')
            ->seePageIs('login')
            ->see('Zaloguj')
            ->dontSee($this->fakeUser->name)
            ->dontSee('Pokoje');
    }

    public function testFactoryLoggedUserCannotLoginAgain()
    {
        if ($this->actingAs($this->fakeUser) == null) {
            $this->markTestSkipped('FakeUser not working...');
        }

        $this->actingAs($this->fakeUser)
            ->visit('/')
            ->see($this->fakeUser->name)
            ->see('Pokoje')
            ->visit('login')
            ->seePageIs('room')
            ->see($this->fakeUser->name)
            ->see('Pokoje');
    }

    public function testNotLoggedUserCannotSeeRooms()
    {
        $this->visit('room')
            ->seePageIs('login')
            ->see('Zaloguj')
            ->dontSee('Pokoje');
    }
}
