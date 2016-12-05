<?php

Route::get(config('laravel-solarium.uri'), 'Fbf\LaravelSolarium\SearchController@results');