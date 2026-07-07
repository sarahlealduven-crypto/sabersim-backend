<?php

it('allows guests to view the Scramble documentation in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    $this->get('/docs/api')
        ->assertOk();
});
