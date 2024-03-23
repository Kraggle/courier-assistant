<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {

        $tables = DB::select("SELECT * FROM information_schema.tables;");
        foreach ($tables as $table) {
            if ($table->TABLE_TYPE == 'BASE TABLE' && $table->TABLE_NAME != 'migrations') {
                DB::table($table->TABLE_NAME)->truncate();
            }
        }

        // Table - Users
        $users = [[
            'name' => 'Kraig Larner',
            'email' => 'kraggle27@gmail.com'
        ]];
        foreach ($users as $user) {
            DB::table('users')->insert(array_merge([
                'password' => Hash::make('mysecret'),
                'created_at' => now(),
                'updated_at' => now()
            ], $user));
        }



        // Table - Routes
        $routes = [[
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 27),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 71901,
            'end_mileage' => 71901,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 26),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('16:45'),
            'start_mileage' => 71728,
            'end_mileage' => 71887,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 25),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:40'),
            'start_mileage' => 71561,
            'end_mileage' => 71711,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 23),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:20'),
            'start_mileage' => 71372,
            'end_mileage' => 71545,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 22),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('16:20'),
            'start_mileage' => 71225,
            'end_mileage' => 71359,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 21),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 71199,
            'end_mileage' => 71199,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 20),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 71171,
            'end_mileage' => 71171,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 19),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:30'),
            'start_mileage' => 71020,
            'end_mileage' => 71157,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 16),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:40'),
            'start_mileage' => 70869,
            'end_mileage' => 70987,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 15),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:00'),
            'start_mileage' => 70705,
            'end_mileage' => 70856,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 14),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 70680,
            'end_mileage' => 70680,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 13),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 70654,
            'end_mileage' => 70654,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 12),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 70622,
            'end_mileage' => 70622,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 11),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:10'),
            'start_mileage' => 70440,
            'end_mileage' => 70609,
            'invoice_mileage' => 209,
            'invoice_fuel_rate' => 0.2090,
            'stops' => 106,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 9),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 70396,
            'end_mileage' => 70396,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 8),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:00'),
            'start_mileage' => 70213,
            'end_mileage' => 70382,
            'invoice_mileage' => 185,
            'invoice_fuel_rate' => 0.2090,
            'stops' => 100,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 7),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 70091,
            'end_mileage' => 70091,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 6),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 70091,
            'end_mileage' => 70091,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 5),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 70091,
            'end_mileage' => 70091,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 4),
            'start_time' => Carbon::createFromTimeString('09:00'),
            'end_time' => Carbon::createFromTimeString('16:00'),
            'start_mileage' => 70091,
            'end_mileage' => 70091,
            'type' => 'POC'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 3),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('16:40'),
            'start_mileage' => 69955,
            'end_mileage' => 70091,
            'invoice_mileage' => 136,
            'invoice_fuel_rate' => 0.2084,
            'stops' => 103,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2024, 1, 2),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:20'),
            'start_mileage' => 69772,
            'end_mileage' => 69927,
            'invoice_mileage' => 171,
            'invoice_fuel_rate' => 0.2084,
            'stops' => 78,
            'type' => 'Standard'
        ], [
            'user_id' => 1,
            'date' => Carbon::create(2023, 12, 31),
            'start_time' => Carbon::createFromTimeString('10:20'),
            'end_time' => Carbon::createFromTimeString('17:40'),
            'start_mileage' => 69586,
            'end_mileage' => 69759,
            'invoice_mileage' => 167,
            'invoice_fuel_rate' => 0.2084,
            'stops' => 99,
            'type' => 'Standard'
        ]];
        // foreach ($routes as $route) {
        //     DB::table('routes')->insert(array_merge([
        //         'created_at' => now(),
        //         'updated_at' => now()
        //     ], $route));
        // }
    }
}
