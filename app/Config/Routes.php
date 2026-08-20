<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// AUTH
$routes->get('/', 'Auth::index');
$routes->post('login','Auth::login');
$routes->get('logout','Auth::logout');


// MASTER DATA
$routes->get('transaksi','Transaksi::index');
$routes->post('transaksi/upload','Transaksi::upload');
$routes->get('transaksi/reset', 'Transaksi::reset');

$routes->get('kuesioner','Kuesioner::index');
$routes->post('kuesioner/upload','Kuesioner::upload');
$routes->get('kuesioner/reset','Kuesioner::reset');

$routes->get('kategori-produk','KategoriProduk::index');
$routes->post('kategori-produk/simpan','KategoriProduk::simpan');
$routes->get('kategori-produk/hapus/(:num)','KategoriProduk::hapus/$1');


// ANALISIS DATA
$routes->get('analisis-data', 'AnalisisData::index');
$routes->get('analisis-data/proses', 'AnalisisData::proses');
$routes->get('analisis-data/reset', 'AnalisisData::reset');
$routes->get('analisis-data/detail', 'AnalisisData::detail');
$routes->get('analisis-data/history', 'AnalisisData::history');
$routes->get('analisis-data/generate', 'AnalisisData::generate');