<?php

declare(strict_types=1);

namespace Tests\Resolvers;

use Illuminate\Support\Facades\Route;
use PreemStudio\DataBags\DataBag;
use PreemStudio\DataBags\Resolvers\RegexResolver;

it('should match by a glob pattern', function () {
    expect((new RegexResolver)->resolve([
        'meta' => [
            '|(/*)|' => [
                'title' => 'Any',
            ],
        ],
    ], 'meta'))->toBe(['title' => 'Any']);
});

it('should match by a glob pattern through a request', function () {
    DataBag::register('meta', [
        '|(posts/hello-*)|' => [
            'title' => 'Hello World',
        ],
        '/(.*)/' => [
            'title' => 'Any',
        ],
    ]);

    Route::get('/', fn () => DataBag::resolveByRegex('meta'));
    Route::get('/posts', fn () => DataBag::resolveByRegex('meta'));
    Route::get('/posts/hello-world', fn () => DataBag::resolveByRegex('meta'));

    expect($this->call('GET', '/')->json())->toBe(['title' => 'Any']);
    expect($this->call('GET', '/posts')->json())->toBe(['title' => 'Any']);
    expect($this->call('GET', '/posts/hello-world')->json())->toBe(['title' => 'Hello World']);
});