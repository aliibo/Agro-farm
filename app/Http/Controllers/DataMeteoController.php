<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Nfroid;
use App\Models\Data_meteo;
use App\Models\maxmin_temp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DataMeteoController extends Controller
{

    public function fc_month_days_avg($val)
    {
        $data = array();
        for ($i = 30; $i >= 0; $i--) {
            $d = Data_meteo::whereDate('created_at', Carbon::today()->subDays($i))->pluck($val)->avg();
            if ($d) {
                $data[] = $d;
            }
        }
        return $data;
    }

    public function data24($val)
    {
        $data = Data_meteo::whereBetween('created_at', [Carbon::now()->subDays(1), Carbon::now()])->get()->pluck($val);

        return $data;
    }

    // temperature -------------------------------------
    public function historique_temperature()
    {
        $temperatures = array();
        $humidites = array();
        $vitesses = array();
        $pluies = array();
        $directions = array();
        $created = array();
        $month_days_temp_avg = array();
        $month_days_humidite_avg = array();
        $month_days_vitesse_avg = array();
        $month_days_pluie_avg = array();
        $month_days_direction_avg = array();
        $month_days_array = array();

        if (count(Data_meteo::all())) {
            if (!Cache::has('temperatures')) {
                $temperatures = $this->data24('temperature');
                Cache::put('temperatures',$temperatures, 600);
            } else {
                $temperatures =  Cache::get('temperatures');
            }
            if (!Cache::has('humidites')) {
                $humidites = $this->data24('humidite');
                Cache::put('humidites',$humidites, 600);
            } else {
                $humidites = Cache::get('humidites');
            }
            if (!Cache::has('vitesses')) {
                $vitesses = $this->data24('vitesse');
                Cache::put('vitesses',$vitesses, 600);
            } else {
                $vitesses = Cache::get('vitesses');
            }
            if (!Cache::has('pluies')) {
                $pluies = $this->data24('pluie');
                Cache::put('pluies',$pluies, 600);
            } else {
                $pluies = Cache::get('pluies');
            }
            if (!Cache::has('directions')) {
                $directions = $this->data24('direction');
                Cache::put('directions',$directions,600);
            } else {
                $directions = Cache::get('directions');
            }
            if (!Cache::has('created')) {
                $created = $this->data24('created_at');
                Cache::put('created',$created, 600);
            } else {
                $created = Cache::get('created');
            }
            // -----
            if (!Cache::has('month_days_temp_avg')) {
                $month_days_temp_avg = $this->fc_month_days_avg('temperature');
                Cache::put($month_days_humidite_avg, 600);
            } else {
                $month_days_temp_avg = Cache::get('month_days_temp_avg');
            }
            if (!Cache::has('month_days_humidite_avg')) {
                $month_days_humidite_avg = $this->fc_month_days_avg('humidite');
                Cache::put($month_days_humidite_avg, 600);
            } else {
                $month_days_humidite_avg = Cache::get('month_days_humidite_avg');
            }
            if (!Cache::has('month_days_vitesse_avg')) {
                $month_days_vitesse_avg = $this->fc_month_days_avg('vitesse');
                Cache::put($month_days_vitesse_avg, 600);
            } else {
                $month_days_vitesse_avg = Cache::get('month_days_vitesse_avg');
            }
            if (!Cache::has('month_days_pluie_avg')) {
                $month_days_pluie_avg = $this->fc_month_days_avg('pluie');
                Cache::put($month_days_pluie_avg, 600);
            } else {
                $month_days_pluie_avg = Cache::get('month_days_pluie_avg');
            }
            if (!Cache::has('month_days_direction_avg')) {
                $month_days_direction_avg = $this->fc_month_days_avg('direction');
                Cache::put($month_days_direction_avg, 600);
            } else {
                $month_days_direction_avg = Cache::get('month_days_direction_avg');
            }

            $days = Data_meteo::whereBetween('created_at', [Carbon::today()->subDays(30), Carbon::now()])->get()->pluck('created_at');
            foreach ($days as $unformatted_date) {
                $date = new \DateTime($unformatted_date);
                $day_no = $date->format('d/m/Y');
                $month_days[] = $day_no;
            }

            $month_days = array_unique($month_days);
            $dates = array_values($month_days);
            foreach ($dates as $dateString) {
                $month_days_array[] = DateTime::createFromFormat('d/m/Y', $dateString)->format("m-d");
            }
        }

        return view('historique.temperature', compact(
            'temperatures',
            'humidites',
            'vitesses',
            'pluies',
            'directions',
            'created',
            'month_days_temp_avg',
            'month_days_humidite_avg',
            'month_days_vitesse_avg',
            'month_days_pluie_avg',
            'month_days_direction_avg',
            'month_days_array'
        ));
    }

    public function chart_temp()
    {
        // Data_meteo::create(['temperature' => rand(0,30),
        // 'humidite' => rand(0,100),
        // 'vitesse' => rand(0,80),
        // 'direction' => rand(0,360),
        // 'pluie' => rand(0,100),]);

        if (!Cache::has('data') && !Cache::has('labels')) {
            $Temperatures = Data_meteo::latest()->take(24)->get()->sortBy('id');
            $labels2 = $Temperatures->pluck('created_at');
            foreach ($labels2 as $date) {
                $labels[] =  Carbon::parse($date)->translatedFormat('D H\h');
            }
            $data = $Temperatures->pluck('temperature');
            Cache::put('data', $data, 600);
            Cache::put('labels', $labels, 600);
        } else {
            $data = Cache::get('data');
            $labels = Cache::get('labels');
        }

        return response()->json(compact('labels', 'data'));
    }
    // End temperature -------------------------------------

    // humidite -------------------------------------
    public function historique_humidite()
    {
        $temperatures = array();
        $humidites = array();
        $vitesses = array();
        $pluies = array();
        $directions = array();
        $created = array();
        $month_days_temp_avg = array();
        $month_days_humidite_avg = array();
        $month_days_vitesse_avg = array();
        $month_days_pluie_avg = array();
        $month_days_direction_avg = array();
        $month_days_array = array();

        if (count(Data_meteo::all())) {

            if (!Cache::has('temperatures')) {
                $temperatures = $this->data24('temperature');
                Cache::put('temperatures', $temperatures, 600);
            } else {
                $temperatures =  Cache::get('temperatures');
            }
            if (!Cache::has('humidites')) {
                $humidites = $this->data24('humidite');
                Cache::put('humidites', $humidites, 600);
            } else {
                $humidites = Cache::get('humidites');
            }
            if (!Cache::has('vitesses')) {
                $vitesses = $this->data24('vitesse');
                Cache::put('vitesses', $vitesses, 600);
            } else {
                $vitesses = Cache::get('vitesses');
            }
            if (!Cache::has('pluies')) {
                $pluies = $this->data24('pluie');
                Cache::put('pluies', $pluies, 600);
            } else {
                $pluies = Cache::get('pluies');
            }
            if (!Cache::has('directions')) {
                $directions = $this->data24('direction');
                Cache::put('directions', $directions, 600);
            } else {
                $directions = Cache::get('directions');
            }
            if (!Cache::has('created')) {
                $created = $this->data24('created_at');
                Cache::put('created', $created, 600);
            } else {
                $created = Cache::get('created');
            }

            if (!Cache::has('month_days_temp_avg')) {
                $month_days_temp_avg  = $this->fc_month_days_avg('temperature');
                Cache::put($month_days_temp_avg, 600);
            }
            if (!Cache::has('month_days_humidite_avg')) {
                $month_days_humidite_avg = $this->fc_month_days_avg('humidite');
                Cache::put($month_days_humidite_avg, 600);
            }
            if (!Cache::has('month_days_vitesse_avg')) {
                $month_days_vitesse_avg = $this->fc_month_days_avg('vitesse');
                Cache::put($month_days_vitesse_avg, 600);
            }
            if (!Cache::has('month_days_pluie_avg')) {
                $month_days_pluie_avg = $this->fc_month_days_avg('pluie');
                Cache::put($month_days_pluie_avg, 600);
            }
            if (!Cache::has('month_days_direction_avg')) {
                $month_days_direction_avg = $this->fc_month_days_avg('direction');
                Cache::put($month_days_direction_avg, 600);
            }

            $days = Data_meteo::whereBetween('created_at', [Carbon::today()->subDays(30), Carbon::today()])->get()->pluck('created_at');
            foreach ($days as $unformatted_date) {
                $date = new \DateTime($unformatted_date);
                $day_no = $date->format('d/m/Y');
                $month_days[] = $day_no;
            }

            $month_days = array_unique($month_days);
            $dates = array_values($month_days);

            foreach ($dates as $dateString) {
                $month_days_array[] = DateTime::createFromFormat('d/m/Y', $dateString)->format("m-d");
            }
        }
        return view('historique.humidite', compact('temperatures', 'humidites', 'vitesses', 'pluies', 'directions', 'created', 'month_days_temp_avg', 'month_days_humidite_avg', 'month_days_vitesse_avg', 'month_days_pluie_avg', 'month_days_direction_avg', 'month_days_array'));
    }

    public function chart_humidite()
    {
        if (!Cache::has('data') && !Cache::has('labels')) {
            $humidites = Data_meteo::latest()->take(24)->get()->sortBy('id');
            $labels2 = $humidites->pluck('created_at');
    
            foreach ($labels2 as $date) {
                $labels[] =  Carbon::parse($date)->translatedFormat('D H\h');
            }
            $data = $humidites->pluck('humidite');
            Cache::put('data', $data, 600);
            Cache::put('labels', $labels, 600);
        } else {
            $data = Cache::get('data');
            $labels = Cache::get('labels');
        }
        
        return response()->json(compact('labels', 'data'));
    }
    // End humidite -------------------------------------

    // Vitesse du vent -------------------------------------
    public function historique_Vitesse()
    {
        $temperatures = array();
        $humidites = array();
        $vitesses = array();
        $pluies = array();
        $directions = array();
        $created = array();
        $month_days_temp_avg = array();
        $month_days_humidite_avg = array();
        $month_days_vitesse_avg = array();
        $month_days_pluie_avg = array();
        $month_days_direction_avg = array();
        $month_days_array = array();

        if (count(Data_meteo::all())) {

            if (!Cache::has('temperatures')) {
                $temperatures = $this->data24('temperature');
                Cache::put('temperatures', $temperatures, 600);
            } else {
                $temperatures =  Cache::get('temperatures');
            }
            if (!Cache::has('humidites')) {
                $humidites = $this->data24('humidite');
                Cache::put('humidites', $humidites, 600);
            } else {
                $humidites = Cache::get('humidites');
            }
            if (!Cache::has('vitesses')) {
                $vitesses = $this->data24('vitesse');
                Cache::put('vitesses', $vitesses, 600);
            } else {
                $vitesses = Cache::get('vitesses');
            }
            if (!Cache::has('pluies')) {
                $pluies = $this->data24('pluie');
                Cache::put('pluies', $pluies, 600);
            } else {
                $pluies = Cache::get('pluies');
            }
            if (!Cache::has('directions')) {
                $directions = $this->data24('direction');
                Cache::put('directions', $directions, 600);
            } else {
                $directions = Cache::get('directions');
            }
            if (!Cache::has('created')) {
                $created = $this->data24('created_at');
                Cache::put('created', $created, 600);
            } else {
                $created = Cache::get('created');
            }

            if (!Cache::has('month_days_temp_avg')) {
                $month_days_temp_avg  = $this->fc_month_days_avg('temperature');
                Cache::put($month_days_temp_avg, 600);
            }
            if (!Cache::has('month_days_humidite_avg')) {
                $month_days_humidite_avg = $this->fc_month_days_avg('humidite');
                Cache::put($month_days_humidite_avg, 600);
            }
            if (!Cache::has('month_days_vitesse_avg')) {
                $month_days_vitesse_avg = $this->fc_month_days_avg('vitesse');
                Cache::put($month_days_vitesse_avg, 600);
            }
            if (!Cache::has('month_days_pluie_avg')) {
                $month_days_pluie_avg = $this->fc_month_days_avg('pluie');
                Cache::put($month_days_pluie_avg, 600);
            }
            if (!Cache::has('month_days_direction_avg')) {
                $month_days_direction_avg = $this->fc_month_days_avg('direction');
                Cache::put($month_days_direction_avg, 600);
            }

            $days = Data_meteo::whereBetween('created_at', [Carbon::today()->subDays(30), Carbon::today()])->get()->pluck('created_at');
            foreach ($days as $unformatted_date) {
                $date = new \DateTime($unformatted_date);
                $day_no = $date->format('d/m/Y');
                $month_days[] = $day_no;
            }

            $month_days = array_unique($month_days);
            $dates = array_values($month_days);

            foreach ($dates as $dateString) {
                $month_days_array[] = DateTime::createFromFormat('d/m/Y', $dateString)->format("m-d");
            }
        }
        return view('historique.vitesse', compact('temperatures', 'humidites', 'vitesses', 'pluies', 'directions', 'created', 'month_days_temp_avg', 'month_days_humidite_avg', 'month_days_vitesse_avg', 'month_days_pluie_avg', 'month_days_direction_avg', 'month_days_array'));
    }

    public function chart_Vitesse()
    {
        if (!Cache::has('data') && !Cache::has('labels')) {
            $vitesses = Data_meteo::latest()->take(24)->get()->sortBy('id');
            $labels2 = $vitesses->pluck('created_at');

            foreach ($labels2 as $date) {
                $labels[] =  Carbon::parse($date)->translatedFormat('D H\h');
            }

            $data = $vitesses->pluck('vitesse');
            Cache::put('data', $data, 600);
            Cache::put('labels', $labels, 600);
        } else {
            $data = Cache::get('data');
            $labels = Cache::get('labels');
        }

        return response()->json(compact('labels', 'data'));
    }
    // End Vitesse du vent -------------------------------------

    // pluie -------------------------------------
    public function historique_pluie()
    {
        $temperatures = array();
        $humidites = array();
        $vitesses = array();
        $pluies = array();
        $directions = array();
        $created = array();
        $month_days_temp_avg = array();
        $month_days_humidite_avg = array();
        $month_days_vitesse_avg = array();
        $month_days_pluie_avg = array();
        $month_days_direction_avg = array();
        $month_days_array = array();

        if (count(Data_meteo::all())) {

            if (!Cache::has('temperatures')) {
                $temperatures = $this->data24('temperature');
                Cache::put('temperatures', $temperatures, 600);
            } else {
                $temperatures =  Cache::get('temperatures');
            }
            if (!Cache::has('humidites')) {
                $humidites = $this->data24('humidite');
                Cache::put('humidites', $humidites, 600);
            } else {
                $humidites = Cache::get('humidites');
            }
            if (!Cache::has('vitesses')) {
                $vitesses = $this->data24('vitesse');
                Cache::put('vitesses', $vitesses, 600);
            } else {
                $vitesses = Cache::get('vitesses');
            }
            if (!Cache::has('pluies')) {
                $pluies = $this->data24('pluie');
                Cache::put('pluies', $pluies, 600);
            } else {
                $pluies = Cache::get('pluies');
            }
            if (!Cache::has('directions')) {
                $directions = $this->data24('direction');
                Cache::put('directions', $directions, 600);
            } else {
                $directions = Cache::get('directions');
            }
            if (!Cache::has('created')) {
                $created = $this->data24('created_at');
                Cache::put('created', $created, 600);
            } else {
                $created = Cache::get('created');
            }

            if (!Cache::has('month_days_temp_avg')) {
                $month_days_temp_avg  = $this->fc_month_days_avg('temperature');
                Cache::put($month_days_temp_avg, 600);
            }
            if (!Cache::has('month_days_humidite_avg')) {
                $month_days_humidite_avg = $this->fc_month_days_avg('humidite');
                Cache::put($month_days_humidite_avg, 600);
            }
            if (!Cache::has('month_days_vitesse_avg')) {
                $month_days_vitesse_avg = $this->fc_month_days_avg('vitesse');
                Cache::put($month_days_vitesse_avg, 600);
            }
            if (!Cache::has('month_days_pluie_avg')) {
                $month_days_pluie_avg = $this->fc_month_days_avg('pluie');
                Cache::put($month_days_pluie_avg, 600);
            }
            if (!Cache::has('month_days_direction_avg')) {
                $month_days_direction_avg = $this->fc_month_days_avg('direction');
                Cache::put($month_days_direction_avg, 600);
            }

            $days = Data_meteo::whereBetween('created_at', [Carbon::today()->subDays(30), Carbon::today()])->get()->pluck('created_at');
            foreach ($days as $unformatted_date) {
                $date = new \DateTime($unformatted_date);
                $day_no = $date->format('d/m/Y');
                $month_days[] = $day_no;
            }

            $month_days = array_unique($month_days);
            $dates = array_values($month_days);

            foreach ($dates as $dateString) {
                $month_days_array[] = DateTime::createFromFormat('d/m/Y', $dateString)->format("m-d");
            }
        }
        return view('historique.pluie', compact('temperatures', 'humidites', 'vitesses', 'pluies', 'directions', 'created', 'month_days_temp_avg', 'month_days_humidite_avg', 'month_days_vitesse_avg', 'month_days_pluie_avg', 'month_days_direction_avg', 'month_days_array'));
    }

    public function chart_pluie()
    {
        if (!Cache::has('data') && !Cache::has('labels')) {
            $pluies = Data_meteo::latest()->take(24)->get()->sortBy('id');
            $labels2 = $pluies->pluck('created_at');
            foreach ($labels2 as $date) {
                $labels[] =  Carbon::parse($date)->translatedFormat('D H\h');
            }
            $data = $pluies->pluck('pluie');   
            Cache::put('data', $data, 600);
            Cache::put('labels', $labels, 600);
        } else {
            $data = Cache::get('data');
            $labels = Cache::get('labels');
        }

        return response()->json(compact('labels', 'data'));
    }
    // End pluie -------------------------------------

    // direction du vent -------------------------------------
    public function historique_direction()
    {
        $temperatures = array();
        $humidites = array();
        $vitesses = array();
        $pluies = array();
        $directions = array();
        $created = array();
        $month_days_temp_avg = array();
        $month_days_humidite_avg = array();
        $month_days_vitesse_avg = array();
        $month_days_pluie_avg = array();
        $month_days_direction_avg = array();
        $month_days_array = array();

        if (count(Data_meteo::all())) {

            if (!Cache::has('temperatures')) {
                $temperatures = $this->data24('temperature');
                Cache::put('temperatures', $temperatures, 600);
            } else {
                $temperatures =  Cache::get('temperatures');
            }
            if (!Cache::has('humidites')) {
                $humidites = $this->data24('humidite');
                Cache::put('humidites', $humidites, 600);
            } else {
                $humidites = Cache::get('humidites');
            }
            if (!Cache::has('vitesses')) {
                $vitesses = $this->data24('vitesse');
                Cache::put('vitesses', $vitesses, 600);
            } else {
                $vitesses = Cache::get('vitesses');
            }
            if (!Cache::has('pluies')) {
                $pluies = $this->data24('pluie');
                Cache::put('pluies', $pluies, 600);
            } else {
                $pluies = Cache::get('pluies');
            }
            if (!Cache::has('directions')) {
                $directions = $this->data24('direction');
                Cache::put('directions', $directions, 600);
            } else {
                $directions = Cache::get('directions');
            }
            if (!Cache::has('created')) {
                $created = $this->data24('created_at');
                Cache::put('created', $created, 600);
            } else {
                $created = Cache::get('created');
            }

            if (!Cache::has('month_days_temp_avg')) {
                $month_days_temp_avg  = $this->fc_month_days_avg('temperature');
                Cache::put($month_days_temp_avg, 600);
            }
            if (!Cache::has('month_days_humidite_avg')) {
                $month_days_humidite_avg = $this->fc_month_days_avg('humidite');
                Cache::put($month_days_humidite_avg, 600);
            }
            if (!Cache::has('month_days_vitesse_avg')) {
                $month_days_vitesse_avg = $this->fc_month_days_avg('vitesse');
                Cache::put($month_days_vitesse_avg, 600);
            }
            if (!Cache::has('month_days_pluie_avg')) {
                $month_days_pluie_avg = $this->fc_month_days_avg('pluie');
                Cache::put($month_days_pluie_avg, 600);
            }
            if (!Cache::has('month_days_direction_avg')) {
                $month_days_direction_avg = $this->fc_month_days_avg('direction');
                Cache::put($month_days_direction_avg, 600);
            }

            $days = Data_meteo::whereBetween('created_at', [Carbon::today()->subDays(30), Carbon::today()])->get()->pluck('created_at');
            foreach ($days as $unformatted_date) {
                $date = new \DateTime($unformatted_date);
                $day_no = $date->format('d/m/Y');
                $month_days[] = $day_no;
            }

            $month_days = array_unique($month_days);
            $dates = array_values($month_days);

            foreach ($dates as $dateString) {
                $month_days_array[] = DateTime::createFromFormat('d/m/Y', $dateString)->format("m-d");
            }
        }

        return view('historique.direction', compact('temperatures', 'humidites', 'vitesses', 'pluies', 'directions', 'created', 'month_days_temp_avg', 'month_days_humidite_avg', 'month_days_vitesse_avg', 'month_days_pluie_avg', 'month_days_direction_avg', 'month_days_array'));
    }

    public function chart_direction()
    {
        if (!Cache::has('data') && !Cache::has('labels')) {
            $pluies = Data_meteo::latest()->take(24)->get()->sortBy('id');
            $labels2 = $pluies->pluck('created_at');

            foreach ($labels2 as $date) {
                $labels[] =  Carbon::parse($date)->translatedFormat('D H\h');
            }

            $data = $pluies->pluck('direction');
            
            Cache::put('data', $data, 600);
            Cache::put('labels', $labels, 600);
        } else {
            $data = Cache::get('data');
            $labels = Cache::get('labels');
        }
        
        return response()->json(compact('labels', 'data'));
    }
    // End direction du vent -------------------------------------

    // test
    public function test()
    {
        $day_avg_array = array();
        $Temperature_dates = Data_meteo::orderBy('created_at', 'ASC')->pluck('created_at');

        for ($i = 0; $i < 30; $i++) {
            $day_avg_array[] = Data_meteo::whereDate('created_at', Carbon::today()->subDays($i))->avg('temperature');
        }

        return response()->json(compact('day_avg_array'));

    }
    //END test

    // getAllDays
    function getAllDays()
    {
        

    }
    //END getAllDays

}
