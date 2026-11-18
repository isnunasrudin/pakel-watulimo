@extends('layouts.app')

@section('title', $villageSettings->village_name.' | Website Resmi Pemerintah Desa')
@section('meta_description', $villageSettings->description ?: 'Website resmi Desa Pakel, Kecamatan Watulimo, Kabupaten Trenggalek. Informasi desa, berita, agenda, galeri, dan layanan masyarakat.')
@section('canonical', url('/'))
@section('og_title', $villageSettings->village_name.' | Website Resmi')
@section('og_image', $albums->isNotEmpty() ? url($albums->first()->coverUrl()) : asset('favicon.ico'))

@section('content')
@include('partials.home.navbar')
@include('partials.home.hero')
@include('partials.home.announcement')
@include('partials.home.main-content')
@include('partials.home.finance')
@include('partials.home.statistics')
@include('partials.home.footer')
@endsection
