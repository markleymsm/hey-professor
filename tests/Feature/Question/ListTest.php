<?php

use App\Models\{Question, User};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

it('should paginate the result', function () {
    $user = User::factory()->create();
    Question::factory()->count(20)->create();

    actingAs($user);
    get(route('dashboard'))->assertViewHas('questions', fn ($value) => $value instanceof LengthAwarePaginator);
});
