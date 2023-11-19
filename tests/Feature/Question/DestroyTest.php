<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, assertDatabaseMissing, delete};

it('should be able to delete a question', function () {
    // Arrange - criar algumas perguntas
    $user     = User::factory()->create();
    $question = Question::factory()
        ->for($user, 'createdBy')
        ->create(['draft' => false]);

    actingAs($user);

    // Act - acessar a rota
    delete(route('question.destroy', $question))->assertRedirect();

    // Assert - verificar se a lista de perguntas está sendo mostrada
    assertDatabaseMissing('questions', ['id' => $question->id]);
});

it('should make sure that only the person who has created the question can destroy the question', function () {
    // Arrange - criar algumas perguntas
    $rightUser = User::factory()->create();
    $wrongUser = User::factory()->create();
    $question  = Question::factory()->create(['draft' => true, 'created_by' => $rightUser->id]);

    actingAs($wrongUser);
    delete(route('question.destroy', $question))->assertForbidden();

    actingAs($rightUser);
    delete(route('question.destroy', $question))->assertRedirect();
});
