<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
use App\Models\User;
$user = User::where('email', 'admin@zanistv.com')->first();
if ($user) {
    $user->password = bcrypt('NewPass123!');
    $user->save();
    echo "Password reset successfully for admin@zanistv.com\n";
} else {
    echo "User not found\n";
}