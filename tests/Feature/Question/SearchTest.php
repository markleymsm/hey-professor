<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, get};

it('Should be able to search a question by text', function () {
    $user          = User::factory()->create();
    $wrongQuestion = Question::factory()->create(['question' => 'Someting elseeeee?']);
    $question      = Question::factory()->create(['question' => 'My question is ?']);

    actingAs($user);

    $response = get(route('dashboard', ['search' => 'question']));

    $response->assertDontSee('Someting elseeeee?');

    $response->assertSee('My question is ?');
});
