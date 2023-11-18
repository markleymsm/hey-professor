<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, put};

it('should be able to publish a question', function () {
    // Arrange - criar algumas perguntas
    $user     = User::factory()->create();
    $question = Question::factory()->create(['draft' => false]);
    actingAs($user);

    // Act - acessar a rota
    put(route('question.publish', $question))->assertRedirect();
    $question->refresh();

    // Assert - verificar se a lista de perguntas está sendo mostrada
    expect($question)->draft->toBeFalse();
});
