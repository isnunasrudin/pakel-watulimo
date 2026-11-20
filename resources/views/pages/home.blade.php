@extends('layouts.app')

@section('full_title', 'Website Resmi '.($villageSettings->village_name ?: 'Desa Pakel').' Kec. '.($villageSettings->district_name ?: 'Watulimo').' Kab. '.($villageSettings->regency_name ?: 'Trenggalek'))
@section('meta_description', $villageSettings->description ?: 'Website resmi Desa Pakel, Kecamatan Watulimo, Kabupaten Trenggalek. Informasi desa, berita, agenda, galeri, dan layanan masyarakat.')
@section('canonical', url('/'))
@section('og_image', $villageSettings->heroUrl() ?: ($albums->isNotEmpty() ? url($albums->first()->coverUrl()) : ''))

@section('content')
@include('partials.home.navbar')
@include('partials.home.hero')
@include('partials.home.announcement')
@include('partials.home.main-content')
@include('partials.home.finance')
@include('partials.home.statistics')
@include('partials.home.footer')
@endsection
