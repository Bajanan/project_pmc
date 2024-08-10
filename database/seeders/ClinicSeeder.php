<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clinics')->insert([
            'clinic_name' => "Pillayar Medi Clinic",
            'email' => 'admin@gmail.com',
            'phone' => '0712345678',
            'mobile' => "0712345678",
            'bill_message' => "Thank you for visiting us!",
            'clinic_address'=>"Atchuveli, Jaffna",
            'clinic_logo'=> url('uploads/1721746180.png') 
        ]);
    }
}
