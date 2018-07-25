<?php

use Illuminate\Database\Seeder;
use App\Singer;

class SingerTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('singers')->delete();
		
		Singer::create([
			'name'	=> 'John Smith', 
			'email'	=> 'haydenbech+s1@gmail.com',
		]);
		
		Singer::create([
			'name'	=> 'Bob Citizen', 
			'email'	=> 'haydenbech+s2@gmail.com',
		]);
		
		Singer::create([
			'name'	=> 'Mister Person', 
			'email'	=> 'haydenbech+s3@gmail.com',
		]);
		
	}
}