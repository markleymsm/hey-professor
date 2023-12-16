<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, assertDatabaseCount, assertDatabaseHas, post};

it('should be able to create a new question bigger than 255 characters', function () {
    // Arrange:: preperar
    $user = User::factory()->create();
    actingAs($user);

    // Act:: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 260) . "?",
    ]);

    // Assert:: verificar
    $request->assertRedirect();

    assertDatabaseCount('questions', 1);
    assertDatabaseHas('questions', [
        'question' => str_repeat('*', 260) . "?",
    ]);
});

it('should check if ends with question mark ?', function () {
    // Arrange:: preperar
    $user = User::factory()->create();
    actingAs($user);

    // Act:: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 10),
    ]);

    // Assert:: verificar
    $request->assertSessionHasErrors([
        'question' => 'Are you sure that is a question? It is missing the question mark in the end.',
    ]);

    assertDatabaseCount('questions', 0);
});

it('should have at least 10 characters', function () {
    // Arrange:: preperar
    $user = User::factory()->create();
    actingAs($user);

    // Act:: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 8) . "?",
    ]);

    // Assert:: verificar
    $request->assertSessionHasErrors(['question' => __('validation.min.string', ['min' => 10, 'attribute' => 'question'])]);

    assertDatabaseCount('questions', 0);
});

it('should create as a draft all the time', function () {
    // Arrange:: preperar
    $user = User::factory()->create();
    actingAs($user);

    // Act:: agir
    post(route('question.store'), [
        'question' => str_repeat('*', 260) . "?",
    ]);

    // Assert:: verificar
    assertDatabaseHas('questions', [
        'question' => str_repeat('*', 260) . '?',
        'draft'    => true,
    ]);
});

test('only authenticated users can create a new question', function () {
    post(route('question.store'), [
        'question' => str_repeat('*', 10) . "?",
    ])->assertRedirect(route('login'));

});

test('question shoul be unique', function () {
    $user = User::factory()->create();
    actingAs($user);

    Question::factory()->create(['question' => 'Alguma pergunta para estar ?']);

    post(route('question.store'), ['question' => 'Alguma pergunta para estar ?'])
    ->assertSessionHasErrors(['question' => 'Pergunta já existe!']);
});
