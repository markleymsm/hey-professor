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

it('should order by like and unlike, most liked question should be at the top, most unlike questions should be in the bottom', function () {
    $user       = User::factory()->create();
    $secondUser = User::factory()->create();

    Question::factory()->count(5)->create();

    $mostLikedQuestion   = Question::find(3);
    $mostUnlikedQuestion = Question::find(1);

    $user->like($mostLikedQuestion);
    $secondUser->unlike($mostUnlikedQuestion);

    actingAs($user);
    get(route('dashboard'))->assertViewHas('questions', function ($questions) {
        expect($questions)->first()->id->toBe(3)
        ->and($questions)
        ->last()->id->toBe(1);

        return true;
    });
});
