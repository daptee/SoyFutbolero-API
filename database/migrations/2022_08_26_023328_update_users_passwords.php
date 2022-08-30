<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class UpdateUsersPasswords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::where('is_admin',0)->get();
        // hash
        $key = hash("sha256", "soy_futbolero_secretKey!");
        $iv = substr(hash("sha256", "soy_futbolero_secretIV!"), 0, 16);

        foreach($users as $user){
            $password = openssl_decrypt(base64_decode($user->password), "AES-256-CBC", $key, 0, $iv);

            if(!$password){
                continue;
            }

            $user->password = (string)bcrypt($password);
            $user->update();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
