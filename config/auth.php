<?php

return ['defaults' => ['guard' => 'web', 'passwords' => 'users'], 'guards' => ['web' => ['driver' => 'session', 'provider' => 'users']], 'providers' => ['users' => ['driver' => 'eloquent', 'model' => User::class]], 'passwords' => ['users' => ['provider' => 'users', 'table' => 'password_reminders', 'expire' => 60, 'throttle' => 60]]];
