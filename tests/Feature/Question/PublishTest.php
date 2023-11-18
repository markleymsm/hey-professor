<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, put};

it('should be able to publish a question', function () {
    // Arrange - criar algumas perguntas
    $user     = User::factory()->create();
    $question = Question::factory()
        ->for($user, 'createdBy')
        ->create(['draft' => false]);

    actingAs($user);

    // Act - acessar a rota
    put(route('question.publish', $question))->assertRedirect();
    $question->refresh();

    // Assert - verificar se a lista de perguntas está sendo mostrada
    expect($question)->draft->toBeFalse();
});

it('should make sure that only the person who has created the question can publish the question', function () {
    // Arrange - criar algumas perguntas
    $rightUser = User::factory()->create();
    $wrongUser = User::factory()->create();
    $question  = Question::factory()->create(['draft' => true, 'created_by' => $rightUser->id]);

    actingAs($wrongUser);
    put(route('question.publish', $question))->assertForbidden();

    actingAs($rightUser);
    put(route('question.publish', $question))->assertRedirect();

    // $question->refresh();

    // // Assert - verificar se a lista de perguntas está sendo mostrada
    // expect($question)->draft->toBeFalse();
});
