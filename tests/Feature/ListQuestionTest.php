<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, get};

it('should be list all the questions', function () {
    // Arrange - criar algumas perguntas
    $user     = User::factory()->create();
    $quetions = Question::factory()->count(5)->create();

    actingAs($user);
    // Act - acessar a rota
    $response = get(route('dashboard'));

    // Assert - verificar se a lista de perguntas está sendo mostrada

    /** @var Question $value */
    foreach ($quetions as $value) {
        $response->assertSee($value->question);
    }
});
