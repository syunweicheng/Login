<?php

use Illuminate\Database\Seeder;

class FakerMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('members')->insert([
            'name' => 'user',
            'email' => 'user@email.com',
            'mobile' => '0912345678',
            'password' => bcrypt('aaaaaa'),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
