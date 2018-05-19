<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        Session::start();
    }

    public function testFactoryLoggedUserCannotRegister()
    {
        $this->fakeUser = factory(\App\Models\User::class)->create();

        if ($this->actingAs($this->fakeUser) == null) {
            $this->markTestSkipped('FakeUser not working...');
        }

        $this->actingAs($this->fakeUser)
            ->visit('/')
            ->seePageIs('room')
            ->see($this->fakeUser->name)
            ->see('Pokoje')
            ->dontSee('Zarejestruj');

        $response = $this->call('GET', 'register');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testRegisterIsDisabled()
    {
        $this->visit('login')
            ->seePageIs('login')
            ->see('Zaloguj')
            ->dontSee('Zarejestruj');

        $response = $this->call('GET', 'register');

        $this->assertEquals(404, $response->getStatusCode());
    }
}
