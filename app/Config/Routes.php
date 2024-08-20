<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

$routes->get('/index', 'BiblioController::index');
$routes->get('/login', 'BiblioController::loginpage');
$routes->get('/cadastro', 'BiblioController::registerpage');

$routes->get('/logout', 'BiblioController::logout');
$routes->post('/cadastro', 'BiblioController::register');
$routes->post('/login', 'BiblioController::login');

$routes->get('/books', 'BiblioController::booksPage');
$routes->get('insertbooks', 'BiblioController::insertBooksPage');
$routes->post('insertbooks', 'BiblioController::insertBooks');
$routes->post('editbooks', 'BiblioController::editBooksPage');
$routes->post('updatebooks', 'BiblioController::updateBooks');
$routes->post('deletebooks', 'BiblioController::deleteBooks');

$routes->post('takebook', 'BiblioController::takeBook');
$routes->post('givebackbook', 'BiblioController::giveBackBook');
$routes->get('emprest', 'BiblioController::emprestimosPage');
$routes->get('insertemprest', 'BiblioController::insertEmprestPage');
$routes->post('insertemprest', 'BiblioController::insertEmprest');
$routes->post('editemprest', 'BiblioController::editEmprestPage');
$routes->post('updateemprest', 'BiblioController::updateEmprest');
$routes->post('deleteemprest', 'BiblioController::deleteEmprest');