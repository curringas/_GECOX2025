<?php

namespace Tests\Unit;

use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UserTwitterValidationTest extends TestCase
{
    private function twitterRule(): array
    {
        $rules = (new UserRequest())->rules();

        return ['twitter' => $rules['twitter'] ?? []];
    }

    public function test_acepta_una_url_de_twitter_valida(): void
    {
        $validator = Validator::make(
            ['twitter' => 'https://twitter.com/granadaenjuego'],
            $this->twitterRule()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_rechaza_un_twitter_que_no_es_url(): void
    {
        $validator = Validator::make(
            ['twitter' => '@granadaenjuego'],
            $this->twitterRule()
        );

        $this->assertTrue($validator->fails());
    }

    public function test_permite_el_twitter_vacio(): void
    {
        $validator = Validator::make(
            ['twitter' => null],
            $this->twitterRule()
        );

        $this->assertFalse($validator->fails());
    }
}
